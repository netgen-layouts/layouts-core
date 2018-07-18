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

    public function setUp(): void
    {
        $this->tagger = new Tagger();
    }

    /**
     * @covers \Netgen\BlockManager\HttpCache\Tagger::tagLayout
     */
    public function testTagLayout(): void
    {
        $response = new Response();
        $response->setVary('Cookie');
        $layout = Layout::fromArray(['id' => 42]);

        $this->tagger->tagLayout($response, $layout);

        $this->assertTrue($response->headers->has('X-Layout-Id'));
        $this->assertSame('42', $response->headers->get('X-Layout-Id'));

        $this->assertTrue($response->hasVary());
        $this->assertSame(['Cookie', 'X-Layout-Id'], $response->getVary());
    }

    /**
     * @covers \Netgen\BlockManager\HttpCache\Tagger::tagBlock
     */
    public function testTagBlock(): void
    {
        $response = new Response();
        $block = Block::fromArray(['id' => 42, 'layoutId' => 24]);

        $this->tagger->tagBlock($response, $block);

        $this->assertTrue($response->headers->has('X-Block-Id'));
        $this->assertSame('42', $response->headers->get('X-Block-Id'));

        $this->assertTrue($response->headers->has('X-Origin-Layout-Id'));
        $this->assertSame('24', $response->headers->get('X-Origin-Layout-Id'));
    }
}
