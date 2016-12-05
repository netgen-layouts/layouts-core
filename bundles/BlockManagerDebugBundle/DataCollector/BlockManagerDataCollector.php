<?php

namespace Netgen\Bundle\BlockManagerDebugBundle\DataCollector;

use Exception;
use Netgen\BlockManager\API\Repository;
use Netgen\BlockManager\API\Values\LayoutResolver\Rule;
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
    protected $globalVariable;

    /**
     * @var \Netgen\BlockManager\API\Repository
     */
    protected $repository;

    /**
     * @var \Netgen\BlockManager\API\Values\Page\Layout[]
     */
    protected $loadedLayouts;

    /**
     * Constructor.
     *
     * @param \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable $globalVariable
     * @param \Netgen\BlockManager\API\Repository $repository
     */
    public function __construct(GlobalVariable $globalVariable, Repository $repository)
    {
        $this->globalVariable = $globalVariable;
        $this->repository = $repository;

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
        $layoutView = $this->globalVariable->getLayoutView();

        if ($rule instanceof Rule) {
            $this->collectRule($rule);
        }

        if ($layoutView instanceof LayoutViewInterface) {
            $this->collectLayout($layoutView);
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

        $layoutCacheKey = $block->getStatus() . '-' . $block->getLayoutId();
        if (!isset($this->loadedLayouts[$layoutCacheKey])) {
            $method = $block->isPublished() ? 'loadLayout' : 'loadLayoutDraft';

            $this->loadedLayouts[$layoutCacheKey] = $this
                ->repository
                ->getLayoutService()
                ->$method($block->getLayoutId());
        }

        $layout = $this->loadedLayouts[$layoutCacheKey];
        $blockDefinition = $block->getBlockDefinition();

        $this->data['blocks'][] = array(
            'id' => $block->getId(),
            'layout_id' => $layout->getId(),
            'layout_name' => $layout->getName(),
            'layout_type' => $layout->getLayoutType()->getName(),
            'layout_shared' => $layout->isShared(),
            'zone' => $layout->getLayoutType()->getZone(
                $block->getZoneIdentifier()
            )->getName(),
            'definition' => $blockDefinition->getConfig()->getName(),
            'view_type' => $blockDefinition->getConfig()->getViewType(
                $block->getViewType()
            )->getName(),
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
