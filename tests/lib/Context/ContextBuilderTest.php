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
        $this->contextBuilder = new ContextBuilder();

        $this->context = new Context();
    }

    /**
     * @covers \Netgen\BlockManager\Context\ContextBuilder::buildContext
     * @covers \Netgen\BlockManager\Context\ContextBuilder::registerProvider
     */
    public function testBuildContext(): void
    {
        $this->contextBuilder->registerProvider(new ContextProvider(['var1' => 'value1']));
        $this->contextBuilder->registerProvider(new ContextProvider(['var2' => 'value2']));

        $this->contextBuilder->buildContext($this->context);

        $this->assertEquals(
            [
                'var1' => 'value1',
                'var2' => 'value2',
            ],
            $this->context->all()
        );
    }
}
