<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Parameters\ParameterType\Html;

use Netgen\BlockManager\Parameters\ParameterType\Html\HtmlPurifier;
use PHPUnit\Framework\TestCase;

final class HtmlPurifierTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Parameters\ParameterType\Html\HtmlPurifier
     */
    private $htmlPurifier;

    public function setUp(): void
    {
        $this->htmlPurifier = new HtmlPurifier();
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterType\Html\HtmlPurifier::__construct
     * @covers \Netgen\BlockManager\Parameters\ParameterType\Html\HtmlPurifier::purify
     */
    public function testPurify(): void
    {
        $unsafeHtml = <<<'HTML'
<h1>Title</h1>
<script src="https://cool-hacker.com/cool-hacking-script.js"></script>
<a onclick="alert('Haw-haw!');" href="http://www.google.com">Google</a>
HTML;

        $safeHtml = <<<'HTML'
<h1>Title</h1>
<a href="http://www.google.com">Google</a>
HTML;

        $this->assertSame($safeHtml, $this->htmlPurifier->purify($unsafeHtml));
    }
}
