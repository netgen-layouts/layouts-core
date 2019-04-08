<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Block\BlockDefinition;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandler;
use Netgen\BlockManager\Block\DynamicParameters;
use Netgen\BlockManager\Parameters\ParameterType;
use Netgen\BlockManager\Parameters\Registry\ParameterTypeRegistry;
use Netgen\BlockManager\Parameters\TranslatableParameterBuilderFactory;
use PHPUnit\Framework\TestCase;

final class BlockDefinitionHandlerTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandler
     */
    private $handler;

    /**
     * @var \Netgen\BlockManager\Parameters\Registry\ParameterTypeRegistryInterface
     */
    private $parameterTypeRegistry;

    /**
     * @var \Netgen\BlockManager\Parameters\ParameterBuilderFactoryInterface
     */
    private $parameterBuilderFactory;

    public function setUp(): void
    {
        $this->handler = $this->getMockForAbstractClass(BlockDefinitionHandler::class);

        $this->parameterTypeRegistry = new ParameterTypeRegistry(
            [
                new ParameterType\TextLineType(),
                new ParameterType\BooleanType(),
            ]
        );

        $this->parameterBuilderFactory = new TranslatableParameterBuilderFactory(
            $this->parameterTypeRegistry
        );
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandler::buildParameters
     */
    public function testBuildParameters(): void
    {
        $builder = $this->parameterBuilderFactory->createParameterBuilder();
        $this->handler->buildParameters($builder);

        self::assertCount(0, $builder);
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandler::getDynamicParameters
     */
    public function testGetDynamicParameters(): void
    {
        $dynamicParameters = new DynamicParameters();
        $this->handler->getDynamicParameters($dynamicParameters, new Block());

        self::assertCount(0, $dynamicParameters);
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandler::isContextual
     */
    public function testIsContextual(): void
    {
        self::assertFalse($this->handler->isContextual(new Block()));
    }
}
