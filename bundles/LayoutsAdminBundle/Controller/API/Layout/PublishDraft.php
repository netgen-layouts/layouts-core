<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Controller\API\Layout;

use Netgen\Bundle\LayoutsBundle\Configuration\ConfigurationInterface;
use Netgen\Bundle\LayoutsBundle\Controller\AbstractController;
use Netgen\Layouts\API\Service\LayoutService;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\HttpCache\InvalidatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class PublishDraft extends AbstractController
{
    /**
     * @var \Netgen\Layouts\API\Service\LayoutService
     */
    private $layoutService;

    /**
     * @var \Netgen\Layouts\HttpCache\InvalidatorInterface
     */
    private $invalidator;

    /**
     * @var \Netgen\Bundle\LayoutsBundle\Configuration\ConfigurationInterface
     */
    private $configuration;

    public function __construct(
        LayoutService $layoutService,
        InvalidatorInterface $invalidator,
        ConfigurationInterface $configuration
    ) {
        $this->layoutService = $layoutService;
        $this->invalidator = $invalidator;
        $this->configuration = $configuration;
    }

    /**
     * Publishes a layout draft.
     *
     * Optionally, clears the HTTP cache for the layout if specified either by the configuration
     * injected in the constructor or the query parameter passed with the request.
     */
    public function __invoke(Layout $layout, Request $request): Response
    {
        $this->denyAccessUnlessGranted('nglayouts:layout:edit', $layout);

        $this->layoutService->publishLayout($layout);

        if (
            (bool) $this->configuration->getParameter('app.automatic_cache_clear') ||
            $request->query->getBoolean('clearCache')
        ) {
            $this->invalidator->invalidateLayouts([$layout->getId()->toString()]);
            $this->invalidator->invalidateLayoutBlocks([$layout->getId()->toString()]);
        }

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
