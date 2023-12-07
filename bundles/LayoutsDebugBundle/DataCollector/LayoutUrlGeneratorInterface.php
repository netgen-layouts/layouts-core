<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsDebugBundle\DataCollector;

use Ramsey\Uuid\UuidInterface;

interface LayoutUrlGeneratorInterface
{
    /**
     * @param array<string, mixed> $parameters
     */
    public function generateLayoutUrl(UuidInterface $layoutId, array $parameters = []): string;
}
