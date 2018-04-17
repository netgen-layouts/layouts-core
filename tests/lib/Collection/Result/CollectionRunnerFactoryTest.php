<?php

namespace Netgen\BlockManager\Tests\Collection\Result;

use Netgen\BlockManager\Collection\Result\CollectionRunnerFactory;
use Netgen\BlockManager\Collection\Result\DynamicCollectionRunner;
use Netgen\BlockManager\Collection\Result\ManualCollectionRunner;
use Netgen\BlockManager\Core\Values\Collection\Collection;
use Netgen\BlockManager\Core\Values\Collection\Query;
use Netgen\BlockManager\Item\ItemBuilderInterface;
use Netgen\BlockManager\Tests\Collection\Stubs\QueryType;
use PHPUnit\Framework\TestCase;

final class CollectionRunnerFactoryTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $itemBuilderMock;

    /**
     * @var \Netgen\BlockManager\Collection\Result\CollectionRunnerFactory
     */
    private $factory;

    public function setUp()
    {
        $this->itemBuilderMock = $this->createMock(ItemBuilderInterface::class);

        $this->factory = new CollectionRunnerFactory($this->itemBuilderMock, 12);
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Result\CollectionRunnerFactory::__construct
     * @covers \Netgen\BlockManager\Collection\Result\CollectionRunnerFactory::getCollectionRunner
     * @covers \Netgen\BlockManager\Collection\Result\CollectionRunnerFactory::getQueryRunner
     */
    public function testGetCollectionRunnerWithManualCollection()
    {
        $runner = $this->factory->getCollectionRunner(new Collection());

        $this->assertInstanceOf(ManualCollectionRunner::class, $runner);
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Result\CollectionRunnerFactory::__construct
     * @covers \Netgen\BlockManager\Collection\Result\CollectionRunnerFactory::getCollectionRunner
     * @covers \Netgen\BlockManager\Collection\Result\CollectionRunnerFactory::getQueryRunner
     */
    public function testGetCollectionRunnerWithDynamicCollection()
    {
        $runner = $this->factory->getCollectionRunner(
            new Collection(
                array(
                    'query' => new Query(
                        array(
                            'queryType' => new QueryType('type'),
                        )
                    ),
                )
            )
        );

        $this->assertInstanceOf(DynamicCollectionRunner::class, $runner);
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Result\CollectionRunnerFactory::__construct
     * @covers \Netgen\BlockManager\Collection\Result\CollectionRunnerFactory::getCollectionRunner
     * @covers \Netgen\BlockManager\Collection\Result\CollectionRunnerFactory::getQueryRunner
     */
    public function testGetCollectionRunnerWithDynamicContextualCollection()
    {
        $runner = $this->factory->getCollectionRunner(
            new Collection(
                array(
                    'query' => new Query(
                        array(
                            'queryType' => new QueryType('type', array(), null, true),
                        )
                    ),
                )
            ),
            PHP_INT_MAX
        );

        $this->assertInstanceOf(DynamicCollectionRunner::class, $runner);
    }
}
