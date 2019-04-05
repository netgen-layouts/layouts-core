<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Design\Twig;

use Netgen\Bundle\BlockManagerBundle\Configuration\ConfigurationInterface;
use Twig\Loader\LoaderInterface;
use Twig\Source;

final class FilesystemLoader implements LoaderInterface
{
    /**
     * @var \Twig\Loader\LoaderInterface
     */
    private $innerLoader;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\Configuration\ConfigurationInterface
     */
    private $configuration;

    /**
     * @var array
     */
    private $templateMap;

    public function __construct(LoaderInterface $innerLoader, ConfigurationInterface $configuration)
    {
        $this->innerLoader = $innerLoader;
        $this->configuration = $configuration;
    }

    public function getSourceContext($name): Source
    {
        return $this->innerLoader->getSourceContext($this->getRealName($name));
    }

    public function getCacheKey($name): string
    {
        return $this->innerLoader->getCacheKey($this->getRealName($name));
    }

    public function isFresh($name, $time): bool
    {
        return $this->innerLoader->isFresh($this->getRealName($name), $time);
    }

    public function exists($name): bool
    {
        return $this->innerLoader->exists($this->getRealName($name));
    }

    /**
     * Returns the name of the template converted from the virtual Twig namespace ("@ngbm")
     * to the real currently defined design name.
     */
    private function getRealName(string $name): string
    {
        if (mb_strpos($name, '@ngbm/') !== 0) {
            return $name;
        }

        if (!isset($this->templateMap[$name])) {
            $this->templateMap[$name] = str_replace(
                '@ngbm/',
                '@ngbm_' . $this->configuration->getParameter('design') . '/',
                $name
            );
        }

        return $this->templateMap[$name];
    }
}
