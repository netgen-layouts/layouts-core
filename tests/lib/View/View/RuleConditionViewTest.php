<?php

namespace Netgen\BlockManager\Tests\View\View;

use Netgen\BlockManager\Core\Values\LayoutResolver\Condition;
use Netgen\BlockManager\View\View\RuleConditionView;
use PHPUnit\Framework\TestCase;

final class RuleConditionViewTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\API\Values\LayoutResolver\Condition
     */
    private $condition;

    /**
     * @var \Netgen\BlockManager\View\View\RuleConditionViewInterface
     */
    private $view;

    public function setUp()
    {
        $this->condition = new Condition(array('id' => 42));

        $this->view = new RuleConditionView(
            array(
                'condition' => $this->condition,
            )
        );

        $this->view->addParameter('param', 'value');
        $this->view->addParameter('condition', 42);
    }

    /**
     * @covers \Netgen\BlockManager\View\View\RuleConditionView::__construct
     * @covers \Netgen\BlockManager\View\View\RuleConditionView::getCondition
     */
    public function testGetCondition()
    {
        $this->assertEquals($this->condition, $this->view->getCondition());
        $this->assertEquals(
            array(
                'param' => 'value',
                'condition' => $this->condition,
            ),
            $this->view->getParameters()
        );
    }

    /**
     * @covers \Netgen\BlockManager\View\View\RuleConditionView::getIdentifier
     */
    public function testGetIdentifier()
    {
        $this->assertEquals('rule_condition_view', $this->view->getIdentifier());
    }

    /**
     * @covers \Netgen\BlockManager\View\View\RuleConditionView::jsonSerialize
     */
    public function testJsonSerialize()
    {
        $this->assertEquals(
            array(
                'conditionId' => 42,
            ),
            $this->view->jsonSerialize()
        );
    }
}
