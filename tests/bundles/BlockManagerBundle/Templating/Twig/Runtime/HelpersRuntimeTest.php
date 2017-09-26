<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\Templating\Twig\Runtime;

use Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\HelpersRuntime;
use PHPUnit\Framework\TestCase;

class HelpersRuntimeTest extends TestCase
{
    /**
     * @var \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\HelpersRuntime
     */
    private $runtime;

    public function setUp()
    {
        $this->runtime = new HelpersRuntime();
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\HelpersRuntime::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\HelpersRuntime::getLocaleName
     */
    public function testGetLocaleName()
    {
        $localeName = $this->runtime->getLocaleName('hr_HR', 'hr_HR');

        $this->assertEquals('hrvatski (Hrvatska)', $localeName);
    }
}
