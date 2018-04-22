<?php

namespace Netgen\BlockManager\Tests\Block\ConfigDefinition\Integration;

use Netgen\BlockManager\API\Values\Config\Config;
use Netgen\BlockManager\API\Values\Config\ConfigStruct;
use Netgen\BlockManager\Block\BlockDefinition;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\ItemViewType;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\ViewType;
use Netgen\BlockManager\Config\ConfigDefinition;
use Netgen\BlockManager\Config\ConfigDefinitionInterface;
use Netgen\BlockManager\Core\Service\Validator\BlockValidator;
use Netgen\BlockManager\Core\Service\Validator\CollectionValidator;
use Netgen\BlockManager\Core\Service\Validator\LayoutValidator;
use Netgen\BlockManager\Exception\Validation\ValidationException;
use Netgen\BlockManager\Item\ItemLoaderInterface;
use Netgen\BlockManager\Item\Registry\ValueTypeRegistry;
use Netgen\BlockManager\Parameters\ParameterBuilderFactory;
use Netgen\BlockManager\Parameters\ParameterType;
use Netgen\BlockManager\Parameters\ParameterType\ItemLink\RemoteIdConverter;
use Netgen\BlockManager\Tests\Block\Stubs\BlockDefinitionHandler;
use Netgen\BlockManager\Tests\Core\Service\ServiceTestCase;
use Netgen\BlockManager\Tests\TestCase\ValidatorFactory;
use Symfony\Component\Validator\Validation;

