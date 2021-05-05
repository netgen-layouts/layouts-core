<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Controller\Admin\Layouts;

use Netgen\Bundle\LayoutsBundle\Controller\AbstractController;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\HttpCache\InvalidatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class ClearLayoutCache extends AbstractController
{
    private InvalidatorInterface $invalidator;

    public function __construct(InvalidatorInterface $invalidator)
    {
        $this->invalidator = $invalidator;
    }

    /**
     * Clears the HTTP cache for provided layout.
     */
    public function __invoke(Request $request, Layout $layout): Response
    {
        $this->denyAccessUnlessGranted('nglayouts:layout:clear_cache', $layout);

        if ($request->getMethod() !== Request::METHOD_POST) {
            return $this->render(
                '@NetgenLayoutsAdmin/admin/layouts/form/clear_layout_cache.html.twig',
                [
                    'submitted' => false,
                    'error' => false,
                    'layout' => $layout,
                ],
            );
        }

        $this->invalidator->invalidateLayouts([$layout->getId()->toString()]);

        $cacheCleared = $this->invalidator->commit();

        if ($cacheCleared) {
            return new Response(null, Response::HTTP_NO_CONTENT);
        }

        return $this->render(
            '@NetgenLayoutsAdmin/admin/layouts/form/clear_layout_cache.html.twig',
            [
                'submitted' => true,
                'error' => true,
                'layout' => $layout,
            ],
            new Response(null, Response::HTTP_UNPROCESSABLE_ENTITY),
        );
    }
}
