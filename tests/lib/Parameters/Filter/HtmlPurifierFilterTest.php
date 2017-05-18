<?php

namespace Netgen\BlockManager\Tests\Parameters\Filter;

use Netgen\BlockManager\Parameters\Filter\Html\HtmlPurifierFilter;
use PHPUnit\Framework\TestCase;

class HtmlPurifierFilterTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Parameters\Filter\Html\HtmlPurifierFilter
     */
    protected $filter;

    public function setUp()
    {
        $this->filter = new HtmlPurifierFilter();
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Filter\Html\HtmlPurifierFilter::__construct
     * @covers \Netgen\BlockManager\Parameters\Filter\Html\HtmlPurifierFilter::filter
     */
    public function testConvertOptions()
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

        $this->assertEquals($safeHtml, $this->filter->filter($unsafeHtml));
    }
}
