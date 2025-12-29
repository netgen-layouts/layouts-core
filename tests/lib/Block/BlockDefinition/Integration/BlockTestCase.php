<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Block\BlockDefinition\Integration;

use Netgen\Layouts\Block\BlockDefinition;
use Netgen\Layouts\Block\BlockDefinition\BlockDefinitionHandlerInterface;
use Netgen\Layouts\Block\BlockDefinitionInterface;
use Netgen\Layouts\Exception\Validation\ValidationException;
use Netgen\Layouts\Parameters\Parameter;
use Netgen\Layouts\Parameters\ParameterBuilderFactory;
use Netgen\Layouts\Tests\Core\CoreTestCase;
use Netgen\Layouts\Tests\Core\Stubs\ConfigProvider;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\Uid\Uuid;

use function array_keys;
use function array_map;
use function count;
use function in_array;

abstract class BlockTestCase extends CoreTestCase
{
    /**
     * @param array<string, mixed> $parameters
     * @param array<string, mixed> $expectedParameters
     */
    #[DataProvider('parametersDataProvider')]
    final public function testCreateBlock(array $parameters, array $expectedParameters): void
    {
        $blockDefinition = $this->createBlockDefinition(array_keys($expectedParameters));

        $blockCreateStruct = $this->blockService->newBlockCreateStruct($blockDefinition);
        $blockCreateStruct->viewType = 'default';
        $blockCreateStruct->itemViewType = 'standard';
        $blockCreateStruct->fillParametersFromHash($parameters);

        $zone = $this->layoutService->loadLayoutDraft(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'))->getZone('left');
        $createdBlock = $this->blockService->createBlockInZone($blockCreateStruct, $zone);

        $createdParameters = array_map(
            static fn (Parameter $parameter): mixed => $parameter->value,
            $createdBlock->parameters->toArray(),
        );

        self::assertSame(array_keys($expectedParameters), array_keys($createdParameters));

        foreach ($expectedParameters as $expectedParameter) {
            self::assertContainsEquals($expectedParameter, $createdParameters);
        }
    }

    /**
     * @param array<string, mixed> $parameters
     * @param string[] $testedParams
     */
    #[DataProvider('invalidParametersDataProvider')]
    final public function testCreateBlockWithInvalidParameters(array $parameters, array $testedParams = []): void
    {
        $this->expectException(ValidationException::class);

        if (count($parameters) === 0) {
            throw ValidationException::validationFailed('parameters', 'Invalid parameters');
        }

        $blockDefinition = $this->createBlockDefinition(
            count($testedParams) > 0 ? $testedParams : array_keys($parameters),
        );

        $blockCreateStruct = $this->blockService->newBlockCreateStruct($blockDefinition);
        $blockCreateStruct->viewType = 'default';
        $blockCreateStruct->itemViewType = 'standard';
        $blockCreateStruct->fillParametersFromHash($parameters);

        $zone = $this->layoutService->loadLayoutDraft(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'))->getZone('left');
        $this->blockService->createBlockInZone($blockCreateStruct, $zone);
    }

    /**
     * @return iterable<mixed>
     */
    abstract public static function parametersDataProvider(): iterable;

    /**
     * @return iterable<mixed>
     */
    abstract public static function invalidParametersDataProvider(): iterable;

    abstract protected function createBlockDefinitionHandler(): BlockDefinitionHandlerInterface;

    /**
     * @param string[] $parameterNames
     */
    private function createBlockDefinition(array $parameterNames = []): BlockDefinitionInterface
    {
        $handler = $this->createBlockDefinitionHandler();

        $builderFactory = new ParameterBuilderFactory($this->parameterTypeRegistry);
        $parameterBuilder = $builderFactory->createParameterBuilder();
        $handler->buildParameters($parameterBuilder);
        $parameterDefinitions = $parameterBuilder->buildParameterDefinitions();

        $filteredParameterDefinitions = [];
        if (count($parameterNames) > 0) {
            foreach ($parameterDefinitions as $parameterName => $parameterDefinition) {
                if (in_array($parameterName, $parameterNames, true)) {
                    $filteredParameterDefinitions[$parameterName] = $parameterDefinition;
                }
            }
        }

        $blockDefinition = BlockDefinition::fromArray(
            [
                'identifier' => 'definition',
                'handler' => $handler,
                'configProvider' => ConfigProvider::fromShortConfig(['default' => ['standard']]),
                'isTranslatable' => false,
                'collections' => [],
                'parameterDefinitions' => $filteredParameterDefinitions,
                'configDefinitions' => [],
            ],
        );

        (function () use ($blockDefinition): void {
            $this->blockDefinitions['definition'] = $blockDefinition;
        })->call($this->blockDefinitionRegistry);

        return $blockDefinition;
    }
}
