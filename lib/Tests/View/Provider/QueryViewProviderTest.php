<?php

namespace Netgen\BlockManager\Tests\View\Provider;

use Netgen\BlockManager\View\Provider\QueryViewProvider;
use Netgen\BlockManager\Core\Values\Collection\Query;
use Netgen\BlockManager\Core\Values\Page\Layout;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use Netgen\BlockManager\View\QueryViewInterface;

class QueryViewProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Netgen\BlockManager\View\Provider\ViewProviderInterface
     */
    protected $queryViewProvider;

    public function setUp()
    {
        $this->queryViewProvider = new QueryViewProvider();
    }

    /**
     * @covers \Netgen\BlockManager\View\Provider\QueryViewProvider::provideView
     */
    public function testProvideView()
    {
        $query = new Query(array('id' => 42));

        /** @var \Netgen\BlockManager\View\QueryViewInterface $view */
        $view = $this->queryViewProvider->provideView($query);

        self::assertInstanceOf(QueryViewInterface::class, $view);

        self::assertEquals($query, $view->getQuery());
        self::assertNull($view->getTemplate());
        self::assertEquals(
            array(
                'query' => $query,
            ),
            $view->getParameters()
        );
    }

    /**
     * @param \Netgen\BlockManager\API\Values\Value $value
     * @param bool $supports
     *
     * @covers \Netgen\BlockManager\View\Provider\QueryViewProvider::supports
     * @dataProvider supportsProvider
     */
    public function testSupports($value, $supports)
    {
        self::assertEquals($supports, $this->queryViewProvider->supports($value));
    }

    /**
     * Provider for {@link self::testSupports}.
     *
     * @return array
     */
    public function supportsProvider()
    {
        return array(
            array(new Value(), false),
            array(new Query(), true),
            array(new Layout(), false),
        );
    }
}
