<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsDebugBundle\DataCollector;

use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

use function sprintf;

final class LayoutUrlGenerator implements LayoutUrlGeneratorInterface
{
    private UrlGeneratorInterface $urlGenerator;

    private bool $isDebug;

    public function __construct(UrlGeneratorInterface $urlGenerator, bool $isDebug = false)
    {
        $this->urlGenerator = $urlGenerator;
        $this->isDebug = $isDebug;
    }

    public function generateLayoutUrl(UuidInterface $layoutId, array $parameters = []): string
    {
        return $this->urlGenerator->generate(
            $this->isDebug ? 'nglayouts_dev_app' : 'nglayouts_app',
            ['_fragment' => sprintf('layout/%s', $layoutId->toString())] + $parameters,
        );
    }
}
