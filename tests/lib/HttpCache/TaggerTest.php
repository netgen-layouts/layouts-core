<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\HttpCache;

use FOS\HttpCacheBundle\Http\SymfonyResponseTagger;
use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\HttpCache\Tagger;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

use function class_exists;

final class TaggerTest extends TestCase
{
    private SymfonyResponseTagger $responseTagger;

    private Tagger $tagger;

    protected function setUp(): void
    {
        if (!class_exists(SymfonyResponseTagger::class)) {
            self::markTestSkipped('Test requires friendsofsymfony/http-cache-bundle 2.x to run');
        }

        $this->responseTagger = new SymfonyResponseTagger();
        $this->tagger = new Tagger($this->responseTagger);
    }

    /**
     * @covers \Netgen\Layouts\HttpCache\Tagger::__construct
     * @covers \Netgen\Layouts\HttpCache\Tagger::tagLayout
     */
    public function testTagLayout(): void
    {
        $uuid = Uuid::uuid4();
        $layout = Layout::fromArray(['id' => $uuid]);

        $this->tagger->tagLayout($layout);

        self::assertTrue($this->responseTagger->hasTags());
        self::assertSame('ngl-all,ngl-layout-' . $uuid->toString(), $this->responseTagger->getTagsHeaderValue());
    }

    /**
     * @covers \Netgen\Layouts\HttpCache\Tagger::tagBlock
     */
    public function testTagBlock(): void
    {
        $layoutUuid = Uuid::uuid4();
        $blockUuid = Uuid::uuid4();

        $block = Block::fromArray(['id' => $blockUuid, 'layoutId' => $layoutUuid]);

        $this->tagger->tagBlock($block);

        self::assertTrue($this->responseTagger->hasTags());
        self::assertSame(
            'ngl-all,ngl-block-' . $blockUuid->toString() . ',ngl-origin-layout-' . $layoutUuid->toString(),
            $this->responseTagger->getTagsHeaderValue(),
        );
    }
}
