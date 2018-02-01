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
use Twig\Environment;

final class BlockManagerDataCollector extends DataCollector
{
    /**
     * @var \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable
     */
    private $globalVariable;

    /**
     * @var \Twig\Environment
     */
    private $twig;

    public function __construct(GlobalVariable $globalVariable, Environment $twig)
    {
        $this->globalVariable = $globalVariable;
        $this->twig = $twig;

        $this->data['version'] = Version::VERSION;

        $this->reset();
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

    public function reset()
    {
        $this->data['rule'] = null;
        $this->data['layout'] = null;
        $this->data['blocks'] = array();
    }

    /**
     * Collects the layout data.
     *
     * @param \Netgen\BlockManager\View\View\LayoutViewInterface $layoutView
     */
    public function collectLayout(LayoutViewInterface $layoutView)
    {
        $layout = $layoutView->getLayout();
        $templateSource = $this->getTemplateSource($layoutView->getTemplate());

        $this->data['layout'] = array(
            'id' => $layout->getId(),
            'name' => $layout->getName(),
            'type' => $layout->getLayoutType()->getName(),
            'context' => $layoutView->getContext(),
            'template' => $templateSource->getName(),
            'template_path' => $templateSource->getPath(),
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
        $templateSource = $this->getTemplateSource($blockView->getTemplate());

        $this->data['blocks'][] = array(
            'id' => $block->getId(),
            'layout_id' => $block->getLayoutId(),
            'definition' => $blockDefinition->getName(),
            'view_type' => $blockDefinition->getViewType(
                $block->getViewType()
            )->getName(),
            'locale' => $block->getLocale(),
            'template' => $templateSource->getName(),
            'template_path' => $templateSource->getPath(),
        );
    }

    /**
     * Returns the collected data.
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

    /**
     * @param string $name
     *
     * @return \Twig\Source
     */
    private function getTemplateSource($name)
    {
        return $this->twig->load($name)->getSourceContext();
    }
}
