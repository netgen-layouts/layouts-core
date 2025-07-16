<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsDebugBundle\DataCollector;

use Jean85\PrettyVersions;
use Netgen\Bundle\LayoutsBundle\Templating\Twig\GlobalVariable;
use Netgen\Layouts\API\Values\LayoutResolver\Rule;
use Netgen\Layouts\Persistence\Doctrine\Handler\LayoutHandler;
use Netgen\Layouts\View\View\BlockViewInterface;
use Netgen\Layouts\View\View\LayoutViewInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Twig\Environment;
use Twig\Source;
use Version\Exception\InvalidVersionString;
use Version\Version;

use function is_array;
use function sprintf;

final class LayoutsDataCollector extends DataCollector
{
    private LayoutHandler $layoutHandler;

    private GlobalVariable $globalVariable;

    private LayoutUrlGeneratorInterface $layoutUrlGenerator;

    private Environment $twig;

    /**
     * @var \Netgen\Layouts\Persistence\Values\Layout\Layout[]
     */
    private array $layoutCache = [];

    public function __construct(
        LayoutHandler $layoutHandler,
        GlobalVariable $globalVariable,
        LayoutUrlGeneratorInterface $layoutUrlGenerator,
        Environment $twig,
        string $edition
    ) {
        $this->layoutHandler = $layoutHandler;
        $this->globalVariable = $globalVariable;
        $this->layoutUrlGenerator = $layoutUrlGenerator;
        $this->twig = $twig;

        $coreVersion = PrettyVersions::getVersion('netgen/layouts-core')->getPrettyVersion();
        $this->data['version'] = sprintf('%s %s', $coreVersion, $edition);
        $this->data['docs_version'] = 'latest';

        try {
            $version = Version::fromString($coreVersion);
            $this->data['docs_version'] = sprintf('%d.%d', $version->getMajor(), $version->getMinor());
        } catch (InvalidVersionString $e) {
            // Do nothing
        }

        $this->reset();
    }

    /**
     * @param \Throwable|null $exception
     */
    public function collect(Request $request, Response $response, $exception = null): void
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
            'id' => $layout->getId()->toString(),
            'path' => $this->layoutUrlGenerator->generateLayoutUrl($layout->getId()),
            'name' => $layout->getName(),
            'type' => $layout->getLayoutType()->getName(),
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
            'id' => $rule->getId()->toString(),
        ];

        foreach ($rule->getTargets() as $target) {
            $ruleData['targets'][] = [
                'type' => $target->getTargetType()::getType(),
                'value' => $this->cloneVar($target->getValue()),
            ];
        }

        foreach ($rule->getConditions() as $condition) {
            $ruleData['conditions'][] = [
                'type' => $condition->getConditionType()::getType(),
                'value' => $this->cloneVar($condition->getValue()),
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
        $blockDefinition = $block->getDefinition();
        $template = $blockView->getTemplate();

        $layoutId = $block->getLayoutId()->toString();
        $this->layoutCache[$layoutId] ??= $this->layoutHandler->loadLayout(
            $block->getLayoutId(),
            $block->getStatus(),
        );

        $blockData = [
            'id' => $block->getId()->toString(),
            'name' => $block->getName(),
            'layout_id' => $layoutId,
            'layout_path' => $this->layoutUrlGenerator->generateLayoutUrl($block->getLayoutId()),
            'layout_name' => $this->layoutCache[$layoutId]->name,
            'definition' => $blockDefinition->getName(),
            'view_type' => $blockDefinition->hasViewType($block->getViewType(), $block) ?
                $blockDefinition->getViewType($block->getViewType(), $block)->getName() :
                'Invalid view type',
            'locale' => $block->getLocale(),
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
