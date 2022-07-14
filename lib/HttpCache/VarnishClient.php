<?php

declare(strict_types=1);

namespace Netgen\Layouts\HttpCache;

use FOS\HttpCache\CacheInvalidator;
use FOS\HttpCache\Exception\ExceptionCollection;
use FOS\HttpCache\ProxyClient\Invalidation\TagCapable;
use Netgen\Layouts\HttpCache\Varnish\HostHeaderProviderInterface;

use function interface_exists;

final class VarnishClient implements ClientInterface
{
    private CacheInvalidator $fosInvalidator;

    private HostHeaderProviderInterface $hostHeaderProvider;

    public function __construct(
        CacheInvalidator $fosInvalidator,
        HostHeaderProviderInterface $hostHeaderProvider
    ) {
        $this->fosInvalidator = $fosInvalidator;
        $this->hostHeaderProvider = $hostHeaderProvider;
    }

    public function purge(array $tags): void
    {
        if (interface_exists(TagCapable::class)) {
            // FOS HTTP Cache v2 support
            $this->fosInvalidator->invalidateTags($tags);

            return;
        }

        $hostHeader = $this->hostHeaderProvider->provideHostHeader();

        foreach ($tags as $tag) {
            $this->fosInvalidator->invalidatePath(
                '/',
                [
                    'key' => $tag,
                    'Host' => $hostHeader,
                ],
            );
        }
    }

    public function commit(): bool
    {
        try {
            $this->fosInvalidator->flush();
        } catch (ExceptionCollection $e) {
            // Do nothing, FOS invalidator will write to log.
            return false;
        }

        return true;
    }
}
