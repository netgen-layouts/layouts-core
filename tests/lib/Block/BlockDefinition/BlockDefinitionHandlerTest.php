<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Block\BlockDefinition;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\Block\BlockDefinition\BlockDefinitionHandler;
use Netgen\Layouts\Block\DynamicParameters;
use Netgen\Layouts\Parameters\ParameterType;
use Netgen\Layouts\Parameters\Registry\ParameterTypeRegistry;
use Netgen\Layouts\Parameters\TranslatableParameterBuilderFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class BlockDefinitionHandlerTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject&\Netgen\Layouts\Block\BlockDefinition\BlockDefinitionHandler
     */
    private MockObject $handler;

    private ParameterTypeRegistry $parameterTypeRegistry;

    private TranslatableParameterBuilderFactory $parameterBuilderFactory;

    protected function setUp(): void
    {
        $this->handler = $this->getMockForAbstractClass(BlockDefinitionHandler::class);

        $this->parameterTypeRegistry = new ParameterTypeRegistry(
            [
                new ParameterType\TextLineType(),
                new ParameterType\BooleanType(),
            ],
        );

        $this->parameterBuilderFactory = new TranslatableParameterBuilderFactory(
            $this->parameterTypeRegistry,
        );
    }

    /**
     * @covers \Netgen\Layouts\Block\BlockDefinition\BlockDefinitionHandler::buildParameters
     */
    public function testBuildParameters(): void
    {
        $builder = $this->parameterBuilderFactory->createParameterBuilder();
        $this->handler->buildParameters($builder);

        self::assertCount(0, $builder);
    }

    /**
     * @covers \Netgen\Layouts\Block\BlockDefinition\BlockDefinitionHandler::getDynamicParameters
     */
    public function testGetDynamicParameters(): void
    {
        $dynamicParameters = new DynamicParameters();
        $this->handler->getDynamicParameters($dynamicParameters, new Block());

        self::assertCount(0, $dynamicParameters);
    }

    /**
     * @covers \Netgen\Layouts\Block\BlockDefinition\BlockDefinitionHandler::isContextual
     */
    public function testIsContextual(): void
    {
        self::assertFalse($this->handler->isContextual(new Block()));
    }
}
