<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Context;

use Netgen\Layouts\Context\Context;
use Netgen\Layouts\Context\ContextBuilder;
use Netgen\Layouts\Tests\Context\Stubs\ContextProvider;
use PHPUnit\Framework\TestCase;

final class ContextBuilderTest extends TestCase
{
    private Context $context;

    private ContextBuilder $contextBuilder;

    protected function setUp(): void
    {
        $this->context = new Context();
    }

    /**
     * @covers \Netgen\Layouts\Context\ContextBuilder::__construct
     * @covers \Netgen\Layouts\Context\ContextBuilder::buildContext
     */
    public function testBuildContext(): void
    {
        $this->contextBuilder = new ContextBuilder(
            [
                new ContextProvider(['var1' => 'value1']),
                new ContextProvider(['var2' => 'value2']),
            ],
        );

        $this->contextBuilder->buildContext($this->context);

        self::assertSame(
            [
                'var2' => 'value2',
                'var1' => 'value1',
            ],
            $this->context->all(),
        );
    }
}
