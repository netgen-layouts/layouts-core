<?php

namespace Netgen\BlockManager\Tests\View;

use Netgen\BlockManager\Core\Values\Collection\Query;
use Netgen\BlockManager\View\QueryView;

class QueryViewTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Netgen\BlockManager\API\Values\Collection\Query
     */
    protected $query;

    /**
     * @var \Netgen\BlockManager\View\QueryViewInterface
     */
    protected $view;

    public function setUp()
    {
        $this->query = new Query(array('id' => 42));

        $this->view = new QueryView($this->query);
        $this->view->addParameters(array('param' => 'value'));
        $this->view->addParameters(array('query' => 42));
    }

    /**
     * @covers \Netgen\BlockManager\View\QueryView::__construct
     * @covers \Netgen\BlockManager\View\QueryView::getQuery
     */
    public function testGetQuery()
    {
        self::assertEquals($this->query, $this->view->getQuery());
        self::assertEquals(
            array(
                'param' => 'value',
                'query' => $this->query,
            ),
            $this->view->getParameters()
        );
    }

    /**
     * @covers \Netgen\BlockManager\View\QueryView::getAlias
     */
    public function testGetAlias()
    {
        self::assertEquals('query_view', $this->view->getAlias());
    }
}
