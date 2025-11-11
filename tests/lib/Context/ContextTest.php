<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Context;

use ArrayIterator;
use Netgen\Layouts\Context\Context;
use Netgen\Layouts\Exception\Context\ContextException;
use Netgen\Layouts\Exception\RuntimeException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Context::class)]
final class ContextTest extends TestCase
{
    private Context $context;

    protected function setUp(): void
    {
        $this->context = new Context();
        $this->context->set('var', 'value');
    }

    public function testSet(): void
    {
        $this->context->set('var2', 'value2');

        self::assertTrue($this->context->has('var2'));
        self::assertSame('value2', $this->context->get('var2'));
    }

    public function testAdd(): void
    {
        $this->context->add(['var2' => 'value2', 'var3' => 'value3']);

        self::assertTrue($this->context->has('var2'));
        self::assertSame('value2', $this->context->get('var2'));

        self::assertTrue($this->context->has('var3'));
        self::assertSame('value3', $this->context->get('var3'));
    }

    public function testGet(): void
    {
        self::assertSame('value', $this->context->get('var'));
    }

    public function testGetThrowsContextException(): void
    {
        $this->expectException(ContextException::class);
        $this->expectExceptionMessage('Variable "unknown" does not exist in the context.');

        $this->context->get('unknown');
    }

    public function testHas(): void
    {
        self::assertTrue($this->context->has('var'));
    }

    public function testHasWithUnknownVariable(): void
    {
        self::assertFalse($this->context->has('unknown'));
    }

    public function testAll(): void
    {
        self::assertSame(['var' => 'value'], $this->context->all());
    }

    public function testGetIterator(): void
    {
        self::assertInstanceOf(ArrayIterator::class, $this->context->getIterator());

        $variables = [];
        foreach ($this->context as $identifier => $value) {
            $variables[$identifier] = $value;
        }

        self::assertSame($this->context->all(), $variables);
    }

    public function testCount(): void
    {
        self::assertCount(1, $this->context);
    }

    public function testOffsetExists(): void
    {
        self::assertArrayHasKey('var', $this->context);
        self::assertArrayNotHasKey('var2', $this->context);
    }

    public function testOffsetGet(): void
    {
        self::assertSame('value', $this->context['var']);
    }

    public function testOffsetSet(): void
    {
        $this->context['var'] = 'value2';

        self::assertSame('value2', $this->context['var']);
    }

    public function testOffsetUnset(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Method call not supported.');

        unset($this->context['var']);
    }
}
