<?php

namespace Netgen\Bundle\BlockManagerAdminBundle\Controller\Admin;

use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\BlockManager\Exception\NotFoundException;
use Netgen\BlockManager\Layout\Form\CopyType;
use Netgen\BlockManager\View\ViewInterface;
use Netgen\Bundle\BlockManagerBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
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
                'layouts' => $this->layoutService->loadLayouts(true),
            )
        );
    }

    /**
     * Copies a layout.
     *
     * @param int $layoutId
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Netgen\BlockManager\View\ViewInterface
     */
    public function copyLayout($layoutId, Request $request)
    {
        $layout = $this->loadLayout($layoutId);

        $form = $this->createForm(
            CopyType::class,
            array('name' => $layout->getName() . ' (copy)'),
            array(
                'layout' => $layout,
                'action' => $this->generateUrl(
                    'ngbm_admin_layouts_layout_copy',
                    array(
                        'layoutId' => $layout->getId(),
                    )
                ),
            )
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $copiedLayout = $this->layoutService->copyLayout(
                $layout,
                $form->getData()['name']
            );

            return $this->buildView($copiedLayout, array(), ViewInterface::CONTEXT_ADMIN);
        }

        return $this->buildView($form, array(), ViewInterface::CONTEXT_ADMIN);
    }

    /**
     * Deletes a layout.
     *
     * @param int $layoutId
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteLayout($layoutId)
    {
        $layout = $this->loadLayout($layoutId);
        $this->layoutService->deleteLayout($layout);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Loads either published or draft state of the layout.
     *
     * @param int|string $layoutId
     *
     * @return \Netgen\BlockManager\API\Values\Page\Layout|\Netgen\BlockManager\API\Values\Page\LayoutDraft
     */
    protected function loadLayout($layoutId)
    {
        try {
            return $this->layoutService->loadLayout($layoutId);
        } catch (NotFoundException $e) {
            // Do nothing
        }

        return $this->layoutService->loadLayoutDraft($layoutId);
    }
}
