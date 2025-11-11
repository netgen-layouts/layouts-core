<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\HttpCache;

use FOS\HttpCacheBundle\Http\SymfonyResponseTagger;
use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\HttpCache\Tagger;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

#[CoversClass(Tagger::class)]
final class TaggerTest extends TestCase
{
    private SymfonyResponseTagger $responseTagger;

    private Tagger $tagger;

    protected function setUp(): void
    {
        $this->responseTagger = new SymfonyResponseTagger();
        $this->tagger = new Tagger($this->responseTagger);
    }

    public function testTagLayout(): void
    {
        $uuid = Uuid::uuid4();
        $layout = Layout::fromArray(['id' => $uuid]);

        $this->tagger->tagLayout($layout);

        self::assertTrue($this->responseTagger->hasTags());
        self::assertSame('ngl-all,ngl-layout-' . $uuid->toString(), $this->responseTagger->getTagsHeaderValue());
    }

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
