<?php

declare(strict_types=1);

namespace Netgen\Layouts\HttpCache;

use FOS\HttpCache\ResponseTagger;
use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\API\Values\Layout\Layout;

final class Tagger implements TaggerInterface
{
    public function __construct(
        private ?ResponseTagger $responseTagger = null,
    ) {}

    public function tagLayout(Layout $layout): void
    {
        $this->responseTagger?->addTags(['ngl-all', 'ngl-layout-' . $layout->id->toString()]);
    }

    public function tagBlock(Block $block): void
    {
        $this->responseTagger?->addTags(
            [
                'ngl-all',
                'ngl-block-' . $block->id->toString(),
                'ngl-origin-layout-' . $block->layoutId->toString(),
            ],
        );
    }
}
