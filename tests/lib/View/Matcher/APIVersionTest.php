<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\View\Matcher;

use Netgen\BlockManager\Tests\Core\Stubs\Value;
use Netgen\BlockManager\Tests\View\Stubs\View;
use Netgen\BlockManager\View\Matcher\APIVersion;
use PHPUnit\Framework\TestCase;

final class APIVersionTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\View\Matcher\MatcherInterface
     */
    private $matcher;

    public function setUp()
    {
        $this->matcher = new APIVersion();
    }

    /**
     * @param array $config
     * @param bool $expected
     *
     * @covers \Netgen\BlockManager\View\Matcher\APIVersion::match
     * @dataProvider matchProvider
     */
    public function testMatch(array $config, $expected)
    {
        $view = new View(['value' => new Value()]);
        $view->addParameter('api_version', 42);

        $this->assertEquals($expected, $this->matcher->match($view, $config));
    }

    /**
     * Provider for {@link self::testMatch}.
     *
     * @return array
     */
    public function matchProvider()
    {
        return [
            [[], false],
            [[24], false],
            [[42], true],
            [[43, 24], false],
            [[24, 42], true],
        ];
    }

    /**
     * @covers \Netgen\BlockManager\View\Matcher\APIVersion::match
     */
    public function testMatchWithNoAPIVersion()
    {
        $this->assertFalse($this->matcher->match(new View(['value' => new Value()]), []));
    }
}
