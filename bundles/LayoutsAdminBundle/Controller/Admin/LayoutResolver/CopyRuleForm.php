<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Controller\Admin\LayoutResolver;

use Netgen\Bundle\LayoutsAdminBundle\Form\Admin\Type\CopyRuleType;
use Netgen\Bundle\LayoutsBundle\Controller\AbstractController;
use Netgen\Layouts\API\Service\LayoutResolverService;
use Netgen\Layouts\API\Service\LayoutService;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\API\Values\LayoutResolver\Rule;
use Netgen\Layouts\View\ViewInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class CopyRuleForm extends AbstractController
{
    private LayoutResolverService $layoutResolverService;

    private LayoutService $layoutService;

    public function __construct(LayoutResolverService $layoutResolverService, LayoutService $layoutService)
    {
        $this->layoutResolverService = $layoutResolverService;
        $this->layoutService = $layoutService;
    }

    /**
     * Copies a rule together with the mapped layout (if selected). Rule is added
     * to a position below the copied one, and deactivated by default.
     */
    public function __invoke(Rule $rule, Request $request): ViewInterface
    {
        $this->denyAccessUnlessGranted(
            'nglayouts:mapping:edit',
            [
                'rule_group' => $rule->getRuleGroupId()->toString(),
            ],
        );

        $originalLayout = $rule->getLayout();

        if (!$originalLayout instanceof Layout) {
            throw new BadRequestHttpException('Mapping can be copied via this form only if it is related to a layout.');
        }

        $layoutCopyStruct = $this->layoutService->newLayoutCopyStruct($rule->getLayout());

        $form = $this->createForm(
            CopyRuleType::class,
            [
                'layout_name' => $layoutCopyStruct->name,
                'layout_description' => $layoutCopyStruct->description,
            ],
            [
                'action' => $this->generateUrl(
                    'nglayouts_admin_layout_resolver_rule_copy_form',
                    [
                        'ruleId' => $rule->getId()->toString(),
                    ],
                ),
            ],
        );

        $form->handleRequest($request);

        if (!$form->isSubmitted()) {
            return $this->buildView($form, ViewInterface::CONTEXT_ADMIN);
        }

        if (!$form->isValid()) {
            return $this->buildView(
                $form,
                ViewInterface::CONTEXT_ADMIN,
                [],
                new Response(null, Response::HTTP_UNPROCESSABLE_ENTITY),
            );
        }

        $copiedRule = $this->layoutResolverService->transaction(
            function () use ($rule, $form, $layoutCopyStruct, $originalLayout): Rule {
                $ruleMetadataUpdateStruct = $this->layoutResolverService->newRuleMetadataUpdateStruct();
                $ruleMetadataUpdateStruct->priority = $rule->getPriority() - 1;

                $targetGroup = $this->layoutResolverService->loadRuleGroup($rule->getRuleGroupId());

                $copiedRule = $this->layoutResolverService->updateRuleMetadata(
                    $this->layoutResolverService->copyRule($rule, $targetGroup),
                    $ruleMetadataUpdateStruct,
                );

                if ($copiedRule->isEnabled()) {
                    $copiedRule = $this->layoutResolverService->disableRule($copiedRule);
                }

                if (!((bool) $form->get('copy_layout')->getData())) {
                    return $copiedRule;
                }

                $layoutCopyStruct->name = $form->get('layout_name')->getData();
                $layoutCopyStruct->description = $form->get('layout_description')->getData();

                $ruleUpdateStruct = $this->layoutResolverService->newRuleUpdateStruct();
                $ruleUpdateStruct->layoutId = $this->layoutService->copyLayout($originalLayout, $layoutCopyStruct)->getId();

                $draftRule = $this->layoutResolverService->updateRule(
                    $this->layoutResolverService->createRuleDraft($copiedRule),
                    $ruleUpdateStruct,
                );

                return $this->layoutResolverService->publishRule($draftRule);
            },
        );

        return $this->buildView($copiedRule, ViewInterface::CONTEXT_ADMIN);
    }
}
