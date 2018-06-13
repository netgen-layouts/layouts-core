<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\HttpCache;

use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\Core\Values\Layout\Layout;
use Netgen\BlockManager\HttpCache\Tagger;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;

final class TaggerTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\HttpCache\Tagger
     */
    private $tagger;

    public function setUp()
    {
        $this->tagger = new Tagger();
    }

    /**
     * @covers \Netgen\BlockManager\HttpCache\Tagger::tagLayout
     */
    public function testTagLayout()
    {
        $response = new Response();
        $response->setVary('Cookie');
        $layout = new Layout(['id' => 42]);

        $this->tagger->tagLayout($response, $layout);

        $this->assertTrue($response->headers->has('X-Layout-Id'));
        $this->assertSame('42', $response->headers->get('X-Layout-Id'));

        $this->assertTrue($response->hasVary());
        $this->assertEquals(['Cookie', 'X-Layout-Id'], $response->getVary());
    }

    /**
     * @covers \Netgen\BlockManager\HttpCache\Tagger::tagBlock
     */
    public function testTagBlock()
    {
        $response = new Response();
        $block = new Block(['id' => 42, 'layoutId' => 24]);

        $this->tagger->tagBlock($response, $block);

        $this->assertTrue($response->headers->has('X-Block-Id'));
        $this->assertSame('42', $response->headers->get('X-Block-Id'));

        $this->assertTrue($response->headers->has('X-Origin-Layout-Id'));
        $this->assertSame('24', $response->headers->get('X-Origin-Layout-Id'));
    }
}
