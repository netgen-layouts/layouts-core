<?php

namespace Netgen\Bundle\BlockManagerAdminBundle\Controller\Admin;

use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\BlockManager\API\Values\Layout\Layout;
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
     * @param \Netgen\BlockManager\API\Values\Layout\Layout $layout
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Netgen\BlockManager\View\ViewInterface
     */
    public function copyLayout(Layout $layout, Request $request)
    {
        $form = $this->createForm(
            CopyType::class,
            array('name' => $layout->getName() . ' (copy)'),
            array(
                'action' => $this->generateUrl(
                    'ngbm_admin_layouts_layout_copy',
                    array(
                        'layoutId' => $layout->getId(),
                    )
                ),
            )
        );

        $form->handleRequest($request);

        if (!$form->isSubmitted()) {
            return $this->buildView($form, ViewInterface::CONTEXT_ADMIN);
        }

        if ($form->isValid()) {
            $copiedLayout = $this->layoutService->copyLayout(
                $layout,
                $form->getData()['name']
            );

            return $this->buildView($copiedLayout, ViewInterface::CONTEXT_ADMIN);
        }

        return $this->buildView(
            $form,
            ViewInterface::CONTEXT_ADMIN,
            array(),
            new Response(null, Response::HTTP_UNPROCESSABLE_ENTITY)
        );
    }

    /**
     * Deletes a layout.
     *
     * @param \Netgen\BlockManager\API\Values\Layout\Layout $layout
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteLayout(Layout $layout)
    {
        $this->layoutService->deleteLayout($layout);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
