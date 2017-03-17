<?php

namespace Netgen\BlockManager\Tests\HttpCache\Block\Strategy\Ban;

use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\HttpCache\Block\Strategy\Ban\Tagger;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;

class TaggerTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\HttpCache\Block\Strategy\Ban\Tagger
     */
    protected $tagger;

    public function setUp()
    {
        $this->tagger = new Tagger();
    }

    /**
     * @covers \Netgen\BlockManager\HttpCache\Block\Strategy\Ban\Tagger::tag
     */
    public function testTag()
    {
        $response = new Response();
        $block = new Block(array('id' => 42));

        $this->tagger->tag($response, $block);

        $this->assertTrue($response->headers->has('X-Block-Id'));
        $this->assertEquals(42, $response->headers->get('X-Block-Id'));
    }

    /**
     * @covers \Netgen\BlockManager\HttpCache\Block\Strategy\Ban\Tagger::tag
     */
    public function testTagWhenDependantOnLayout()
    {
        $this->markTestIncomplete('Test for block tagger with dependency on layout is not implemented yet.');
    }
}
