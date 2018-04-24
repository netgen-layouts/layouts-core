<?php

namespace Netgen\BlockManager\Tests\Item;

use Netgen\BlockManager\Item\Item;
use Netgen\BlockManager\Item\NullItem;
use Netgen\BlockManager\Item\UrlGenerator;
use Netgen\BlockManager\Tests\Item\Stubs\ValueUrlGenerator;
use PHPUnit\Framework\TestCase;
use stdClass;

final class UrlGeneratorTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Item\UrlGeneratorInterface
     */
    private $urlGenerator;

    public function setUp()
    {
        $this->urlGenerator = new UrlGenerator(
            ['value' => new ValueUrlGenerator()]
        );
    }

    /**
     * @covers \Netgen\BlockManager\Item\UrlGenerator::__construct
     * @expectedException \Netgen\BlockManager\Exception\InvalidInterfaceException
     * @expectedExceptionMessage Value URL generator "stdClass" needs to implement "Netgen\BlockManager\Item\ValueUrlGeneratorInterface" interface.
     */
    public function testConstructorThrowsInvalidInterfaceExceptionWithWrongInterface()
    {
        new UrlGenerator([new stdClass()]);
    }

    /**
     * @covers \Netgen\BlockManager\Item\UrlGenerator::__construct
     * @covers \Netgen\BlockManager\Item\UrlGenerator::generate
     */
    public function testGenerate()
    {
        $this->assertEquals(
            '/item-url',
            $this->urlGenerator->generate(
                new Item(['valueType' => 'value'])
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Item\UrlGenerator::__construct
     * @covers \Netgen\BlockManager\Item\UrlGenerator::generate
     */
    public function testGenerateWithNullItem()
    {
        $this->assertNull(
            $this->urlGenerator->generate(
                new NullItem('value')
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Item\UrlGenerator::generate
     * @expectedException \Netgen\BlockManager\Exception\Item\ItemException
     * @expectedExceptionMessage Value type "unknown" does not exist.
     */
    public function testGenerateWithNoUrlGenerator()
    {
        $this->urlGenerator->generate(
            new Item(['valueType' => 'unknown'])
        );
    }
}
