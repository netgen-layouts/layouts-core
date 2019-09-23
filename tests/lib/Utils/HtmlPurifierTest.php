<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Utils;

use Netgen\Layouts\Utils\HtmlPurifier;
use PHPUnit\Framework\TestCase;

final class HtmlPurifierTest extends TestCase
{
    /**
     * @var \Netgen\Layouts\Utils\HtmlPurifier
     */
    private $htmlPurifier;

    protected function setUp(): void
    {
        $this->htmlPurifier = new HtmlPurifier();
    }

    /**
     * @covers \Netgen\Layouts\Utils\HtmlPurifier::__construct
     * @covers \Netgen\Layouts\Utils\HtmlPurifier::purify
     */
    public function testPurify(): void
    {
        $unsafeHtml = "<h1>Title</h1><script src=\"https://cool-hacker.com/cool-hacking-script.js\"></script><a onclick=\"alert('Haw-haw!');\" href=\"http://www.google.com\">Google</a>";
        $safeHtml = '<h1>Title</h1><a href="http://www.google.com">Google</a>';

        self::assertSame($safeHtml, $this->htmlPurifier->purify($unsafeHtml));
    }
}
