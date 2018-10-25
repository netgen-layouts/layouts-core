<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Block\BlockDefinition\Integration;

use Netgen\BlockManager\Block\BlockDefinition;
use Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandlerInterface;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\Collection;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\ItemViewType;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\ViewType;
use Netgen\BlockManager\Block\BlockDefinitionInterface;
use Netgen\BlockManager\Block\Registry\BlockDefinitionRegistry;
use Netgen\BlockManager\Core\Validator\BlockValidator;
use Netgen\BlockManager\Core\Validator\CollectionValidator;
use Netgen\BlockManager\Core\Validator\LayoutValidator;
use Netgen\BlockManager\Exception\Validation\ValidationException;
use Netgen\BlockManager\Parameters\TranslatableParameterBuilderFactory;
use Netgen\BlockManager\Tests\Core\CoreTestCase;
use Netgen\BlockManager\Tests\TestCase\ValidatorFactory;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class BlockTest extends CoreTestCase
{
    /**
     * @var \Netgen\BlockManager\Core\Validator\BlockValidator
     */
    private $blockValidator;

    public function setUp(): void
    {
        parent::setUp();

        $validator = $this->getValidator();

        $collectionValidator = new CollectionValidator();
        $collectionValidator->setValidator($validator);

        $layoutValidator = new LayoutValidator();
        $layoutValidator->setValidator($validator);

        $this->layoutService = $this->createLayoutService($layoutValidator);

        $this->blockValidator = new BlockValidator($collectionValidator);
        $this->blockValidator->setValidator($validator);

        $this->blockService = $this->createBlockService($this->blockValidator);
    }

    /**
     * @dataProvider parametersDataProvider
     */
    public function testCreateBlock(array $parameters, array $expectedParameters): void
    {
        $blockDefinition = $this->createBlockDefinition(array_keys($expectedParameters));
        $blockCreateStruct = $this->blockService->newBlockCreateStruct($blockDefinition);
        $blockCreateStruct->viewType = 'default';
        $blockCreateStruct->itemViewType = 'standard';
        $blockCreateStruct->fillParametersFromHash($parameters);

        // We need to recreate the service due to recreating the block definition
        // registry in $this->createBlockDefinition() call
        $this->blockService = $this->createBlockService($this->blockValidator);

        $zone = $this->layoutService->loadZoneDraft(1, 'left');
        $createdBlock = $this->blockService->createBlockInZone($blockCreateStruct, $zone);

        $createdParameters = [];
        foreach ($createdBlock->getParameters() as $parameterName => $parameter) {
            $createdParameters[$parameterName] = $parameter->getValue();
        }

        self::assertEquals($expectedParameters, $createdParameters);
    }

    /**
     * @dataProvider invalidParametersDataProvider
     */
    public function testCreateBlockWithInvalidParameters(array $parameters, array $testedParams = []): void
    {
        $this->expectException(ValidationException::class);

        if (empty($parameters)) {
            throw ValidationException::validationFailed('parameters', 'Invalid parameters');
        }

        $blockDefinition = $this->createBlockDefinition(
            !empty($testedParams) ? $testedParams : array_keys($parameters)
        );

        // We need to recreate the service due to recreating the block definition
        // registry in $this->createBlockDefinition() call
        $this->blockService = $this->createBlockService($this->blockValidator);

        $blockCreateStruct = $this->blockService->newBlockCreateStruct($blockDefinition);
        $blockCreateStruct->viewType = 'default';
        $blockCreateStruct->itemViewType = 'standard';
        $blockCreateStruct->fillParametersFromHash($parameters);

        $zone = $this->layoutService->loadZoneDraft(1, 'left');
        $this->blockService->createBlockInZone($blockCreateStruct, $zone);
    }

    public function hasCollection(): bool
    {
        return false;
    }

    abstract public function parametersDataProvider(): array;

    abstract public function invalidParametersDataProvider(): array;

    abstract protected function createBlockDefinitionHandler(): BlockDefinitionHandlerInterface;

    protected function getValidator(): ValidatorInterface
    {
        return Validation::createValidatorBuilder()
            ->setConstraintValidatorFactory(new ValidatorFactory($this))
            ->getValidator();
    }

    private function createBlockDefinition(array $parameterNames = []): BlockDefinitionInterface
    {
        $handler = $this->createBlockDefinitionHandler();

        $builderFactory = new TranslatableParameterBuilderFactory($this->parameterTypeRegistry);
        $parameterBuilder = $builderFactory->createParameterBuilder();
        $handler->buildParameters($parameterBuilder);
        $parameterDefinitions = $parameterBuilder->buildParameterDefinitions();

        $filteredParameterDefinitions = [];
        if (!empty($parameterNames)) {
            foreach ($parameterDefinitions as $parameterName => $parameterDefinition) {
                if (in_array($parameterName, $parameterNames, true)) {
                    $filteredParameterDefinitions[$parameterName] = $parameterDefinition;
                }
            }
        }

        $collections = [];
        if ($this->hasCollection()) {
            $collections['default'] = Collection::fromArray(
                [
                    'identifier' => 'default',
                ]
            );
        }

        $blockDefinition = BlockDefinition::fromArray(
            [
                'identifier' => 'definition',
                'handler' => $handler,
                'viewTypes' => [
                    'default' => ViewType::fromArray(
                        [
                            'itemViewTypes' => [
                                'standard' => new ItemViewType(),
                            ],
                        ]
                    ),
                ],
                'collections' => $collections,
                'parameterDefinitions' => $filteredParameterDefinitions,
                'configDefinitions' => [],
            ]
        );

        $allBlockDefinitions = $this->blockDefinitionRegistry->getBlockDefinitions();
        $allBlockDefinitions['definition'] = $blockDefinition;

        $this->blockDefinitionRegistry = new BlockDefinitionRegistry($allBlockDefinitions);

        return $blockDefinition;
    }
}
