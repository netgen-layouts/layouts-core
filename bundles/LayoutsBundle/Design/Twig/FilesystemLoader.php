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
    private LoaderInterface $innerLoader;

    private ConfigurationInterface $configuration;

    /**
     * @var array<string, string>
     */
    private array $templateMap = [];

    public function __construct(LoaderInterface $innerLoader, ConfigurationInterface $configuration)
    {
        $this->innerLoader = $innerLoader;
        $this->configuration = $configuration;
    }

    /**
     * @param string $name
     */
    public function getSourceContext($name): Source
    {
        return $this->innerLoader->getSourceContext($this->getRealName($name));
    }

    /**
     * @param string $name
     */
    public function getCacheKey($name): string
    {
        return $this->innerLoader->getCacheKey($this->getRealName($name));
    }

    /**
     * @param string $name
     * @param int $time
     */
    public function isFresh($name, $time): bool
    {
        return $this->innerLoader->isFresh($this->getRealName($name), $time);
    }

    /**
     * @param string $name
     */
    public function exists($name): bool
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
