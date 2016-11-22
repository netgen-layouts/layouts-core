<?php

namespace Netgen\Bundle\BlockManagerDebugBundle\DataCollector;

use Netgen\BlockManager\API\Values\Page\Layout;
use Netgen\BlockManager\API\Values\LayoutResolver\Rule;
use Netgen\BlockManager\View\View\BlockViewInterface;
use Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Exception;

class BlockManagerDataCollector extends DataCollector
{
    /**
     * @var \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable
     */
    protected $globalVariable;

    /**
     * Constructor.
     *
     * @param \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable $globalVariable
     */
    public function __construct(GlobalVariable $globalVariable)
    {
        $this->globalVariable = $globalVariable;

        $this->data['rule'] = null;
        $this->data['layout'] = null;
        $this->data['blocks'] = array();
    }

    /**
     * Collects data for the given Request and Response.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Symfony\Component\HttpFoundation\Response $response
     * @param \Exception $exception
     */
    public function collect(Request $request, Response $response, Exception $exception = null)
    {
        $rule = $this->globalVariable->getRule();
        $layout = $this->globalVariable->getLayout();

        if ($rule instanceof Rule) {
            $this->collectRule($rule);
        }

        if ($layout instanceof Layout) {
            $this->collectLayout($layout);
        }
    }

    /**
     * Collects the layout data.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Layout $layout
     */
    public function collectLayout(Layout $layout)
    {
        $this->data['layout'] = array(
            'id' => $layout->getId(),
            'name' => $layout->getName(),
            'type' => $layout->getLayoutType()->getName(),
        );
    }

    /**
     * Collects the rule data.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\Rule $rule
     */
    public function collectRule(Rule $rule)
    {
        $this->data['rule'] = array(
            'id' => $rule->getId(),
        );

        foreach ($rule->getTargets() as $target) {
            $this->data['rule']['targets'][] = array(
                'type' => $target->getTargetType()->getType(),
                'value' => $target->getValue(),
            );
        }

        foreach ($rule->getConditions() as $condition) {
            $this->data['rule']['conditions'][] = array(
                'type' => $condition->getConditionType()->getType(),
                'value' => $condition->getValue(),
            );
        }
    }

    /**
     * Collects the block view data.
     *
     * @param \Netgen\BlockManager\View\View\BlockViewInterface $blockView
     */
    public function collectBlockView(BlockViewInterface $blockView)
    {
        $this->data['blocks'][] = array(
            'id' => $blockView->getBlock()->getId(),
            'layout_id' => $blockView->getBlock()->getLayoutId(),
            'definition' => $blockView->getBlock()->getBlockDefinition()->getIdentifier(),
            'view_type' => $blockView->getBlock()->getViewType(),
            'name' => $blockView->getBlock()->getName(),
            'template' => $blockView->getTemplate(),
        );
    }

    /**
     * Returns the resolved layout.
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Returns the name of the collector.
     *
     * @return string The collector name
     */
    public function getName()
    {
        return 'ngbm';
    }
}
