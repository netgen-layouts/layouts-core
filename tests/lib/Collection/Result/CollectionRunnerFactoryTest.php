<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Collection\Result;

use Netgen\Layouts\API\Values\Collection\Collection;
use Netgen\Layouts\API\Values\Collection\Query;
use Netgen\Layouts\Collection\Item\VisibilityResolver;
use Netgen\Layouts\Collection\Result\CollectionRunnerFactory;
use Netgen\Layouts\Collection\Result\DynamicCollectionRunner;
use Netgen\Layouts\Collection\Result\ManualCollectionRunner;
use Netgen\Layouts\Item\CmsItemBuilderInterface;
use Netgen\Layouts\Tests\Collection\Stubs\QueryType;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

use const PHP_INT_MAX;

#[CoversClass(CollectionRunnerFactory::class)]
final class CollectionRunnerFactoryTest extends TestCase
{
    private CollectionRunnerFactory $factory;

    protected function setUp(): void
    {
        $cmsItemBuilderStub = self::createStub(CmsItemBuilderInterface::class);

        $this->factory = new CollectionRunnerFactory($cmsItemBuilderStub, new VisibilityResolver([]));
    }

    public function testGetCollectionRunnerWithManualCollection(): void
    {
        $runner = $this->factory->getCollectionRunner(Collection::fromArray(['query' => null]));

        self::assertInstanceOf(ManualCollectionRunner::class, $runner);
    }

    public function testGetCollectionRunnerWithDynamicCollection(): void
    {
        $runner = $this->factory->getCollectionRunner(
            Collection::fromArray(
                [
                    'query' => Query::fromArray(
                        [
                            'queryType' => new QueryType('type'),
                        ],
                    ),
                ],
            ),
        );

        self::assertInstanceOf(DynamicCollectionRunner::class, $runner);
    }

    public function testGetCollectionRunnerWithDynamicContextualCollection(): void
    {
        $runner = $this->factory->getCollectionRunner(
            Collection::fromArray(
                [
                    'query' => Query::fromArray(
                        [
                            'queryType' => new QueryType('type', [], null, true),
                        ],
                    ),
                ],
            ),
            PHP_INT_MAX,
        );

        self::assertInstanceOf(DynamicCollectionRunner::class, $runner);
    }
}
