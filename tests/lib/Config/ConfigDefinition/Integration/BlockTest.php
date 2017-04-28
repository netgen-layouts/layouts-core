<?php

namespace Netgen\BlockManager\Tests\Config\ConfigDefinition\Integration;

use Netgen\BlockManager\API\Values\Config\Config;
use Netgen\BlockManager\API\Values\Config\ConfigStruct;
use Netgen\BlockManager\Block\BlockDefinition;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\Configuration;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\ItemViewType;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\ViewType;
use Netgen\BlockManager\Block\BlockDefinition\Handler\TitleHandler;
use Netgen\BlockManager\Config\ConfigDefinition;
use Netgen\BlockManager\Core\Service\Validator\BlockValidator;
use Netgen\BlockManager\Core\Service\Validator\ConfigValidator;
use Netgen\BlockManager\Core\Service\Validator\LayoutValidator;
use Netgen\BlockManager\Exception\Validation\ValidationFailedException;
use Netgen\BlockManager\Parameters\ParameterBuilder;
use Netgen\BlockManager\Parameters\ParameterType;
use Netgen\BlockManager\Tests\Core\Service\ServiceTestCase;
use Netgen\BlockManager\Tests\TestCase\ValidatorFactory;
use Symfony\Component\Validator\Validation;

abstract class BlockTest extends ServiceTestCase
{
    /**
     * @var \Netgen\BlockManager\API\Service\BlockService
     */
    protected $blockService;

    /**
     * @var \Netgen\BlockManager\API\Service\LayoutService
     */
    protected $layoutService;

    public function setUp()
    {
        parent::setUp();

        $this->prepareParameterTypeRegistry();

        $validator = $this->getValidator();

        $configValidator = new ConfigValidator($this->configDefinitionRegistry);
        $configValidator->setValidator($validator);

        $blockValidator = new BlockValidator($configValidator);
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
        $blockDefinition = $this->createBlockDefinition();
        $blockCreateStruct = $this->blockService->newBlockCreateStruct($blockDefinition, 'en');
        $blockCreateStruct->viewType = 'default';
        $blockCreateStruct->itemViewType = 'standard';

        $configStruct = new ConfigStruct();
        $configDefinition = $this->createConfigDefinition(array_keys($expectedConfig));

        $configStruct->fillValues($configDefinition, $config);
        $blockCreateStruct->setConfigStruct('definition', $configStruct);

        $layout = $this->layoutService->loadLayoutDraft(1);
        $createdBlock = $this->blockService->createBlockInZone($blockCreateStruct, $layout, 'left');

        $this->assertTrue($createdBlock->hasConfig('definition'));

        $createdConfig = $createdBlock->getConfig('definition');

        $this->assertInstanceOf(Config::class, $createdConfig);

        $createdParameters = array();
        foreach ($createdConfig->getParameters() as $parameterName => $parameterValue) {
            $createdParameters[$parameterName] = $parameterValue->getValue();
        }

        $this->assertEquals($expectedConfig, $createdParameters);
    }

    /**
     * @param array $config
     * @param array $testedParams
     * @dataProvider invalidConfigDataProvider
     * @expectedException \Netgen\BlockManager\Exception\Validation\ValidationFailedException
     */
    public function testCreateBlockWithInvalidConfig(array $config, array $testedParams = null)
    {
        if (empty($config)) {
            throw new ValidationFailedException('config', 'Invalid config');
        }

        $blockDefinition = $this->createBlockDefinition();
        $blockCreateStruct = $this->blockService->newBlockCreateStruct($blockDefinition, 'en');
        $blockCreateStruct->viewType = 'default';
        $blockCreateStruct->itemViewType = 'standard';

        $configStruct = new ConfigStruct();
        $configDefinition = $this->createConfigDefinition(
            $testedParams !== null ? $testedParams : array_keys($config)
        );

        $configStruct->fillValues($configDefinition, $config);
        $blockCreateStruct->setConfigStruct('definition', $configStruct);

        $layout = $this->layoutService->loadLayoutDraft(1);
        $this->blockService->createBlockInZone($blockCreateStruct, $layout, 'left');
    }

    /**
     * @return \Netgen\BlockManager\Config\ConfigDefinition\ConfigDefinitionHandlerInterface
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
        return array();
    }

    public function getValidators()
    {
        return array();
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
     * @return \Netgen\BlockManager\Block\BlockDefinitionInterface
     */
    protected function createBlockDefinition()
    {
        $handler = new TitleHandler();
        $configuration = $this->createBlockConfiguration();

        $blockDefinition = new BlockDefinition(
            array(
                'identifier' => 'definition',
                'handler' => $handler,
                'config' => $configuration,
                'parameters' => array(),
            )
        );

        $this->blockDefinitionRegistry->addBlockDefinition('definition', $blockDefinition);

        return $blockDefinition;
    }

    /**
     * @param array $parameterNames
     *
     * @return \Netgen\BlockManager\Config\ConfigDefinitionInterface
     */
    protected function createConfigDefinition(array $parameterNames = array())
    {
        $handler = $this->createConfigDefinitionHandler();

        $parameterBuilder = new ParameterBuilder($this->parameterTypeRegistry);
        $handler->buildParameters($parameterBuilder);
        $config = $parameterBuilder->buildParameters();

        $filteredParameters = array();
        if (!empty($parameterNames)) {
            foreach ($config as $parameterName => $parameter) {
                if (in_array($parameterName, $parameterNames, true)) {
                    $filteredParameters[$parameterName] = $parameter;
                }
            }
        }

        $configDefinition = new ConfigDefinition(
            array(
                'type' => 'block',
                'identifier' => 'definition',
                'handler' => $handler,
                'parameters' => $filteredParameters,
            )
        );

        $this->configDefinitionRegistry->addConfigDefinition('block', 'definition', $configDefinition);

        return $configDefinition;
    }

    /**
     * @return \Netgen\BlockManager\Block\BlockDefinition\Configuration\Configuration
     */
    protected function createBlockConfiguration()
    {
        return new Configuration(
            array(
                'viewTypes' => array(
                    'default' => new ViewType(
                        array(
                            'itemViewTypes' => array(
                                'standard' => new ItemViewType(),
                            ),
                        )
                    ),
                ),
            )
        );
    }

    protected function prepareParameterTypeRegistry()
    {
        $this->parameterTypeRegistry->addParameterType(new ParameterType\TextLineType());
        $this->parameterTypeRegistry->addParameterType(new ParameterType\TextType());
        $this->parameterTypeRegistry->addParameterType(new ParameterType\UrlType());
        $this->parameterTypeRegistry->addParameterType(new ParameterType\RangeType());
        $this->parameterTypeRegistry->addParameterType(new ParameterType\NumberType());
        $this->parameterTypeRegistry->addParameterType(new ParameterType\LinkType());
        $this->parameterTypeRegistry->addParameterType(new ParameterType\ItemLinkType());
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
