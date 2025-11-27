<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsDebugBundle\DataCollector;

use Jean85\PrettyVersions;
use Netgen\Bundle\LayoutsBundle\Templating\Twig\GlobalVariable;
use Netgen\Layouts\API\Values\LayoutResolver\Rule;
use Netgen\Layouts\Persistence\Doctrine\Handler\LayoutHandler;
use Netgen\Layouts\Persistence\Values\Status;
use Netgen\Layouts\View\View\BlockViewInterface;
use Netgen\Layouts\View\View\LayoutViewInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Throwable;
use Twig\Environment;
use Twig\Source;
use Version\Exception\InvalidVersionString;
use Version\Version;

use function is_array;
use function sprintf;

final class LayoutsDataCollector extends DataCollector
{
    /**
     * @var \Netgen\Layouts\Persistence\Values\Layout\Layout[]
     */
    private array $layoutCache = [];

    public function __construct(
        private LayoutHandler $layoutHandler,
        private GlobalVariable $globalVariable,
        private LayoutUrlGeneratorInterface $layoutUrlGenerator,
        private Environment $twig,
        string $edition,
    ) {
        $coreVersion = PrettyVersions::getVersion('netgen/layouts-core')->getPrettyVersion();
        $this->data['version'] = sprintf('%s %s', $coreVersion, $edition);
        $this->data['docs_version'] = 'latest';

        try {
            $version = Version::fromString($coreVersion);
            $this->data['docs_version'] = sprintf('%d.%d', $version->getMajor(), $version->getMinor());
        } catch (InvalidVersionString) {
            // Do nothing
        }

        $this->reset();
    }

    public function collect(Request $request, Response $response, ?Throwable $exception = null): void
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

    public function reset(): void
    {
        $this->data['rule'] = null;
        $this->data['layout'] = null;
        $this->data['blocks'] = [];
    }

    /**
     * Collects the layout data.
     */
    public function collectLayout(LayoutViewInterface $layoutView): void
    {
        $layout = $layoutView->getLayout();
        $template = $layoutView->getTemplate();

        $this->data['layout'] = [
            'id' => $layout->id->toString(),
            'path' => $this->layoutUrlGenerator->generateLayoutUrl($layout->id),
            'name' => $layout->name,
            'type' => $layout->layoutType->name,
            'context' => $layoutView->getContext(),
            'template' => null,
            'template_path' => null,
        ];

        if ($template !== null) {
            $templateSource = $this->getTemplateSource($template);

            $this->data['layout']['template'] = $templateSource->getName();
            $this->data['layout']['template_path'] = $templateSource->getPath();
        }
    }

    /**
     * Collects the rule data.
     */
    public function collectRule(Rule $rule): void
    {
        $ruleData = [
            'id' => $rule->id->toString(),
        ];

        foreach ($rule->targets as $target) {
            $ruleData['targets'][] = [
                'type' => $target->targetType::getType(),
                'value' => $this->cloneVar($target->value),
            ];
        }

        foreach ($rule->conditions as $condition) {
            $ruleData['conditions'][] = [
                'type' => $condition->conditionType::getType(),
                'value' => $this->cloneVar($condition->value),
            ];
        }

        $this->data['rule'] = $ruleData;
    }

    /**
     * Collects the block view data.
     */
    public function collectBlockView(BlockViewInterface $blockView): void
    {
        $block = $blockView->getBlock();
        $blockDefinition = $block->definition;
        $template = $blockView->getTemplate();

        $layoutId = $block->layoutId->toString();
        $this->layoutCache[$layoutId] ??= $this->layoutHandler->loadLayout(
            $block->layoutId,
            Status::from($block->status->value),
        );

        $blockData = [
            'id' => $block->id->toString(),
            'name' => $block->name,
            'layout_id' => $layoutId,
            'layout_path' => $this->layoutUrlGenerator->generateLayoutUrl($block->layoutId),
            'layout_name' => $this->layoutCache[$layoutId]->name,
            'definition' => $blockDefinition->getName(),
            'view_type' => $blockDefinition->hasViewType($block->viewType, $block) ?
                $blockDefinition->getViewType($block->viewType, $block)->name :
                'Invalid view type',
            'locale' => $block->locale,
            'template' => null,
            'template_path' => null,
        ];

        if ($template !== null) {
            $templateSource = $this->getTemplateSource($template);

            $blockData['template'] = $templateSource->getName();
            $blockData['template_path'] = $templateSource->getPath();
        }

        $this->data['blocks'][] = $blockData;
    }

    /**
     * Returns the collected data.
     *
     * @return mixed[]
     */
    public function getData(): array
    {
        return is_array($this->data) ? $this->data : [];
    }

    public function getName(): string
    {
        return 'nglayouts';
    }

    private function getTemplateSource(string $name): Source
    {
        return $this->twig->load($name)->getSourceContext();
    }
}
