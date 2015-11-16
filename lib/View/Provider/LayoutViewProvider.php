<?php

namespace Netgen\BlockManager\View\Provider;

use Netgen\BlockManager\API\Service\BlockService;
use Netgen\BlockManager\API\Values\Value;
use Netgen\BlockManager\API\Values\Page\Layout;
use Netgen\BlockManager\View\LayoutView;

class LayoutViewProvider implements ViewProviderInterface
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
     * Provides the view.
     *
     * @param \Netgen\BlockManager\API\Values\Value $value
     *
     * @return \Netgen\BlockManager\View\ViewInterface
     */
    public function provideView(Value $value)
    {
        /** @var \Netgen\BlockManager\API\Values\Page\Layout $value */
        $layoutView = new LayoutView();

        $layoutView->setLayout($value);

        $layoutView->addParameters(
            array(
                'blocks' => $this->blockService->loadLayoutBlocks($value),
            )
        );

        return $layoutView;
    }

    /**
     * Returns if this view provider supports the given value object.
     *
     * @param \Netgen\BlockManager\API\Values\Value $value
     *
     * @return bool
     */
    public function supports(Value $value)
    {
        return $value instanceof Layout;
    }
}
