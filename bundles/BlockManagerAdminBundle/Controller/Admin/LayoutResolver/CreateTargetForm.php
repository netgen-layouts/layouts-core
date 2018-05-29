<?php

namespace Netgen\Bundle\BlockManagerAdminBundle\Controller\Admin\LayoutResolver;

use Netgen\BlockManager\API\Service\LayoutResolverService;
use Netgen\BlockManager\API\Values\LayoutResolver\Rule;
use Netgen\BlockManager\Layout\Resolver\Form\TargetType;
use Netgen\BlockManager\Layout\Resolver\Registry\TargetTypeRegistryInterface;
use Netgen\BlockManager\View\ViewInterface;
use Netgen\Bundle\BlockManagerAdminBundle\Controller\Admin\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class CreateTargetForm extends Controller
{
    /**
     * @var \Netgen\BlockManager\API\Service\LayoutResolverService
     */
    private $layoutResolverService;

    /**
     * @var \Netgen\BlockManager\Layout\Resolver\Registry\TargetTypeRegistryInterface
     */
    private $targetTypeRegistry;

    public function __construct(
        LayoutResolverService $layoutResolverService,
        TargetTypeRegistryInterface $targetTypeRegistry
    ) {
        $this->layoutResolverService = $layoutResolverService;
        $this->targetTypeRegistry = $targetTypeRegistry;
    }

    /**
     * Displays the target create form.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\Rule $rule
     * @param string $type
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Netgen\BlockManager\View\ViewInterface
     */
    public function __invoke(Rule $rule, $type, Request $request)
    {
        $targetType = $this->targetTypeRegistry->getTargetType($type);
        $createStruct = $this->layoutResolverService->newTargetCreateStruct($type);

        $form = $this->createForm(
            TargetType::class,
            $createStruct,
            [
                'target_type' => $targetType,
                'action' => $this->generateUrl(
                    'ngbm_admin_layout_resolver_target_form_create',
                    [
                        'ruleId' => $rule->getId(),
                        'type' => $type,
                    ]
                ),
            ]
        );

        $form->handleRequest($request);

        if (!$form->isSubmitted()) {
            return $this->buildView($form, ViewInterface::CONTEXT_ADMIN);
        }

        if ($form->isValid()) {
            $this->layoutResolverService->addTarget($rule, $createStruct);

            return $this->buildView(
                $this->layoutResolverService->loadRuleDraft(
                    $rule->getId()
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
