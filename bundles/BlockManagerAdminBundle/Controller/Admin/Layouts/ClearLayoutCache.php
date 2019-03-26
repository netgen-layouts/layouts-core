<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerAdminBundle\Controller\Admin\Layouts;

use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\BlockManager\HttpCache\ClientInterface;
use Netgen\Bundle\BlockManagerBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class ClearLayoutCache extends Controller
{
    /**
     * @var \Netgen\BlockManager\HttpCache\ClientInterface
     */
    private $httpCacheClient;

    public function __construct(ClientInterface $httpCacheClient)
    {
        $this->httpCacheClient = $httpCacheClient;
    }

    /**
     * Clears the HTTP cache for provided layout.
     */
    public function __invoke(Request $request, Layout $layout): Response
    {
        $this->denyAccessUnlessGranted('nglayouts:layout:clear_cache');

        if ($request->getMethod() !== Request::METHOD_POST) {
            return $this->render(
                '@NetgenBlockManagerAdmin/admin/layouts/form/clear_layout_cache.html.twig',
                [
                    'submitted' => false,
                    'error' => false,
                    'layout' => $layout,
                ]
            );
        }

        $this->httpCacheClient->invalidateLayouts([$layout->getId()]);

        $cacheCleared = $this->httpCacheClient->commit();

        if ($cacheCleared) {
            return new Response(null, Response::HTTP_NO_CONTENT);
        }

        return $this->render(
            '@NetgenBlockManagerAdmin/admin/layouts/form/clear_layout_cache.html.twig',
            [
                'submitted' => true,
                'error' => !$cacheCleared,
                'layout' => $layout,
            ],
            new Response(null, Response::HTTP_UNPROCESSABLE_ENTITY)
        );
    }
}
