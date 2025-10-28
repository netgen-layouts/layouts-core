<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Design\Twig;

use Netgen\Bundle\LayoutsBundle\Configuration\ConfigurationInterface;
use Twig\Loader\LoaderInterface;
use Twig\Source;

use function str_replace;
use function str_starts_with;

final class FilesystemLoader implements LoaderInterface
{
    /**
     * @var array<string, string>
     */
    private array $templateMap = [];

    public function __construct(
        private LoaderInterface $innerLoader,
        private ConfigurationInterface $configuration,
    ) {}

    public function getSourceContext(string $name): Source
    {
        return $this->innerLoader->getSourceContext($this->getRealName($name));
    }

    public function getCacheKey(string $name): string
    {
        return $this->innerLoader->getCacheKey($this->getRealName($name));
    }

    public function isFresh(string $name, int $time): bool
    {
        return $this->innerLoader->isFresh($this->getRealName($name), $time);
    }

    public function exists(string $name): bool
    {
        return $this->innerLoader->exists($this->getRealName($name));
    }

    /**
     * Returns the name of the template converted from the virtual Twig namespace ("@nglayouts")
     * to the real currently defined design name.
     */
    private function getRealName(string $name): string
    {
        if (!str_starts_with($name, '@nglayouts/')) {
            return $name;
        }

        $this->templateMap[$name] ??= str_replace(
            '@nglayouts/',
            '@nglayouts_' . $this->configuration->getParameter('design') . '/',
            $name,
        );

        return $this->templateMap[$name];
    }
}
