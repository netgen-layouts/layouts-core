<?php

declare(strict_types=1);

namespace Netgen\Layouts\Utils\BackwardsCompatibility;

use Symfony\Component\DependencyInjection\Loader\YamlFileLoader as BaseYamlFileLoader;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Yaml\Tag\TaggedValue;

/**
 * Overrides the original YamlFileLoader to replace !tagged YAML tag with !tagged_iterator in Symfony 4.4.
 *
 * https://github.com/symfony/symfony/issues/31289
 */
final class YamlFileLoader extends BaseYamlFileLoader
{
    protected function loadFile($file): array
    {
        $content = parent::loadFile($file);

        if (Kernel::VERSION_ID < 40400 || !array_key_exists('services', $content)) {
            return $content;
        }

        foreach ($content['services'] as $serviceId => $service) {
            foreach ($service['arguments'] ?? [] as $key => $argument) {
                if ($argument instanceof TaggedValue && $argument->getTag() === 'tagged') {
                    $content['services'][$serviceId]['arguments'][$key] = new TaggedValue('tagged_iterator', $argument->getValue());
                }
            }
        }

        return $content;
    }
}