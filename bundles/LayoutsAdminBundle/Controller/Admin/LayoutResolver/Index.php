<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Controller\Admin\LayoutResolver;

use Netgen\Bundle\LayoutsBundle\Controller\AbstractController;
use Netgen\Layouts\API\Service\LayoutResolverService;
use Netgen\Layouts\API\Values\LayoutResolver\RuleGroup;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\Uuid;

final class Index extends AbstractController
{
    public function __construct(
        private LayoutResolverService $layoutResolverService,
    ) {}

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
