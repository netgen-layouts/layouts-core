<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Controller\Admin\SharedLayouts;

use Netgen\Bundle\LayoutsAdminBundle\Form\Admin\Type\ClearLayoutsCacheType;
use Netgen\Bundle\LayoutsBundle\Controller\AbstractController;
use Netgen\Layouts\API\Service\LayoutService;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\HttpCache\InvalidatorInterface;
use Netgen\Layouts\View\ViewInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use function array_map;

final class ClearRelatedLayoutsCache extends AbstractController
{
    private LayoutService $layoutService;

    private InvalidatorInterface $invalidator;

    public function __construct(LayoutService $layoutService, InvalidatorInterface $invalidator)
    {
        $this->layoutService = $layoutService;
        $this->invalidator = $invalidator;
    }

    /**
     * Clears the HTTP caches for layouts related to provided shared layout.
     *
     * @return \Netgen\Layouts\View\ViewInterface|\Symfony\Component\HttpFoundation\Response
     */
    public function __invoke(Layout $layout, Request $request)
    {
        $this->denyAccessUnlessGranted('nglayouts:layout:clear_cache', $layout);

        $cacheCleared = true;
        $relatedLayouts = $this->layoutService->loadRelatedLayouts($layout);

        $form = $this->createForm(
            ClearLayoutsCacheType::class,
            null,
            [
                'layouts' => $relatedLayouts,
                'action' => $this->generateUrl(
                    'nglayouts_admin_shared_layouts_cache_related_layouts',
                    [
                        'layoutId' => $layout->getId()->toString(),
                    ],
                ),
            ],
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var \Netgen\Layouts\API\Values\Layout\LayoutList $selectedLayouts */
            $selectedLayouts = $form->get('layouts')->getData();

            $this->invalidator->invalidateLayouts(array_map('strval', $selectedLayouts->getLayoutIds()));
            $cacheCleared = $this->invalidator->commit();

            if ($cacheCleared) {
                return new Response(null, Response::HTTP_NO_CONTENT);
            }
        }

        return $this->buildView(
            $form,
            ViewInterface::CONTEXT_ADMIN,
            [
                'error' => !$cacheCleared,
                'layout' => $layout,
                'related_layouts' => $relatedLayouts,
            ],
            new Response(
                null,
                $form->isSubmitted() || !$cacheCleared ?
                    Response::HTTP_UNPROCESSABLE_ENTITY :
                    Response::HTTP_OK,
            ),
        );
    }
}
