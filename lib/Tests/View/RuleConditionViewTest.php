<?php

namespace Netgen\BlockManager\Tests\View;

use Netgen\BlockManager\Core\Values\LayoutResolver\Condition;
use Netgen\BlockManager\View\RuleConditionView;
use PHPUnit\Framework\TestCase;

class RuleConditionViewTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\API\Values\LayoutResolver\Condition
     */
    protected $condition;

    /**
     * @var \Netgen\BlockManager\View\RuleConditionViewInterface
     */
    protected $view;

    public function setUp()
    {
        $this->condition = new Condition(array('id' => 42));

        $this->view = new RuleConditionView($this->condition);
        $this->view->addParameters(array('param' => 'value'));
        $this->view->addParameters(array('condition' => 42));
    }

    /**
     * @covers \Netgen\BlockManager\View\RuleConditionView::__construct
     * @covers \Netgen\BlockManager\View\RuleConditionView::getCondition
     */
    public function testGetCondition()
    {
        self::assertEquals($this->condition, $this->view->getCondition());
        self::assertEquals(
            array(
                'param' => 'value',
                'condition' => $this->condition,
            ),
            $this->view->getParameters()
        );
    }

    /**
     * @covers \Netgen\BlockManager\View\RuleConditionView::getAlias
     */
    public function testGetAlias()
    {
        self::assertEquals('rule_condition_view', $this->view->getAlias());
    }
}
