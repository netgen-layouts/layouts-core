<?php

namespace Netgen\BlockManager\Tests\HttpCache\Layout\Strategy\Ban;

use Netgen\BlockManager\Core\Values\Layout\Layout;
use Netgen\BlockManager\HttpCache\Layout\Strategy\Ban\Tagger;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;

class TaggerTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\HttpCache\Layout\Strategy\Ban\Tagger
     */
    protected $tagger;

    public function setUp()
    {
        $this->tagger = new Tagger();
    }

    /**
     * @covers \Netgen\BlockManager\HttpCache\Layout\Strategy\Ban\Tagger::tag
     */
    public function testTag()
    {
        $response = new Response();
        $layout = new Layout(array('id' => 42));

        $this->tagger->tag($response, $layout);

        $this->assertTrue($response->headers->has('X-Layout-Id'));
        $this->assertEquals(42, $response->headers->get('X-Layout-Id'));
    }
}
