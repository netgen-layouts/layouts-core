<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Context;

use ArrayIterator;
use Netgen\Layouts\Context\Context;
use Netgen\Layouts\Exception\Context\ContextException;
use Netgen\Layouts\Exception\RuntimeException;
use PHPUnit\Framework\TestCase;

final class ContextTest extends TestCase
{
    private Context $context;

    protected function setUp(): void
    {
        $this->context = new Context();
        $this->context->set('var', 'value');
    }

    /**
     * @covers \Netgen\Layouts\Context\Context::get
     * @covers \Netgen\Layouts\Context\Context::has
     * @covers \Netgen\Layouts\Context\Context::set
     */
    public function testSet(): void
    {
        $this->context->set('var2', 'value2');

        self::assertTrue($this->context->has('var2'));
        self::assertSame('value2', $this->context->get('var2'));
    }

    /**
     * @covers \Netgen\Layouts\Context\Context::add
     * @covers \Netgen\Layouts\Context\Context::get
     * @covers \Netgen\Layouts\Context\Context::has
     */
    public function testAdd(): void
    {
        $this->context->add(['var2' => 'value2', 'var3' => 'value3']);

        self::assertTrue($this->context->has('var2'));
        self::assertSame('value2', $this->context->get('var2'));

        self::assertTrue($this->context->has('var3'));
        self::assertSame('value3', $this->context->get('var3'));
    }

    /**
     * @covers \Netgen\Layouts\Context\Context::get
     */
    public function testGet(): void
    {
        self::assertSame('value', $this->context->get('var'));
    }

    /**
     * @covers \Netgen\Layouts\Context\Context::get
     */
    public function testGetThrowsContextException(): void
    {
        $this->expectException(ContextException::class);
        $this->expectExceptionMessage('Variable "unknown" does not exist in the context.');

        $this->context->get('unknown');
    }

    /**
     * @covers \Netgen\Layouts\Context\Context::has
     */
    public function testHas(): void
    {
        self::assertTrue($this->context->has('var'));
    }

    /**
     * @covers \Netgen\Layouts\Context\Context::has
     */
    public function testHasWithUnknownVariable(): void
    {
        self::assertFalse($this->context->has('unknown'));
    }

    /**
     * @covers \Netgen\Layouts\Context\Context::all
     */
    public function testAll(): void
    {
        self::assertSame(['var' => 'value'], $this->context->all());
    }

    /**
     * @covers \Netgen\Layouts\Context\Context::getIterator
     */
    public function testGetIterator(): void
    {
        self::assertInstanceOf(ArrayIterator::class, $this->context->getIterator());

        $variables = [];
        foreach ($this->context as $identifier => $value) {
            $variables[$identifier] = $value;
        }

        self::assertSame($this->context->all(), $variables);
    }

    /**
     * @covers \Netgen\Layouts\Context\Context::count
     */
    public function testCount(): void
    {
        self::assertCount(1, $this->context);
    }

    /**
     * @covers \Netgen\Layouts\Context\Context::offsetExists
     */
    public function testOffsetExists(): void
    {
        self::assertArrayHasKey('var', $this->context);
        self::assertArrayNotHasKey('var2', $this->context);
    }

    /**
     * @covers \Netgen\Layouts\Context\Context::offsetGet
     */
    public function testOffsetGet(): void
    {
        self::assertSame('value', $this->context['var']);
    }

    /**
     * @covers \Netgen\Layouts\Context\Context::offsetSet
     */
    public function testOffsetSet(): void
    {
        $this->context['var'] = 'value2';

        self::assertSame('value2', $this->context['var']);
    }

    /**
     * @covers \Netgen\Layouts\Context\Context::offsetUnset
     */
    public function testOffsetUnset(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Method call not supported.');

        unset($this->context['var']);
    }
}
