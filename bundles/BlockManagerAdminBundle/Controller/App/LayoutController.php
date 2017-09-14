<?php

namespace Netgen\Bundle\BlockManagerAdminBundle\Controller\App;

use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\BlockManager\API\Values\Layout\LayoutCreateStruct;
use Netgen\BlockManager\Layout\Form\AddLocaleType;
use Netgen\BlockManager\Layout\Form\CreateType;
use Netgen\BlockManager\Layout\Form\EditType;
use Netgen\BlockManager\Layout\Form\RemoveLocaleType;
use Netgen\BlockManager\Layout\Form\SetMainLocaleType;
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

    public function __construct(LayoutService $layoutService)
    {
        $this->layoutService = $layoutService;
    }

    /**
     * Displays and processes layout create form.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Netgen\BlockManager\View\ViewInterface|\Symfony\Component\HttpFoundation\JsonResponse
     */
    public function layoutCreateForm(Request $request)
    {
        $createStruct = new LayoutCreateStruct();

        $form = $this->createForm(
            CreateType::class,
            $createStruct,
            array(
                'action' => $this->generateUrl(
                    'ngbm_app_layout_form_create'
                ),
            )
        );

        $form->handleRequest($request);

        if (!$form->isSubmitted()) {
            return $this->buildView($form, ViewInterface::CONTEXT_API);
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
            ViewInterface::CONTEXT_API,
            array(),
            new Response(null, Response::HTTP_UNPROCESSABLE_ENTITY)
        );
    }

    /**
     * Displays and processes layout update form.
     *
     * @param \Netgen\BlockManager\API\Values\Layout\Layout $layout
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Netgen\BlockManager\View\ViewInterface|\Symfony\Component\HttpFoundation\Response
     */
    public function layoutEditForm(Layout $layout, Request $request)
    {
        $updateStruct = $this->layoutService->newLayoutUpdateStruct($layout);

        $form = $this->createForm(
            EditType::class,
            $updateStruct,
            array(
                'layout' => $layout,
                'action' => $this->generateUrl(
                    'ngbm_app_layout_form_edit',
                    array(
                        'layoutId' => $layout->getId(),
                    )
                ),
            )
        );

        $form->handleRequest($request);

        if (!$form->isSubmitted()) {
            return $this->buildView($form, ViewInterface::CONTEXT_API);
        }

        if ($form->isValid()) {
            $this->layoutService->updateLayout($layout, $updateStruct);

            return new Response(null, Response::HTTP_NO_CONTENT);
        }

        return $this->buildView(
            $form,
            ViewInterface::CONTEXT_API,
            array(),
            new Response(null, Response::HTTP_UNPROCESSABLE_ENTITY)
        );
    }

    /**
     * Displays and processes form for setting the main locale.
     *
     * @param \Netgen\BlockManager\API\Values\Layout\Layout $layout
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Netgen\BlockManager\View\ViewInterface|\Symfony\Component\HttpFoundation\Response
     */
    public function setMainLocaleForm(Layout $layout, Request $request)
    {
        $form = $this->createForm(
            SetMainLocaleType::class,
            null,
            array(
                'layout' => $layout,
                'action' => $this->generateUrl(
                    'ngbm_app_layout_form_main_locale',
                    array(
                        'layoutId' => $layout->getId(),
                    )
                ),
            )
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $mainLocale = $form->get('mainLocale')->getData();

            if ($mainLocale !== $layout->getMainLocale()) {
                $this->layoutService->setMainTranslation($layout, $mainLocale);
            }

            return new JsonResponse(
                array(
                    'main_locale' => $mainLocale,
                ),
                Response::HTTP_OK
            );
        }

        return $this->buildView(
            $form,
            ViewInterface::CONTEXT_API,
            array(),
            new Response(
                null,
                $form->isSubmitted() ?
                    Response::HTTP_UNPROCESSABLE_ENTITY :
                    Response::HTTP_OK
            )
        );
    }

    /**
     * Displays and processes form for adding a new locale to layout.
     *
     * @param \Netgen\BlockManager\API\Values\Layout\Layout $layout
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Netgen\BlockManager\View\ViewInterface|\Symfony\Component\HttpFoundation\Response
     */
    public function addLocaleForm(Layout $layout, Request $request)
    {
        $form = $this->createForm(
            AddLocaleType::class,
            null,
            array(
                'layout' => $layout,
                'action' => $this->generateUrl(
                    'ngbm_app_layout_form_add_locale',
                    array(
                        'layoutId' => $layout->getId(),
                    )
                ),
            )
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $locale = $form->get('locale')->getData();
            $sourceLocale = $form->get('sourceLocale')->getData();

            $this->layoutService->addTranslation($layout, $locale, $sourceLocale);

            return new JsonResponse(
                array(
                    'locale' => $locale,
                ),
                Response::HTTP_CREATED
            );
        }

        return $this->buildView(
            $form,
            ViewInterface::CONTEXT_API,
            array(),
            new Response(
                null,
                $form->isSubmitted() ?
                    Response::HTTP_UNPROCESSABLE_ENTITY :
                    Response::HTTP_OK
            )
        );
    }

    /**
     * Displays and processes form for removing locales from the layout.
     *
     * @param \Netgen\BlockManager\API\Values\Layout\Layout $layout
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Netgen\BlockManager\View\ViewInterface|\Symfony\Component\HttpFoundation\Response
     */
    public function removeLocaleForm(Layout $layout, Request $request)
    {
        $form = $this->createForm(
            RemoveLocaleType::class,
            null,
            array(
                'layout' => $layout,
                'action' => $this->generateUrl(
                    'ngbm_app_layout_form_remove_locale',
                    array(
                        'layoutId' => $layout->getId(),
                    )
                ),
            )
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($form->get('locales')->getData() as $locale) {
                if ($locale !== $layout->getMainLocale()) {
                    $this->layoutService->removeTranslation($layout, $locale);
                }
            }

            return new Response(null, Response::HTTP_NO_CONTENT);
        }

        return $this->buildView(
            $form,
            ViewInterface::CONTEXT_API,
            array(),
            new Response(
                null,
                $form->isSubmitted() ?
                    Response::HTTP_UNPROCESSABLE_ENTITY :
                    Response::HTTP_OK
            )
        );
    }

    protected function checkPermissions()
    {
        $this->denyAccessUnlessGranted('ROLE_NGBM_EDITOR');
    }
}
