<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Block\BlockDefinition;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\Block\BlockDefinition\BlockDefinitionHandler;
use Netgen\Layouts\Block\DynamicParameters;
use Netgen\Layouts\Parameters\ParameterType;
use Netgen\Layouts\Parameters\Registry\ParameterTypeRegistry;
use Netgen\Layouts\Parameters\TranslatableParameterBuilderFactory;
use Netgen\Layouts\Tests\Block\Stubs\EmptyBlockDefinitionHandler;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(BlockDefinitionHandler::class)]
final class BlockDefinitionHandlerTest extends TestCase
{
    private EmptyBlockDefinitionHandler $handler;

    private ParameterTypeRegistry $parameterTypeRegistry;

    private TranslatableParameterBuilderFactory $parameterBuilderFactory;

    protected function setUp(): void
    {
        $this->handler = new EmptyBlockDefinitionHandler();

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

    public function testBuildParameters(): void
    {
        $builder = $this->parameterBuilderFactory->createParameterBuilder();
        $this->handler->buildParameters($builder);

        self::assertCount(0, $builder);
    }

    public function testGetDynamicParameters(): void
    {
        $dynamicParameters = new DynamicParameters();
        $this->handler->getDynamicParameters($dynamicParameters, new Block());

        self::assertCount(0, $dynamicParameters);
    }

    public function testIsContextual(): void
    {
        self::assertFalse($this->handler->isContextual(new Block()));
    }
}
