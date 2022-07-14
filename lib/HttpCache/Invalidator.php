<?php

declare(strict_types=1);

namespace Netgen\Layouts\HttpCache;

use Netgen\Layouts\HttpCache\Layout\IdProviderInterface;

use function array_map;
use function array_merge;
use function count;

final class Invalidator implements InvalidatorInterface
{
    private ClientInterface $client;

    private IdProviderInterface $layoutIdProvider;

    public function __construct(ClientInterface $client, IdProviderInterface $layoutIdProvider)
    {
        $this->client = $client;
        $this->layoutIdProvider = $layoutIdProvider;
    }

    public function invalidateLayouts(array $layoutIds): void
    {
        if (count($layoutIds) === 0) {
            return;
        }

        $allLayoutIds = [];
        foreach ($layoutIds as $layoutId) {
            $allLayoutIds[] = $this->layoutIdProvider->provideIds($layoutId);
        }

        $this->client->purge(
            array_map(
                static fn (string $layoutId): string => 'ngl-layout-' . $layoutId,
                array_merge(...$allLayoutIds),
            ),
        );
    }

    public function invalidateBlocks(array $blockIds): void
    {
        if (count($blockIds) === 0) {
            return;
        }

        $this->client->purge(
            array_map(
                static fn (string $blockId): string => 'ngl-block-' . $blockId,
                $blockIds,
            ),
        );
    }

    public function invalidateLayoutBlocks(array $layoutIds): void
    {
        if (count($layoutIds) === 0) {
            return;
        }

        $this->client->purge(
            array_map(
                static fn (string $layoutId): string => 'ngl-origin-layout-' . $layoutId,
                $layoutIds,
            ),
        );
    }

    public function commit(): bool
    {
        return $this->client->commit();
    }
}
