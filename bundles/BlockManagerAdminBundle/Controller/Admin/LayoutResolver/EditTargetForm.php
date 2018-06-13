<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerAdminBundle\Controller\Admin\LayoutResolver;

use Netgen\BlockManager\API\Service\LayoutResolverService;
use Netgen\BlockManager\API\Values\LayoutResolver\Target;
use Netgen\BlockManager\Layout\Resolver\Form\TargetType;
use Netgen\BlockManager\View\ViewInterface;
use Netgen\Bundle\BlockManagerAdminBundle\Controller\Admin\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class EditTargetForm extends Controller
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
     * Displays the target edit form.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\Target $target
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Netgen\BlockManager\View\ViewInterface
     */
    public function __invoke(Target $target, Request $request)
    {
        $targetType = $target->getTargetType();

        $updateStruct = $this->layoutResolverService->newTargetUpdateStruct();
        $updateStruct->value = $target->getValue();

        $form = $this->createForm(
            TargetType::class,
            $updateStruct,
            [
                'target_type' => $targetType,
                'action' => $this->generateUrl(
                    'ngbm_admin_layout_resolver_target_form_edit',
                    [
                        'targetId' => $target->getId(),
                    ]
                ),
            ]
        );

        $form->handleRequest($request);

        if (!$form->isSubmitted()) {
            return $this->buildView($form, ViewInterface::CONTEXT_ADMIN);
        }

        if ($form->isValid()) {
            $this->layoutResolverService->updateTarget($target, $updateStruct);

            return $this->buildView(
                $this->layoutResolverService->loadRuleDraft(
                    $target->getRuleId()
                ),
                ViewInterface::CONTEXT_ADMIN
            );
        }

        return $this->buildView(
            $form,
            ViewInterface::CONTEXT_ADMIN,
            [],
            new Response(null, Response::HTTP_UNPROCESSABLE_ENTITY)
        );
    }
}
