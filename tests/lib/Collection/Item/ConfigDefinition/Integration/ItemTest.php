<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Collection\Item\ConfigDefinition\Integration;

use Netgen\BlockManager\API\Values\Collection\Item;
use Netgen\BlockManager\API\Values\Config\Config;
use Netgen\BlockManager\API\Values\Config\ConfigStruct;
use Netgen\BlockManager\Collection\Item\ItemDefinition;
use Netgen\BlockManager\Collection\Item\ItemDefinitionInterface;
use Netgen\BlockManager\Config\ConfigDefinition;
use Netgen\BlockManager\Config\ConfigDefinitionHandlerInterface;
use Netgen\BlockManager\Config\ConfigDefinitionInterface;
use Netgen\BlockManager\Core\Service\Validator\CollectionValidator;
use Netgen\BlockManager\Exception\Validation\ValidationException;
use Netgen\BlockManager\Parameters\ParameterBuilderFactory;
use Netgen\BlockManager\Tests\Core\Service\ServiceTestCase;
use Netgen\BlockManager\Tests\TestCase\ValidatorFactory;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class ItemTest extends ServiceTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $validator = $this->getValidator();

        $collectionValidator = new CollectionValidator();
        $collectionValidator->setValidator($validator);

        $this->collectionService = $this->createCollectionService($collectionValidator);
    }

    /**
     * @dataProvider configDataProvider
     */
    public function testCreateItem(array $config, array $expectedConfig): void
    {
        $configDefinition = $this->createConfigDefinition();

        $itemDefinition = $this->createItemDefinition($configDefinition);
        $itemCreateStruct = $this->collectionService->newItemCreateStruct($itemDefinition, Item::TYPE_MANUAL, 42);

        $configStruct = new ConfigStruct();
        $configStruct->fill($configDefinition, $config);
        $itemCreateStruct->setConfigStruct('definition', $configStruct);

        $collection = $this->collectionService->loadCollectionDraft(1);
        $createdItem = $this->collectionService->addItem($collection, $itemCreateStruct);

        $this->assertTrue($createdItem->hasConfig('definition'));

        $createdConfig = $createdItem->getConfig('definition');

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
    public function testCreateItemWithInvalidConfig(array $config): void
    {
        if (empty($config)) {
            throw ValidationException::validationFailed('config', 'Invalid config');
        }

        $configDefinition = $this->createConfigDefinition();

        $itemDefinition = $this->createItemDefinition($configDefinition);
        $itemCreateStruct = $this->collectionService->newItemCreateStruct($itemDefinition, Item::TYPE_MANUAL, 42);

        $configStruct = new ConfigStruct();
        $configStruct->fill($configDefinition, $config);
        $itemCreateStruct->setConfigStruct('definition', $configStruct);

        $collection = $this->collectionService->loadCollectionDraft(1);
        $this->collectionService->addItem($collection, $itemCreateStruct);
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

    private function createItemDefinition(ConfigDefinitionInterface $configDefinition): ItemDefinitionInterface
    {
        $itemDefinition = new ItemDefinition(
            [
                'valueType' => 'my_value_type',
                'configDefinitions' => ['definition' => $configDefinition],
            ]
        );

        $this->itemDefinitionRegistry->addItemDefinition('my_value_type', $itemDefinition);

        return $itemDefinition;
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
