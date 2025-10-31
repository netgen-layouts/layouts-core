<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsDebugBundle\DataCollector;

use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

use function sprintf;

final class LayoutUrlGenerator implements LayoutUrlGeneratorInterface
{
    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
        private bool $isDebug = false,
    ) {}

    public function generateLayoutUrl(UuidInterface $layoutId, array $parameters = []): string
    {
        return $this->urlGenerator->generate(
            $this->isDebug ? 'nglayouts_dev_app' : 'nglayouts_app',
            [...$parameters, ...['_fragment' => sprintf('layout/%s', $layoutId->toString())]],
        );
    }
}
