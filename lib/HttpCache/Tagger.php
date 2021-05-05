<?php

declare(strict_types=1);

namespace Netgen\Layouts\HttpCache;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\API\Values\Layout\Layout;

final class Tagger implements TaggerInterface
{
    /**
     * @var \FOS\HttpCache\ResponseTagger|null
     */
    private $responseTagger;

    /**
     * Typehint is not specified to support FOS HTTP Cache Bundle 1.x, which uses a different class.
     *
     * @deprecated Add the typehint when support for FOS HTTP Cache Bundle 1.x ends.
     *
     * @param \FOS\HttpCache\ResponseTagger|null $responseTagger
     */
    public function __construct(/* ResponseTagger */ $responseTagger = null)
    {
        $this->responseTagger = $responseTagger;
    }

    public function tagLayout(Layout $layout): void
    {
        if ($this->responseTagger !== null) {
            $this->responseTagger->addTags(['ngl-all', 'ngl-layout-' . $layout->getId()->toString()]);
        }
    }

    public function tagBlock(Block $block): void
    {
        if ($this->responseTagger !== null) {
            $this->responseTagger->addTags(
                [
                    'ngl-all',
                    'ngl-block-' . $block->getId()->toString(),
                    'ngl-origin-layout-' . $block->getLayoutId()->toString(),
                ],
            );
        }
    }
}
