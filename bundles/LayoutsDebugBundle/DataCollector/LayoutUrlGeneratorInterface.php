<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsDebugBundle\DataCollector;

use Symfony\Component\Uid\Uuid;

interface LayoutUrlGeneratorInterface
{
    /**
     * @param array<string, mixed> $parameters
     */
    public function generateLayoutUrl(Uuid $layoutId, array $parameters = []): string;
}
