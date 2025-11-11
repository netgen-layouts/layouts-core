<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Utils;

use Netgen\Layouts\Utils\HtmlPurifier;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(HtmlPurifier::class)]
final class HtmlPurifierTest extends TestCase
{
    private HtmlPurifier $htmlPurifier;

    protected function setUp(): void
    {
        $this->htmlPurifier = new HtmlPurifier();
    }

    public function testPurify(): void
    {
        $unsafeHtml = "<h1>Title</h1><script src=\"https://cool-hacker.com/cool-hacking-script.js\"></script><a onclick=\"alert('Haw-haw!');\" href=\"https://netgen.io\" target=\"_blank\">Netgen</a>";
        $safeHtml = '<h1>Title</h1><a href="https://netgen.io" target="_blank">Netgen</a>';

        self::assertSame($safeHtml, $this->htmlPurifier->purify($unsafeHtml));
    }
}
