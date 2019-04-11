<?php

declare(strict_types=1);

namespace Netgen\Layouts\HttpCache;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\API\Values\Layout\Layout;
use Symfony\Component\HttpFoundation\Response;

final class Tagger implements TaggerInterface
{
    public function tagLayout(Response $response, Layout $layout): void
    {
        $response->headers->set('X-Layout-Id', (string) $layout->getId());
        $response->setVary('X-Layout-Id', false);
    }

    public function tagBlock(Response $response, Block $block): void
    {
        $response->headers->set('X-Block-Id', (string) $block->getId());
        $response->headers->set('X-Origin-Layout-Id', (string) $block->getLayoutId());
    }
}
