<?php

namespace Netgen\Bundle\BlockManagerAdminUIBundle\Controller\App;

use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\BlockManager\API\Values\LayoutCreateStruct;
use Netgen\BlockManager\Layout\Form\CreateType;
use Netgen\BlockManager\View\ViewInterface;
use Netgen\Bundle\BlockManagerBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class LayoutController extends Controller
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
     * Displays and processes layout create form.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @throws \Netgen\BlockManager\Exception\InvalidArgumentException If query does not support the specified form
     *
     * @return \Netgen\BlockManager\Serializer\Values\View
     */
    public function layoutCreateForm(Request $request)
    {
        $createStruct = new LayoutCreateStruct();

        $form = $this->createForm(
            CreateType::class,
            $createStruct,
            array(
                'action' => $this->generateUrl(
                    'netgen_block_manager_app_layout_form_create'
                ),
            )
        );

        $form->handleRequest($request);

        if ($request->getMethod() !== Request::METHOD_POST) {
            return $this->buildView($form);
        }

        if ($form->isValid()) {
            $createdLayout = $this->layoutService->createLayout($createStruct);

            return new JsonResponse(
                array(
                    'id' => $createdLayout->getId(),
                ),
                Response::HTTP_CREATED
            );
        }

        return $this->buildView(
            $form,
            array(),
            ViewInterface::CONTEXT_VIEW,
            new Response(null, Response::HTTP_UNPROCESSABLE_ENTITY)
        );
    }
}
