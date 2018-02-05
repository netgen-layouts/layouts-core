<?php

namespace Netgen\BlockManager\Tests\Collection\Item\ConfigDefinition\Integration;

use Netgen\BlockManager\API\Values\Collection\Item;
use Netgen\BlockManager\API\Values\Config\Config;
use Netgen\BlockManager\API\Values\Config\ConfigStruct;
use Netgen\BlockManager\Collection\Item\ItemDefinition;
use Netgen\BlockManager\Config\ConfigDefinition;
use Netgen\BlockManager\Config\ConfigDefinitionInterface;
use Netgen\BlockManager\Core\Service\Validator\CollectionValidator;
use Netgen\BlockManager\Core\Service\Validator\ConfigValidator;
use Netgen\BlockManager\Exception\Validation\ValidationException;
use Netgen\BlockManager\Parameters\ParameterBuilderFactory;
use Netgen\BlockManager\Parameters\ParameterType;
use Netgen\BlockManager\Tests\Core\Service\ServiceTestCase;
use Netgen\BlockManager\Tests\TestCase\ValidatorFactory;
use Symfony\Component\Validator\Validation;

abstract class ItemTest extends ServiceTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->prepareParameterTypeRegistry();

        $validator = $this->getValidator();

        $configValidator = new ConfigValidator();
        $configValidator->setValidator($validator);

        $collectionValidator = new CollectionValidator($configValidator);
        $collectionValidator->setValidator($validator);

        $this->collectionService = $this->createCollectionService($collectionValidator);
    }

    /**
     * @param array $config
     * @param array $expectedConfig
     * @dataProvider configDataProvider
     */
    public function testCreateItem(array $config, array $expectedConfig)
    {
        $configDefinition = $this->createConfigDefinition(array_keys($expectedConfig));

        $itemDefinition = $this->createItemDefinition($configDefinition);
        $itemCreateStruct = $this->collectionService->newItemCreateStruct($itemDefinition, Item::TYPE_MANUAL, 42);

        $configStruct = new ConfigStruct();
        $configStruct->fill($configDefinition, $config);
        $itemCreateStruct->setConfigStruct('visibility', $configStruct);

        $collection = $this->collectionService->loadCollectionDraft(1);
        $createdItem = $this->collectionService->addItem($collection, $itemCreateStruct);

        $this->assertTrue($createdItem->hasConfig('visibility'));

        $createdConfig = $createdItem->getConfig('visibility');

        $this->assertInstanceOf(Config::class, $createdConfig);

        $createdParameters = array();
        foreach ($createdConfig->getParameters() as $parameterName => $parameter) {
            $createdParameters[$parameterName] = $parameter->getValue();
        }

        $this->assertEquals($expectedConfig, $createdParameters);
    }

    /**
     * @param array $config
     * @param array $testedParams
     * @dataProvider invalidConfigDataProvider
     * @expectedException \Netgen\BlockManager\Exception\Validation\ValidationException
     */
    public function testCreateItemWithInvalidConfig(array $config, array $testedParams = null)
    {
        if (empty($config)) {
            throw ValidationException::validationFailed('config', 'Invalid config');
        }

        $configDefinition = $this->createConfigDefinition(
            $testedParams !== null ? $testedParams : array_keys($config)
        );

        $itemDefinition = $this->createItemDefinition($configDefinition);
        $itemCreateStruct = $this->collectionService->newItemCreateStruct($itemDefinition, Item::TYPE_MANUAL, 42);

        $configStruct = new ConfigStruct();
        $configStruct->fill($configDefinition, $config);
        $itemCreateStruct->setConfigStruct('visibility', $configStruct);

        $collection = $this->collectionService->loadCollectionDraft(1);
        $this->collectionService->addItem($collection, $itemCreateStruct);
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
     * @param \Netgen\BlockManager\Config\ConfigDefinitionInterface $configDefinition
     *
     * @return \Netgen\BlockManager\Collection\Item\ItemDefinitionInterface
     */
    private function createItemDefinition(ConfigDefinitionInterface $configDefinition)
    {
        $itemDefinition = new ItemDefinition(
            array(
                'valueType' => 'ezlocation',
                'configDefinitions' => array('visibility' => $configDefinition),
            )
        );

        $this->itemDefinitionRegistry->addItemDefinition('visibility', $itemDefinition);

        return $itemDefinition;
    }

    /**
     * @param array $parameterNames
     *
     * @return \Netgen\BlockManager\Config\ConfigDefinitionInterface
     */
    private function createConfigDefinition(array $parameterNames = array())
    {
        $handler = $this->createConfigDefinitionHandler();

        $builderFactory = new ParameterBuilderFactory($this->parameterTypeRegistry);
        $parameterBuilder = $builderFactory->createParameterBuilder();
        $handler->buildParameters($parameterBuilder);
        $config = $parameterBuilder->buildParameterDefinitions();

        $filteredParameterDefinitions = array();
        if (!empty($parameterNames)) {
            foreach ($config as $parameterName => $parameterDefinition) {
                if (in_array($parameterName, $parameterNames, true)) {
                    $filteredParameterDefinitions[$parameterName] = $parameterDefinition;
                }
            }
        }

        return new ConfigDefinition(
            array(
                'configKey' => 'visibility',
                'handler' => $handler,
                'parameterDefinitions' => $filteredParameterDefinitions,
            )
        );
    }

    private function prepareParameterTypeRegistry()
    {
        $this->parameterTypeRegistry->addParameterType(new ParameterType\DateTimeType());
        $this->parameterTypeRegistry->addParameterType(new ParameterType\Compound\BooleanType());

        foreach ($this->getParameterTypes() as $parameterType) {
            $this->parameterTypeRegistry->addParameterType($parameterType);
        }
    }
}
