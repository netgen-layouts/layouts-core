<?php

namespace Netgen\Bundle\BlockManagerAdminBundle\Controller\Admin;

use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\BlockManager\API\Values\Page\LayoutInfo;
use Netgen\BlockManager\View\ViewInterface;
use Netgen\Bundle\BlockManagerBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class LayoutsController extends Controller
{
    /**
     * @var \Netgen\BlockManager\API\Service\LayoutService
     */
    protected $layoutService;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\API\Service\LayoutService $layoutService
     */
    public function __construct(LayoutService $layoutService)
    {
        $this->layoutService = $layoutService;
    }

    /**
     * Displays the index page of shared layouts admin interface.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index()
    {
        return $this->render(
            'NetgenBlockManagerAdminBundle:admin/layouts:index.html.twig',
            array(
                'layouts' => $this->layoutService->loadLayouts(),
            )
        );
    }

    /**
     * Copies a layout.
     *
     * @param \Netgen\BlockManager\API\Values\Page\LayoutInfo $layout
     *
     * @return \Netgen\BlockManager\View\ViewInterface
     */
    public function copyLayout(LayoutInfo $layout)
    {
        $copiedLayout = $this->layoutService->copyLayout($layout);

        return $this->buildView($copiedLayout, array(), ViewInterface::CONTEXT_ADMIN);
    }

    /**
     * Deletes a layout.
     *
     * @param \Netgen\BlockManager\API\Values\Page\LayoutInfo $layout
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteLayout(LayoutInfo $layout)
    {
        $this->layoutService->deleteLayout($layout);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
