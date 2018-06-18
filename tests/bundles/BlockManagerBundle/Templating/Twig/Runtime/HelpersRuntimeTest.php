<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Tests\Templating\Twig\Runtime;

use Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\HelpersRuntime;
use PHPUnit\Framework\TestCase;

final class HelpersRuntimeTest extends TestCase
{
    /**
     * @var \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\HelpersRuntime
     */
    private $runtime;

    public function setUp(): void
    {
        $this->runtime = new HelpersRuntime();
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\HelpersRuntime::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\HelpersRuntime::getLocaleName
     */
    public function testGetLocaleName(): void
    {
        $localeName = $this->runtime->getLocaleName('hr_HR', 'hr_HR');

        $this->assertSame('hrvatski (Hrvatska)', $localeName);
    }
}
