<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout;

use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\BlockManager\Serializer\Values\View;
use Netgen\BlockManager\Serializer\Version;
use Netgen\Bundle\BlockManagerBundle\Controller\API\Controller;
use Symfony\Component\HttpFoundation\Response;

final class CreateDraft extends Controller
{
    /**
     * @var \Netgen\BlockManager\API\Service\LayoutService
     */
    private $layoutService;

    public function __construct(LayoutService $layoutService)
    {
        $this->layoutService = $layoutService;
    }

    /**
     * Creates a new layout draft.
     */
    public function __invoke(Layout $layout): View
    {
        $createdDraft = $this->layoutService->createDraft($layout, true);

        return new View($createdDraft, Version::API_V1, Response::HTTP_CREATED);
    }
}
