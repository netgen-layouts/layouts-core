<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\HttpCache;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\HttpCache\Tagger;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\Response;

final class TaggerTest extends TestCase
{
    /**
     * @var \Netgen\Layouts\HttpCache\Tagger
     */
    private $tagger;

    protected function setUp(): void
    {
        $this->tagger = new Tagger();
    }

    /**
     * @covers \Netgen\Layouts\HttpCache\Tagger::tagLayout
     */
    public function testTagLayout(): void
    {
        $uuid = Uuid::uuid4();

        $response = new Response();
        $response->setVary('Cookie');
        $layout = Layout::fromArray(['id' => $uuid]);

        $this->tagger->tagLayout($response, $layout);

        self::assertTrue($response->headers->has('X-Layout-Id'));
        self::assertSame($uuid->toString(), $response->headers->get('X-Layout-Id'));

        self::assertTrue($response->hasVary());
        self::assertSame(['Cookie', 'X-Layout-Id'], $response->getVary());
    }

    /**
     * @covers \Netgen\Layouts\HttpCache\Tagger::tagBlock
     */
    public function testTagBlock(): void
    {
        $layoutUuid = Uuid::uuid4();
        $blockUuid = Uuid::uuid4();

        $response = new Response();
        $block = Block::fromArray(['id' => $blockUuid, 'layoutId' => $layoutUuid]);

        $this->tagger->tagBlock($response, $block);

        self::assertTrue($response->headers->has('X-Block-Id'));
        self::assertSame($blockUuid->toString(), $response->headers->get('X-Block-Id'));

        self::assertTrue($response->headers->has('X-Origin-Layout-Id'));
        self::assertSame($layoutUuid->toString(), $response->headers->get('X-Origin-Layout-Id'));
    }
}
