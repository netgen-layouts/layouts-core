<?php

namespace Netgen\BlockManager\Tests\View\Matcher;

use Netgen\BlockManager\Tests\Core\Stubs\Value;
use Netgen\BlockManager\View\Matcher\APIVersion;
use Netgen\BlockManager\Tests\View\Stubs\View;
use PHPUnit\Framework\TestCase;

class APIVersionTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\View\Matcher\MatcherInterface
     */
    protected $matcher;

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
        $view = new View(array('valueObject' => new Value()));
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
        return array(
            array(array(), false),
            array(array(24), false),
            array(array(42), true),
            array(array(43, 24), false),
            array(array(24, 42), true),
        );
    }

    /**
     * @covers \Netgen\BlockManager\View\Matcher\APIVersion::match
     */
    public function testMatchWithNoAPIVersion()
    {
        $this->assertFalse($this->matcher->match(new View(array('valueObject' => new Value())), array()));
    }
}
