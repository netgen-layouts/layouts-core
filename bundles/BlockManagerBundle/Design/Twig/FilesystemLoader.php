<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Design\Twig;

use Netgen\Bundle\BlockManagerBundle\Configuration\ConfigurationInterface;
use Twig\Loader\LoaderInterface;

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

    public function getSourceContext($name)
    {
        return $this->innerLoader->getSourceContext($this->getRealName($name));
    }

    /**
     * @deprecated Used for compatibility with Twig 1.x
     *
     * @param string $name
     */
    public function getSource($name)
    {
        return $this->innerLoader->getSourceContext($this->getRealName($name))->getCode();
    }

    public function getCacheKey($name)
    {
        return $this->innerLoader->getCacheKey($this->getRealName($name));
    }

    public function isFresh($name, $time)
    {
        return $this->innerLoader->isFresh($this->getRealName($name), $time);
    }

    public function exists($name)
    {
        return $this->innerLoader->exists($this->getRealName($name));
    }

    /**
     * Returns the name of the template converted from the virtual Twig namespace ("@ngbm")
     * to the real currently defined design name.
     *
     * @param string $name
     *
     * @return string
     */
    private function getRealName($name)
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
