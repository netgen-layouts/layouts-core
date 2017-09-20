<?php

namespace Netgen\Bundle\BlockManagerDebugBundle\DataCollector;

use Exception;
use Netgen\BlockManager\API\Values\LayoutResolver\Rule;
use Netgen\BlockManager\Version;
use Netgen\BlockManager\View\View\BlockViewInterface;
use Netgen\BlockManager\View\View\LayoutViewInterface;
use Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

class BlockManagerDataCollector extends DataCollector
{
    /**
     * @var \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable
     */
    private $globalVariable;

    public function __construct(GlobalVariable $globalVariable)
    {
        $this->globalVariable = $globalVariable;

        $this->data['rule'] = null;
        $this->data['layout'] = null;
        $this->data['blocks'] = array();
        $this->data['version'] = Version::VERSION;
    }

    public function collect(Request $request, Response $response, Exception $exception = null)
    {
        $rule = $this->globalVariable->getRule();
        $layoutView = $this->globalVariable->getLayoutView();

        if ($rule instanceof Rule) {
            $this->collectRule($rule);
        }

        if ($layoutView instanceof LayoutViewInterface) {
            $this->collectLayout($layoutView);
        } elseif ($layoutView === false) {
            $this->data['layout'] = false;
        }
    }

    /**
     * Collects the layout data.
     *
     * @param \Netgen\BlockManager\View\View\LayoutViewInterface $layoutView
     */
    public function collectLayout(LayoutViewInterface $layoutView)
    {
        $layout = $layoutView->getLayout();

        $this->data['layout'] = array(
            'id' => $layout->getId(),
            'name' => $layout->getName(),
            'type' => $layout->getLayoutType()->getName(),
            'context' => $layoutView->getContext(),
            'template' => $layoutView->getTemplate(),
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
        $block = $blockView->getBlock();
        $blockDefinition = $block->getDefinition();

        $this->data['blocks'][] = array(
            'id' => $block->getId(),
            'layout_id' => $block->getLayoutId(),
            'definition' => $blockDefinition->getConfig()->getName(),
            'view_type' => $blockDefinition->getConfig()->getViewType(
                $block->getViewType()
            )->getName(),
            'locale' => $block->getTranslation()->getLocale(),
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

    public function getName()
    {
        return 'ngbm';
    }
}
