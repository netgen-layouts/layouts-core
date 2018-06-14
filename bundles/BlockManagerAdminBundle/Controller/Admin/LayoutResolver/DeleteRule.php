<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerAdminBundle\Controller\Admin\LayoutResolver;

use Netgen\BlockManager\API\Service\LayoutResolverService;
use Netgen\BlockManager\API\Values\LayoutResolver\Rule;
use Netgen\Bundle\BlockManagerAdminBundle\Controller\Admin\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class DeleteRule extends Controller
{
    /**
     * @var \Netgen\BlockManager\API\Service\LayoutResolverService
     */
    private $layoutResolverService;

    public function __construct(LayoutResolverService $layoutResolverService)
    {
        $this->layoutResolverService = $layoutResolverService;
    }

    /**
     * Deletes a rule.
     */
    public function __invoke(Request $request, Rule $rule): Response
    {
        if ($request->getMethod() !== Request::METHOD_DELETE) {
            return $this->render(
                '@NetgenBlockManagerAdmin/admin/layout_resolver/form/delete_rule.html.twig',
                [
                    'submitted' => false,
                    'error' => false,
                    'rule' => $rule,
                ]
            );
        }

        $this->layoutResolverService->deleteRule($rule);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
