<?php

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Configuration;

use Netgen\BlockManager\Configuration\Factory\SourceFactory;
use Netgen\BlockManager\Configuration\Source\Source;
use Netgen\BlockManager\Exception\RuntimeException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class SourcePass implements CompilerPassInterface
{
    const SERVICE_NAME = 'netgen_block_manager.configuration.registry.source';
    const TAG_NAME = 'netgen_block_manager.configuration.source';

    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has(self::SERVICE_NAME)) {
            return;
        }

        $sources = $container->getParameter('netgen_block_manager.sources');
        $queryTypes = $container->getParameter('netgen_block_manager.query_types');

        $this->validateSources($sources, $queryTypes);
        $this->buildSources($container, $sources);

        $registry = $container->findDefinition(self::SERVICE_NAME);
        $sourceServices = $container->findTaggedServiceIds(self::TAG_NAME);

        foreach ($sourceServices as $sourceService => $tag) {
            $registry->addMethodCall(
                'addSource',
                array(new Reference($sourceService))
            );
        }
    }

    /**
     * Builds the source objects from provided configuration.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param array $sources
     */
    protected function buildSources(ContainerBuilder $container, array $sources)
    {
        foreach ($sources as $identifier => $source) {
            if (!$source['enabled']) {
                continue;
            }

            $serviceIdentifier = sprintf('netgen_block_manager.configuration.source.%s', $identifier);

            $queryTypeReferences = array();
            foreach ($source['queries'] as $queryIdentifier => $queryConfig) {
                $queryTypeReferences[$queryIdentifier] = new Reference(
                    sprintf(
                        'netgen_block_manager.collection.query_type.%s',
                        $queryConfig['query_type']
                    )
                );
            }

            $container->register($serviceIdentifier, Source::class)
                ->setArguments(array($identifier, $source, $queryTypeReferences))
                ->addTag('netgen_block_manager.configuration.source')
                ->setFactory(array(SourceFactory::class, 'buildSource'));
        }
    }

    /**
     * Validates source config.
     *
     * @param array $sources
     * @param array $queryTypes
     *
     * @throws \RuntimeException If validation failed
     */
    protected function validateSources(array $sources, array $queryTypes)
    {
        foreach ($sources as $source => $sourceConfig) {
            foreach ($sourceConfig['queries'] as $queryConfig) {
                if (!isset($queryTypes[$queryConfig['query_type']])) {
                    throw new RuntimeException(
                        sprintf(
                            'Query type "%s" used in "%s" source does not exist.',
                            $queryConfig['query_type'],
                            $source
                        )
                    );
                }
            }
        }
    }
}
