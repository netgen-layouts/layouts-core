<?php

namespace Netgen\BlockManager\Tests\View\View;

use Netgen\BlockManager\Core\Values\LayoutResolver\Rule;
use Netgen\BlockManager\View\View\RuleView;
use PHPUnit\Framework\TestCase;

class RuleViewTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\API\Values\LayoutResolver\Rule
     */
    protected $rule;

    /**
     * @var \Netgen\BlockManager\View\View\RuleViewInterface
     */
    protected $view;

    public function setUp()
    {
        $this->rule = new Rule(array('id' => 42));

        $this->view = new RuleView($this->rule);
        $this->view->addParameters(array('param' => 'value'));
        $this->view->addParameters(array('rule' => 42));
    }

    /**
     * @covers \Netgen\BlockManager\View\View\RuleView::__construct
     * @covers \Netgen\BlockManager\View\View\RuleView::getRule
     */
    public function testGetRule()
    {
        self::assertEquals($this->rule, $this->view->getRule());
        self::assertEquals(
            array(
                'param' => 'value',
                'rule' => $this->rule,
            ),
            $this->view->getParameters()
        );
    }

    /**
     * @covers \Netgen\BlockManager\View\View\RuleView::getIdentifier
     */
    public function testGetIdentifier()
    {
        self::assertEquals('rule_view', $this->view->getIdentifier());
    }
}
