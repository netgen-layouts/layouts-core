<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Context;

use ArrayIterator;
use Netgen\BlockManager\Context\Context;
use PHPUnit\Framework\TestCase;

final class ContextTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Context\Context
     */
    private $context;

    public function setUp(): void
    {
        $this->context = new Context();
        $this->context->set('var', 'value');
    }

    /**
     * @covers \Netgen\BlockManager\Context\Context::get
     * @covers \Netgen\BlockManager\Context\Context::has
     * @covers \Netgen\BlockManager\Context\Context::set
     */
    public function testSet(): void
    {
        $this->context->set('var2', 'value2');

        $this->assertTrue($this->context->has('var2'));
        $this->assertEquals('value2', $this->context->get('var2'));
    }

    /**
     * @covers \Netgen\BlockManager\Context\Context::add
     * @covers \Netgen\BlockManager\Context\Context::get
     * @covers \Netgen\BlockManager\Context\Context::has
     */
    public function testAdd(): void
    {
        $this->context->add(['var2' => 'value2', 'var3' => 'value3']);

        $this->assertTrue($this->context->has('var2'));
        $this->assertEquals('value2', $this->context->get('var2'));

        $this->assertTrue($this->context->has('var3'));
        $this->assertEquals('value3', $this->context->get('var3'));
    }

    /**
     * @covers \Netgen\BlockManager\Context\Context::get
     */
    public function testGet(): void
    {
        $this->assertEquals('value', $this->context->get('var'));
    }

    /**
     * @covers \Netgen\BlockManager\Context\Context::get
     * @expectedException \Netgen\BlockManager\Exception\Context\ContextException
     * @expectedExceptionMessage Variable "unknown" does not exist in the context.
     */
    public function testGetThrowsContextException(): void
    {
        $this->context->get('unknown');
    }

    /**
     * @covers \Netgen\BlockManager\Context\Context::has
     */
    public function testHas(): void
    {
        $this->assertTrue($this->context->has('var'));
    }

    /**
     * @covers \Netgen\BlockManager\Context\Context::has
     */
    public function testHasWithUnknownVariable(): void
    {
        $this->assertFalse($this->context->has('unknown'));
    }

    /**
     * @covers \Netgen\BlockManager\Context\Context::all
     */
    public function testAll(): void
    {
        $this->assertEquals(['var' => 'value'], $this->context->all());
    }

    /**
     * @covers \Netgen\BlockManager\Context\Context::getIterator
     */
    public function testGetIterator(): void
    {
        $this->assertInstanceOf(ArrayIterator::class, $this->context->getIterator());

        $variables = [];
        foreach ($this->context as $identifier => $value) {
            $variables[$identifier] = $value;
        }

        $this->assertEquals($this->context->all(), $variables);
    }

    /**
     * @covers \Netgen\BlockManager\Context\Context::count
     */
    public function testCount(): void
    {
        $this->assertCount(1, $this->context);
    }

    /**
     * @covers \Netgen\BlockManager\Context\Context::offsetExists
     */
    public function testOffsetExists(): void
    {
        $this->assertArrayHasKey('var', $this->context);
        $this->assertArrayNotHasKey('var2', $this->context);
    }

    /**
     * @covers \Netgen\BlockManager\Context\Context::offsetGet
     */
    public function testOffsetGet(): void
    {
        $this->assertEquals('value', $this->context['var']);
    }

    /**
     * @covers \Netgen\BlockManager\Context\Context::offsetSet
     */
    public function testOffsetSet(): void
    {
        $this->context['var'] = 'value2';

        $this->assertEquals('value2', $this->context['var']);
    }

    /**
     * @covers \Netgen\BlockManager\Context\Context::offsetUnset
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     * @expectedExceptionMessage Method call not supported.
     */
    public function testOffsetUnset(): void
    {
        unset($this->context['var']);
    }
}
