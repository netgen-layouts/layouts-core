<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Context;

use Netgen\BlockManager\Context\Context;
use Netgen\BlockManager\Context\ContextBuilder;
use Netgen\BlockManager\Tests\Context\Stubs\ContextProvider;
use PHPUnit\Framework\TestCase;

final class ContextBuilderTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Context\ContextInterface
     */
    private $context;

    /**
     * @var \Netgen\BlockManager\Context\ContextBuilder
     */
    private $contextBuilder;

    public function setUp(): void
    {
        $this->context = new Context();
    }

    /**
     * @covers \Netgen\BlockManager\Context\ContextBuilder::buildContext
     */
    public function testBuildContext(): void
    {
        $this->contextBuilder = new ContextBuilder(
            [
                new ContextProvider(['var1' => 'value1']),
                new ContextProvider(['var2' => 'value2']),
            ]
        );

        $this->contextBuilder->buildContext($this->context);

        self::assertSame(
            [
                'var2' => 'value2',
                'var1' => 'value1',
            ],
            $this->context->all()
        );
    }
}
