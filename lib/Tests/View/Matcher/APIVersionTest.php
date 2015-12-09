<?php

namespace Netgen\BlockManager\Tests\View\Matcher;

use Netgen\BlockManager\View\Matcher\APIVersion;
use Netgen\BlockManager\Tests\View\Stubs\View;

class APIVersionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param array $config
     * @param bool $expected
     *
     * @covers \Netgen\BlockManager\View\Matcher\APIVersion::match
     * @covers \Netgen\BlockManager\View\Matcher\Matcher::setConfig
     * @dataProvider matchProvider
     */
    public function testMatch(array $config, $expected)
    {
        $matcher = new APIVersion();
        $matcher->setConfig($config);

        $view = new View();
        $view->addParameters(array('api_version' => 42));

        self::assertEquals($expected, $matcher->match($view));
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
            array(array(42), true),
            array(array(24), false),
            array(array(24, 42), true),
            array(array(42, 24), true),
        );
    }

    /**
     * @covers \Netgen\BlockManager\View\Matcher\APIVersion::match
     */
    public function testMatchWithNoAPIVersion()
    {
        $matcher = new APIVersion();
        self::assertEquals(false, $matcher->match(new View()));
    }
}
