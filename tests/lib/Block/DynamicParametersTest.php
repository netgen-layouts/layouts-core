<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Block;

use Netgen\BlockManager\Block\DynamicParameters;
use PHPUnit\Framework\TestCase;

final class DynamicParametersTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Block\DynamicParameters
     */
    private $dynamicParams;

    public function setUp()
    {
        $this->dynamicParams = new DynamicParameters();

        $this->dynamicParams['test'] = 'some_value';
        $this->dynamicParams['closure'] = function () {
            return 'closure_value';
        };
    }

    /**
     * @covers \Netgen\BlockManager\Block\DynamicParameters::count
     */
    public function testCount()
    {
        $this->assertCount(2, $this->dynamicParams);
    }

    /**
     * @covers \Netgen\BlockManager\Block\DynamicParameters::offsetExists
     */
    public function testOffsetExists()
    {
        $this->assertArrayHasKey('test', $this->dynamicParams);
        $this->assertArrayHasKey('closure', $this->dynamicParams);
        $this->assertArrayNotHasKey('unknown', $this->dynamicParams);
    }

    /**
     * @covers \Netgen\BlockManager\Block\DynamicParameters::offsetGet
     */
    public function testOffsetGet()
    {
        $this->assertEquals('some_value', $this->dynamicParams['test']);
        $this->assertEquals('closure_value', $this->dynamicParams['closure']);
        $this->assertNull($this->dynamicParams['unknown']);
    }

    /**
     * @covers \Netgen\BlockManager\Block\DynamicParameters::offsetSet
     */
    public function testOffsetSet()
    {
        $this->dynamicParams['new'] = 'new_value';
        $this->dynamicParams['test'] = 'value2';
        $this->dynamicParams['closure'] = function () {
            return 'closure_value2';
        };

        $this->assertEquals('new_value', $this->dynamicParams['new']);
        $this->assertEquals('value2', $this->dynamicParams['test']);
        $this->assertEquals('closure_value2', $this->dynamicParams['closure']);
    }

    /**
     * @covers \Netgen\BlockManager\Block\DynamicParameters::offsetUnset()
     */
    public function testOffsetUnset()
    {
        unset($this->dynamicParams['test'], $this->dynamicParams['closure'], $this->dynamicParams['unknown']);

        $this->assertArrayNotHasKey('test', $this->dynamicParams);
        $this->assertArrayNotHasKey('closure', $this->dynamicParams);
        $this->assertArrayNotHasKey('unknown', $this->dynamicParams);
    }
}
