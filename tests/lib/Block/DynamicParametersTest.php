<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Block;

use Netgen\Layouts\Block\DynamicParameters;
use PHPUnit\Framework\TestCase;

final class DynamicParametersTest extends TestCase
{
    private DynamicParameters $dynamicParams;

    protected function setUp(): void
    {
        $this->dynamicParams = new DynamicParameters();

        $this->dynamicParams['test'] = 'some_value';
        $this->dynamicParams['closure'] = static fn (): string => 'closure_value';
    }

    /**
     * @covers \Netgen\Layouts\Block\DynamicParameters::count
     */
    public function testCount(): void
    {
        self::assertCount(2, $this->dynamicParams);
    }

    /**
     * @covers \Netgen\Layouts\Block\DynamicParameters::offsetExists
     */
    public function testOffsetExists(): void
    {
        self::assertArrayHasKey('test', $this->dynamicParams);
        self::assertArrayHasKey('closure', $this->dynamicParams);
        self::assertArrayNotHasKey('unknown', $this->dynamicParams);
    }

    /**
     * @covers \Netgen\Layouts\Block\DynamicParameters::offsetGet
     */
    public function testOffsetGet(): void
    {
        self::assertSame('some_value', $this->dynamicParams['test']);
        self::assertSame('closure_value', $this->dynamicParams['closure']);
        self::assertNull($this->dynamicParams['unknown']);
    }

    /**
     * @covers \Netgen\Layouts\Block\DynamicParameters::offsetSet
     */
    public function testOffsetSet(): void
    {
        $this->dynamicParams['new'] = 'new_value';
        $this->dynamicParams['test'] = 'value2';
        $this->dynamicParams['closure'] = static fn (): string => 'closure_value2';

        self::assertSame('new_value', $this->dynamicParams['new']);
        self::assertSame('value2', $this->dynamicParams['test']);
        self::assertSame('closure_value2', $this->dynamicParams['closure']);
    }

    /**
     * @covers \Netgen\Layouts\Block\DynamicParameters::offsetUnset
     */
    public function testOffsetUnset(): void
    {
        unset($this->dynamicParams['test'], $this->dynamicParams['closure'], $this->dynamicParams['unknown']);

        self::assertArrayNotHasKey('test', $this->dynamicParams);
        self::assertArrayNotHasKey('closure', $this->dynamicParams);
        self::assertArrayNotHasKey('unknown', $this->dynamicParams);
    }
}
