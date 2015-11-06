<?php

namespace Netgen\BlockManager\View\Builder;

use Netgen\BlockManager\API\Service\BlockService;
use Netgen\BlockManager\API\Values\Value;
use Netgen\BlockManager\API\Values\Page\Layout;
use Netgen\BlockManager\View\LayoutView;
use InvalidArgumentException;

class LayoutViewBuilder implements ViewBuilder
{
    /**
     * @var \Netgen\BlockManager\API\Service\BlockService
     */
    protected $blockService;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\API\Service\BlockService $blockService
     */
    public function __construct(BlockService $blockService)
    {
        $this->blockService = $blockService;
    }

    /**
     * Builds the view.
     *
     * @param \Netgen\BlockManager\API\Values\Value $value
     * @param array $parameters
     * @param string $context
     *
     * @throws \InvalidArgumentException If value is of unsupported type
     *
     * @return \Netgen\BlockManager\View\ViewInterface
     */
    public function buildView(Value $value, array $parameters = array(), $context = 'view')
    {
        if (!$value instanceof Layout) {
            throw new InvalidArgumentException('Layout view builder accepts only Layout value objects to build from');
        }

        $layoutView = new LayoutView();
        $layoutView->setLayout($value);
        $layoutView->setContext($context);

        $parameters['blocks'] = $this->blockService->loadLayoutBlocks($value);
        $layoutView->setParameters($parameters);

        return $layoutView;
    }
}