abstract class BlockTest extends ServiceTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->prepareParameterTypeRegistry();

        $validator = $this->getValidator();

        $collectionValidator = new CollectionValidator();
        $collectionValidator->setValidator($validator);

        $blockValidator = new BlockValidator($collectionValidator);
        $blockValidator->setValidator($validator);

        $layoutValidator = new LayoutValidator();
        $layoutValidator->setValidator($validator);

        $this->blockService = $this->createBlockService($blockValidator);
        $this->layoutService = $this->createLayoutService($layoutValidator);
    }

    /**
     * @param array $config
     * @param array $expectedConfig
     * @dataProvider configDataProvider
     */
    public function testCreateBlock(array $config, array $expectedConfig)
    {
        $configDefinition = $this->createConfigDefinition();

        $blockDefinition = $this->createBlockDefinition($configDefinition);
        $blockCreateStruct = $this->blockService->newBlockCreateStruct($blockDefinition);
        $blockCreateStruct->viewType = 'default';
        $blockCreateStruct->itemViewType = 'standard';

        $configStruct = new ConfigStruct();
        $configStruct->fill($configDefinition, $config);
        $blockCreateStruct->setConfigStruct('definition', $configStruct);

        $zone = $this->layoutService->loadZoneDraft(1, 'left');
        $createdBlock = $this->blockService->createBlockInZone($blockCreateStruct, $zone);

        $this->assertTrue($createdBlock->hasConfig('definition'));

        $createdConfig = $createdBlock->getConfig('definition');

        $this->assertInstanceOf(Config::class, $createdConfig);

        $createdParameters = [];
        foreach ($createdConfig->getParameters() as $parameterName => $parameter) {
            $createdParameters[$parameterName] = $parameter->getValue();
        }

        $this->assertEquals($expectedConfig, $createdParameters);
    }

    /**
     * @param array $config
     * @dataProvider invalidConfigDataProvider
     * @expectedException \Netgen\BlockManager\Exception\Validation\ValidationException
     */
    public function testCreateBlockWithInvalidConfig(array $config)
    {
        if (empty($config)) {
            throw ValidationException::validationFailed('config', 'Invalid config');
        }

        $configDefinition = $this->createConfigDefinition();

        $blockDefinition = $this->createBlockDefinition($configDefinition);
        $blockCreateStruct = $this->blockService->newBlockCreateStruct($blockDefinition);
        $blockCreateStruct->viewType = 'default';
        $blockCreateStruct->itemViewType = 'standard';

        $configStruct = new ConfigStruct();
        $configStruct->fill($configDefinition, $config);
        $blockCreateStruct->setConfigStruct('definition', $configStruct);

        $zone = $this->layoutService->loadZoneDraft(1, 'left');
        $this->blockService->createBlockInZone($blockCreateStruct, $zone);
    }

    /**
     * @return \Netgen\BlockManager\Config\ConfigDefinitionHandlerInterface
     */
    abstract public function createConfigDefinitionHandler();

    /**
     * @return array
     */
    abstract public function configDataProvider();

    /**
     * @return array
     */
    abstract public function invalidConfigDataProvider();

    /**
     * @return \Netgen\BlockManager\Parameters\ParameterTypeInterface[]
     */
    public function getParameterTypes()
    {
        return [];
    }

    public function getValidators()
    {
        return [];
    }

    /**
     * @return \Symfony\Component\Validator\Validator\ValidatorInterface
     */
    public function getValidator()
    {
        return Validation::createValidatorBuilder()
            ->setConstraintValidatorFactory(new ValidatorFactory($this))
            ->getValidator();
    }

    /**
     * @param \Netgen\BlockManager\Config\ConfigDefinitionInterface $configDefinition
     *
     * @return \Netgen\BlockManager\Block\BlockDefinitionInterface
     */
    private function createBlockDefinition(ConfigDefinitionInterface $configDefinition)
    {
        $handler = new BlockDefinitionHandler();

        $blockDefinition = new BlockDefinition(
            [
                'identifier' => 'definition',
                'handler' => $handler,
                'viewTypes' => [
                    'default' => new ViewType(
                        [
                            'itemViewTypes' => [
                                'standard' => new ItemViewType(),
                            ],
                        ]
                    ),
                ],
                'parameterDefinitions' => [],
                'configDefinitions' => ['definition' => $configDefinition],
            ]
        );

        $this->blockDefinitionRegistry->addBlockDefinition('definition', $blockDefinition);

        return $blockDefinition;
    }

    /**
     * @return \Netgen\BlockManager\Config\ConfigDefinitionInterface
     */
    private function createConfigDefinition()
    {
        $handler = $this->createConfigDefinitionHandler();

        $builderFactory = new ParameterBuilderFactory($this->parameterTypeRegistry);
        $parameterBuilder = $builderFactory->createParameterBuilder();
        $handler->buildParameters($parameterBuilder);

        return new ConfigDefinition(
            [
                'configKey' => 'definition',
                'handler' => $handler,
                'parameterDefinitions' => $parameterBuilder->buildParameterDefinitions(),
            ]
        );
    }

    private function prepareParameterTypeRegistry()
    {
        $remoteIdConverter = new RemoteIdConverter($this->createMock(ItemLoaderInterface::class));

        $this->parameterTypeRegistry->addParameterType(new ParameterType\TextLineType());
        $this->parameterTypeRegistry->addParameterType(new ParameterType\TextType());
        $this->parameterTypeRegistry->addParameterType(new ParameterType\UrlType());
        $this->parameterTypeRegistry->addParameterType(new ParameterType\RangeType());
        $this->parameterTypeRegistry->addParameterType(new ParameterType\NumberType());
        $this->parameterTypeRegistry->addParameterType(new ParameterType\LinkType(new ValueTypeRegistry(), $remoteIdConverter));
        $this->parameterTypeRegistry->addParameterType(new ParameterType\ItemLinkType(new ValueTypeRegistry(), $remoteIdConverter));
        $this->parameterTypeRegistry->addParameterType(new ParameterType\IntegerType());
        $this->parameterTypeRegistry->addParameterType(new ParameterType\IdentifierType());
        $this->parameterTypeRegistry->addParameterType(new ParameterType\HtmlType());
        $this->parameterTypeRegistry->addParameterType(new ParameterType\EmailType());
        $this->parameterTypeRegistry->addParameterType(new ParameterType\ChoiceType());
        $this->parameterTypeRegistry->addParameterType(new ParameterType\BooleanType());
        $this->parameterTypeRegistry->addParameterType(new ParameterType\Compound\BooleanType());

        foreach ($this->getParameterTypes() as $parameterType) {
            $this->parameterTypeRegistry->addParameterType($parameterType);
        }
    }
}
