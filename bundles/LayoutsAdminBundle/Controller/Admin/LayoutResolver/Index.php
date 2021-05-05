<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Controller\Admin\LayoutResolver;

use Netgen\Bundle\LayoutsBundle\Controller\AbstractController;
use Netgen\Layouts\API\Service\LayoutResolverService;
use Netgen\Layouts\API\Values\LayoutResolver\RuleGroup;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\Response;

final class Index extends AbstractController
{
    private LayoutResolverService $layoutResolverService;

    public function __construct(LayoutResolverService $layoutResolverService)
    {
        $this->layoutResolverService = $layoutResolverService;
    }

    /**
     * Displays the index page of layout resolver admin interface.
     */
    public function __invoke(): Response
    {
        $this->denyAccessUnlessGranted('nglayouts:ui:access');

        return $this->render(
            '@NetgenLayoutsAdmin/admin/layout_resolver/index.html.twig',
            [
                'rules' => $this->layoutResolverService->loadRulesFromGroup(
                    $this->layoutResolverService->loadRuleGroup(Uuid::fromString(RuleGroup::ROOT_UUID)),
                ),
            ],
        );
    }
}
