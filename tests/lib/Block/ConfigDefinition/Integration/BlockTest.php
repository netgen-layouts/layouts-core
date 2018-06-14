<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Block\ConfigDefinition\Integration;

use Netgen\BlockManager\API\Values\Config\Config;
use Netgen\BlockManager\API\Values\Config\ConfigStruct;
use Netgen\BlockManager\Block\BlockDefinition;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\ItemViewType;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\ViewType;
use Netgen\BlockManager\Block\BlockDefinitionInterface;
use Netgen\BlockManager\Config\ConfigDefinition;
use Netgen\BlockManager\Config\ConfigDefinitionHandlerInterface;
use Netgen\BlockManager\Config\ConfigDefinitionInterface;
use Netgen\BlockManager\Core\Service\Validator\BlockValidator;
use Netgen\BlockManager\Core\Service\Validator\CollectionValidator;
use Netgen\BlockManager\Core\Service\Validator\LayoutValidator;
use Netgen\BlockManager\Exception\Validation\ValidationException;
use Netgen\BlockManager\Parameters\ParameterBuilderFactory;
use Netgen\BlockManager\Tests\Block\Stubs\BlockDefinitionHandler;
use Netgen\BlockManager\Tests\Core\Service\ServiceTestCase;
use Netgen\BlockManager\Tests\TestCase\ValidatorFactory;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class BlockTest extends ServiceTestCase
{
    public function setUp(): void
    {
        parent::setUp();

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
     * @dataProvider configDataProvider
     */
    public function testCreateBlock(array $config, array $expectedConfig): void
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
     * @dataProvider invalidConfigDataProvider
     * @expectedException \Netgen\BlockManager\Exception\Validation\ValidationException
     */
    public function testCreateBlockWithInvalidConfig(array $config): void
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

    abstract public function createConfigDefinitionHandler(): ConfigDefinitionHandlerInterface;

    abstract public function configDataProvider(): array;

    abstract public function invalidConfigDataProvider(): array;

    public function getValidator(): ValidatorInterface
    {
        return Validation::createValidatorBuilder()
            ->setConstraintValidatorFactory(new ValidatorFactory($this))
            ->getValidator();
    }

    private function createBlockDefinition(ConfigDefinitionInterface $configDefinition): BlockDefinitionInterface
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

    private function createConfigDefinition(): ConfigDefinitionInterface
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
}
