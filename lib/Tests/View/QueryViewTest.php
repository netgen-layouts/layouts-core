<?php

namespace Netgen\BlockManager\Tests\View;

use Netgen\BlockManager\Core\Values\Collection\Query;
use Netgen\BlockManager\View\QueryView;

class QueryViewTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Netgen\BlockManager\View\QueryViewInterface
     */
    protected $view;

    public function setUp()
    {
        $this->view = new QueryView();
    }

    /**
     * @covers \Netgen\BlockManager\View\QueryView::setQuery
     * @covers \Netgen\BlockManager\View\QueryView::getQuery
     */
    public function testSetQuery()
    {
        $query = new Query(array('id' => 42));

        $this->view->setParameters(array('query' => 42));
        $this->view->setQuery($query);

        self::assertEquals($query, $this->view->getQuery());
        self::assertEquals(array('query' => $query), $this->view->getParameters());
    }

    /**
     * @covers \Netgen\BlockManager\View\QueryView::getAlias
     */
    public function testGetAlias()
    {
        self::assertEquals('query_view', $this->view->getAlias());
    }
}
