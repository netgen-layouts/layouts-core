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
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

use const PHP_INT_MAX;

final class CollectionRunnerFactoryTest extends TestCase
{
    private MockObject $cmsItemBuilderMock;

    private CollectionRunnerFactory $factory;

    protected function setUp(): void
    {
        $this->cmsItemBuilderMock = $this->createMock(CmsItemBuilderInterface::class);

        $this->factory = new CollectionRunnerFactory($this->cmsItemBuilderMock, new VisibilityResolver([]));
    }

    /**
     * @covers \Netgen\Layouts\Collection\Result\CollectionRunnerFactory::__construct
     * @covers \Netgen\Layouts\Collection\Result\CollectionRunnerFactory::getCollectionRunner
     * @covers \Netgen\Layouts\Collection\Result\CollectionRunnerFactory::getQueryRunner
     */
    public function testGetCollectionRunnerWithManualCollection(): void
    {
        $runner = $this->factory->getCollectionRunner(new Collection());

        self::assertInstanceOf(ManualCollectionRunner::class, $runner);
    }

    /**
     * @covers \Netgen\Layouts\Collection\Result\CollectionRunnerFactory::__construct
     * @covers \Netgen\Layouts\Collection\Result\CollectionRunnerFactory::getCollectionRunner
     * @covers \Netgen\Layouts\Collection\Result\CollectionRunnerFactory::getQueryRunner
     */
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

    /**
     * @covers \Netgen\Layouts\Collection\Result\CollectionRunnerFactory::__construct
     * @covers \Netgen\Layouts\Collection\Result\CollectionRunnerFactory::getCollectionRunner
     * @covers \Netgen\Layouts\Collection\Result\CollectionRunnerFactory::getQueryRunner
     */
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
