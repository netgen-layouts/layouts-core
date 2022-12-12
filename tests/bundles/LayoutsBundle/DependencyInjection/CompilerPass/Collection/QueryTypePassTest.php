<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\DependencyInjection\CompilerPass\Collection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractContainerBuilderTestCase;
use Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Collection\QueryTypePass;
use Netgen\Layouts\Exception\RuntimeException;
use stdClass;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ParameterBag\FrozenParameterBag;
use Symfony\Component\DependencyInjection\Reference;

final class QueryTypePassTest extends AbstractContainerBuilderTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->container->addCompilerPass(new QueryTypePass());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Collection\QueryTypePass::process
     */
    public function testProcess(): void
    {
        $queryTypes = ['query_type' => ['priority' => 0]];
        $this->setParameter('netgen_layouts.query_types', $queryTypes);

        $this->setDefinition('netgen_layouts.collection.query_type_factory', new Definition());
        $this->setDefinition('netgen_layouts.collection.registry.query_type', new Definition(null, [[]]));

        $queryTypeHandler = new Definition(stdClass::class);
        $queryTypeHandler->addTag('netgen_layouts.query_type_handler', ['type' => 'query_type']);
        $this->setDefinition('netgen_layouts.collection.query_type.handler.test', $queryTypeHandler);

        $this->compile();

        $this->assertContainerBuilderHasService(
            'netgen_layouts.collection.query_type.query_type',
        );

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'netgen_layouts.collection.registry.query_type',
            0,
            [
                'query_type' => new Reference('netgen_layouts.collection.query_type.query_type'),
            ],
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Collection\QueryTypePass::process
     */
    public function testProcessWithCustomHandler(): void
    {
        $queryTypes = ['query_type' => ['handler' => 'custom', 'priority' => 0]];
        $this->setParameter('netgen_layouts.query_types', $queryTypes);

        $this->setDefinition('netgen_layouts.collection.query_type_factory', new Definition());
        $this->setDefinition('netgen_layouts.collection.registry.query_type', new Definition(null, [[]]));

        $queryTypeHandler = new Definition(stdClass::class);
        $queryTypeHandler->addTag('netgen_layouts.query_type_handler', ['type' => 'custom']);
        $this->setDefinition('netgen_layouts.collection.query_type.handler.test', $queryTypeHandler);

        $this->compile();

        $this->assertContainerBuilderHasService(
            'netgen_layouts.collection.query_type.query_type',
        );

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'netgen_layouts.collection.registry.query_type',
            0,
            [
                'query_type' => new Reference('netgen_layouts.collection.query_type.query_type'),
            ],
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Collection\QueryTypePass::process
     */
    public function testProcessThrowsExceptionWithNoTagType(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Query type handler for "query_type" query type does not exist.');

        $queryTypes = ['query_type' => ['priority' => 0]];
        $this->setParameter('netgen_layouts.query_types', $queryTypes);

        $this->setDefinition('netgen_layouts.collection.registry.query_type', new Definition());

        $queryTypeHandler = new Definition(stdClass::class);
        $queryTypeHandler->addTag('netgen_layouts.query_type_handler');
        $this->setDefinition('netgen_layouts.collection.query_type.handler.test', $queryTypeHandler);

        $this->compile();
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Collection\QueryTypePass::process
     */
    public function testProcessThrowsExceptionWithNoHandler(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Query type handler for "query_type" query type does not exist.');

        $queryTypes = ['query_type' => ['priority' => 0]];
        $this->setParameter('netgen_layouts.query_types', $queryTypes);

        $this->setDefinition('netgen_layouts.collection.registry.query_type', new Definition());

        $queryTypeHandler = new Definition(stdClass::class);
        $queryTypeHandler->addTag('netgen_layouts.query_type_handler', ['type' => 'other']);
        $this->setDefinition('netgen_layouts.collection.query_type.handler.test', $queryTypeHandler);

        $this->compile();
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Collection\QueryTypePass::process
     */
    public function testProcessThrowsExceptionWithNoCustomHandler(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Query type handler for "query_type" query type does not exist.');

        $queryTypes = ['query_type' => ['handler' => 'custom', 'priority' => 0]];
        $this->setParameter('netgen_layouts.query_types', $queryTypes);

        $this->setDefinition('netgen_layouts.collection.registry.query_type', new Definition());

        $queryTypeHandler = new Definition(stdClass::class);
        $queryTypeHandler->addTag('netgen_layouts.query_type_handler', ['type' => 'other']);
        $this->setDefinition('netgen_layouts.collection.query_type.handler.test', $queryTypeHandler);

        $this->compile();
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Collection\QueryTypePass::process
     */
    public function testProcessWithEmptyContainer(): void
    {
        $this->compile();

        self::assertInstanceOf(FrozenParameterBag::class, $this->container->getParameterBag());
    }
}
