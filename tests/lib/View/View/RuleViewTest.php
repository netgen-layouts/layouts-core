<?php

namespace Netgen\BlockManager\Tests\View\View;

use Netgen\BlockManager\Core\Values\LayoutResolver\Rule;
use Netgen\BlockManager\View\View\RuleView;
use PHPUnit\Framework\TestCase;

final class RuleViewTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\API\Values\LayoutResolver\Rule
     */
    private $rule;

    /**
     * @var \Netgen\BlockManager\View\View\RuleViewInterface
     */
    private $view;

    public function setUp()
    {
        $this->rule = new Rule(array('id' => 42));

        $this->view = new RuleView(
            array(
                'rule' => $this->rule,
            )
        );

        $this->view->addParameter('param', 'value');
        $this->view->addParameter('rule', 42);
    }

    /**
     * @covers \Netgen\BlockManager\View\View\RuleView::__construct
     * @covers \Netgen\BlockManager\View\View\RuleView::getRule
     */
    public function testGetRule()
    {
        $this->assertEquals($this->rule, $this->view->getRule());
        $this->assertEquals(
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
        $this->assertEquals('rule_view', $this->view->getIdentifier());
    }
}
