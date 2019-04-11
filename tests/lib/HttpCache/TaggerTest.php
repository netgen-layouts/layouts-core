<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\HttpCache;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\HttpCache\Tagger;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;

final class TaggerTest extends TestCase
{
    /**
     * @var \Netgen\Layouts\HttpCache\Tagger
     */
    private $tagger;

    public function setUp(): void
    {
        $this->tagger = new Tagger();
    }

    /**
     * @covers \Netgen\Layouts\HttpCache\Tagger::tagLayout
     */
    public function testTagLayout(): void
    {
        $response = new Response();
        $response->setVary('Cookie');
        $layout = Layout::fromArray(['id' => 42]);

        $this->tagger->tagLayout($response, $layout);

        self::assertTrue($response->headers->has('X-Layout-Id'));
        self::assertSame('42', $response->headers->get('X-Layout-Id'));

        self::assertTrue($response->hasVary());
        self::assertSame(['Cookie', 'X-Layout-Id'], $response->getVary());
    }

    /**
     * @covers \Netgen\Layouts\HttpCache\Tagger::tagBlock
     */
    public function testTagBlock(): void
    {
        $response = new Response();
        $block = Block::fromArray(['id' => 42, 'layoutId' => 24]);

        $this->tagger->tagBlock($response, $block);

        self::assertTrue($response->headers->has('X-Block-Id'));
        self::assertSame('42', $response->headers->get('X-Block-Id'));

        self::assertTrue($response->headers->has('X-Origin-Layout-Id'));
        self::assertSame('24', $response->headers->get('X-Origin-Layout-Id'));
    }
}
