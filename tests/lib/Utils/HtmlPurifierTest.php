<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Utils;

use Netgen\BlockManager\Utils\HtmlPurifier;
use PHPUnit\Framework\TestCase;

final class HtmlPurifierTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Utils\HtmlPurifier
     */
    private $htmlPurifier;

    public function setUp(): void
    {
        $this->htmlPurifier = new HtmlPurifier();
    }

    /**
     * @covers \Netgen\BlockManager\Utils\HtmlPurifier::__construct
     * @covers \Netgen\BlockManager\Utils\HtmlPurifier::purify
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

        self::assertSame($safeHtml, $this->htmlPurifier->purify($unsafeHtml));
    }
}
