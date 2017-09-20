<?php

namespace Netgen\Bundle\BlockManagerAdminBundle\Controller\Admin;

use Exception;
use Netgen\BlockManager\API\Service\LayoutResolverService;
use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\BlockManager\API\Values\LayoutResolver\Condition;
use Netgen\BlockManager\API\Values\LayoutResolver\Rule;
use Netgen\BlockManager\API\Values\LayoutResolver\Target;
use Netgen\BlockManager\Exception\BadStateException;
use Netgen\BlockManager\Exception\NotFoundException;
use Netgen\BlockManager\Layout\Resolver\Form\ConditionType;
use Netgen\BlockManager\Layout\Resolver\Form\TargetType;
use Netgen\BlockManager\Layout\Resolver\Registry\ConditionTypeRegistryInterface;
use Netgen\BlockManager\Layout\Resolver\Registry\TargetTypeRegistryInterface;
use Netgen\BlockManager\View\ViewInterface;
use Netgen\Bundle\BlockManagerAdminBundle\Controller\Admin\Validator\LayoutResolverValidator;
use Netgen\Bundle\BlockManagerBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class LayoutResolverController extends Controller
{
    /**
     * @var \Netgen\BlockManager\API\Service\LayoutResolverService
     */
    private $layoutResolverService;

    /**
     * @var \Netgen\BlockManager\API\Service\LayoutService
     */
    private $layoutService;

    /**
     * @var \Netgen\BlockManager\Layout\Resolver\Registry\TargetTypeRegistryInterface
     */
    private $targetTypeRegistry;

    /**
     * @var \Netgen\BlockManager\Layout\Resolver\Registry\ConditionTypeRegistryInterface
     */
    private $conditionTypeRegistry;

    /**
     * @var \Netgen\Bundle\BlockManagerAdminBundle\Controller\Admin\Validator\LayoutResolverValidator
     */
    private $validator;

    public function __construct(
        LayoutResolverService $layoutResolverService,
        LayoutService $layoutService,
        TargetTypeRegistryInterface $targetTypeRegistry,
        ConditionTypeRegistryInterface $conditionTypeRegistry,
        LayoutResolverValidator $validator
    ) {
        $this->layoutResolverService = $layoutResolverService;
        $this->layoutService = $layoutService;
        $this->targetTypeRegistry = $targetTypeRegistry;
        $this->conditionTypeRegistry = $conditionTypeRegistry;
        $this->validator = $validator;
    }

    /**
     * Displays the index page of layout resolver admin interface.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index()
    {
        return $this->render(
            '@NetgenBlockManagerAdmin/admin/layout_resolver/index.html.twig',
            array(
                'rules' => $this->layoutResolverService->loadRules(),
            )
        );
    }

    /**
     * Creates a new rule.
     *
     * @return \Netgen\BlockManager\View\ViewInterface
     */
    public function createRule()
    {
        $createdRule = $this->layoutResolverService->createRule(
            $this->layoutResolverService->newRuleCreateStruct()
        );

        $createdRule = $this->layoutResolverService->publishRule(
            $createdRule
        );

        return $this->buildView($createdRule, ViewInterface::CONTEXT_ADMIN);
    }

    /**
     * Updates the rule.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\Rule $rule
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If provided layout does not exist
     *
     * @return \Netgen\BlockManager\View\ViewInterface
     */
    public function updateRule(Rule $rule, Request $request)
    {
        $layoutId = $request->request->get('layout_id');
        $layoutId = $layoutId !== null ? trim($layoutId) : null;

        $comment = $request->request->get('comment');
        $comment = $comment !== null ? trim($comment) : null;

        // null means we don't update the layout
        // empty ("" or "0") means we remove the layout from the rule
        if ($layoutId !== null && !empty($layoutId)) {
            try {
                $this->layoutService->loadLayout($layoutId);
            } catch (NotFoundException $e) {
                throw new BadStateException(
                    'layout_id',
                    sprintf(
                        'Layout with ID "%s" does not exist.',
                        $layoutId
                    ),
                    $e
                );
            }
        }

        $ruleUpdateStruct = $this->layoutResolverService->newRuleUpdateStruct();
        $ruleUpdateStruct->comment = $comment;
        $ruleUpdateStruct->layoutId = $layoutId !== null ?
            (!empty($layoutId) ? $layoutId : 0) :
            null;

        $updatedRule = $this->layoutResolverService->updateRule(
            $rule,
            $ruleUpdateStruct
        );

        return $this->buildView($updatedRule, ViewInterface::CONTEXT_ADMIN);
    }

    /**
     * Updates rule priorities.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If an error occurred
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updatePriorities(Request $request)
    {
        $this->validator->validatePriorities($request);

        try {
            $this->layoutResolverService->transaction(
                function () use ($request) {
                    // Rules are ordered by descending priority
                    // in the request variable, we reverse the list here
                    // as it is way easier to increment priorities
                    // then decrement them (especially when we need to
                    // make sure to skip rules which do not exist)
                    $ruleIds = array_reverse(
                        array_unique(
                            $request->request->get('rule_ids')
                        )
                    );

                    $ruleUpdateStruct = $this->layoutResolverService->newRuleMetadataUpdateStruct();
                    $ruleUpdateStruct->priority = 10;

                    foreach (array_values($ruleIds) as $ruleId) {
                        try {
                            $rule = $this->layoutResolverService->loadRule($ruleId);
                        } catch (NotFoundException $e) {
                            continue;
                        }

                        $this->layoutResolverService->updateRuleMetadata(
                            $rule,
                            $ruleUpdateStruct
                        );

                        $ruleUpdateStruct->priority += 10;
                    }
                }
            );
        } catch (Exception $e) {
            throw new BadStateException('rule', $e->getMessage());
        }

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Enables a rule.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\Rule $rule
     *
     * @return \Netgen\BlockManager\View\ViewInterface
     */
    public function enableRule(Rule $rule)
    {
        $enabledRule = $this->layoutResolverService->enableRule($rule);

        return $this->buildView($enabledRule, ViewInterface::CONTEXT_ADMIN);
    }

    /**
     * Disables a rule.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\Rule $rule
     *
     * @return \Netgen\BlockManager\View\ViewInterface
     */
    public function disableRule(Rule $rule)
    {
        $disabledRule = $this->layoutResolverService->disableRule($rule);

        return $this->buildView($disabledRule, ViewInterface::CONTEXT_ADMIN);
    }

    /**
     * Creates a new rule draft from published state.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\Rule $rule
     *
     * @return \Netgen\BlockManager\View\ViewInterface
     */
    public function createRuleDraft(Rule $rule)
    {
        $createdDraft = $this->layoutResolverService->createDraft($rule, true);

        return $this->buildView($createdDraft, ViewInterface::CONTEXT_ADMIN);
    }

    /**
     * Discards a rule draft.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\Rule $rule
     *
     * @return \Netgen\BlockManager\View\ViewInterface
     */
    public function discardRuleDraft(Rule $rule)
    {
        $this->layoutResolverService->discardDraft($rule);

        $publishedRule = $this->layoutResolverService->loadRule($rule->getId());

        return $this->buildView($publishedRule, ViewInterface::CONTEXT_ADMIN);
    }

    /**
     * Publishes a rule draft.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\Rule $rule
     *
     * @return \Netgen\BlockManager\View\ViewInterface
     */
    public function publishRuleDraft(Rule $rule)
    {
        $publishedRule = $this->layoutResolverService->publishRule($rule);

        return $this->buildView($publishedRule, ViewInterface::CONTEXT_ADMIN);
    }

    /**
     * Deletes a rule.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\Rule $rule
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteRule(Rule $rule)
    {
        $this->layoutResolverService->deleteRule($rule);

        return new Response(null, Response::HTTP_NO_CONTENT);
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
    public function targetCreateForm(Rule $rule, $type, Request $request)
    {
        $targetType = $this->targetTypeRegistry->getTargetType($type);
        $createStruct = $this->layoutResolverService->newTargetCreateStruct($type);

        $form = $this->createForm(
            TargetType::class,
            $createStruct,
            array(
                'targetType' => $targetType,
                'action' => $this->generateUrl(
                    'ngbm_admin_layout_resolver_target_form_create',
                    array(
                        'ruleId' => $rule->getId(),
                        'type' => $type,
                    )
                ),
            )
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
            array(),
            new Response(null, Response::HTTP_UNPROCESSABLE_ENTITY)
        );
    }

    /**
     * Displays the target edit form.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\Target $target
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Netgen\BlockManager\View\ViewInterface
     */
    public function targetEditForm(Target $target, Request $request)
    {
        $targetType = $target->getTargetType();

        $updateStruct = $this->layoutResolverService->newTargetUpdateStruct();
        $updateStruct->value = $target->getValue();

        $form = $this->createForm(
            TargetType::class,
            $updateStruct,
            array(
                'targetType' => $targetType,
                'action' => $this->generateUrl(
                    'ngbm_admin_layout_resolver_target_form_edit',
                    array(
                        'targetId' => $target->getId(),
                    )
                ),
            )
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
            array(),
            new Response(null, Response::HTTP_UNPROCESSABLE_ENTITY)
        );
    }

    /**
     * Deletes a rule target.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\Target $target
     *
     * @return \Netgen\BlockManager\View\ViewInterface
     */
    public function deleteTarget(Target $target)
    {
        $this->layoutResolverService->deleteTarget($target);

        return $this->buildView(
            $this->layoutResolverService->loadRuleDraft(
                $target->getRuleId()
            ),
            ViewInterface::CONTEXT_ADMIN
        );
    }

    /**
     * Displays the condition create form.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\Rule $rule
     * @param string $type
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Netgen\BlockManager\View\ViewInterface
     */
    public function conditionCreateForm(Rule $rule, $type, Request $request)
    {
        $conditionType = $this->conditionTypeRegistry->getConditionType($type);
        $createStruct = $this->layoutResolverService->newConditionCreateStruct($type);

        $form = $this->createForm(
            ConditionType::class,
            $createStruct,
            array(
                'conditionType' => $conditionType,
                'action' => $this->generateUrl(
                    'ngbm_admin_layout_resolver_condition_form_create',
                    array(
                        'ruleId' => $rule->getId(),
                        'type' => $type,
                    )
                ),
            )
        );

        $form->handleRequest($request);

        if (!$form->isSubmitted()) {
            return $this->buildView($form, ViewInterface::CONTEXT_ADMIN);
        }

        if ($form->isValid()) {
            $this->layoutResolverService->addCondition($rule, $createStruct);

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
            array(),
            new Response(null, Response::HTTP_UNPROCESSABLE_ENTITY)
        );
    }

    /**
     * Displays the condition edit form.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\Condition $condition
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Netgen\BlockManager\View\ViewInterface
     */
    public function conditionEditForm(Condition $condition, Request $request)
    {
        $conditionType = $condition->getConditionType();

        $updateStruct = $this->layoutResolverService->newConditionUpdateStruct();
        $updateStruct->value = $condition->getValue();

        $form = $this->createForm(
            ConditionType::class,
            $updateStruct,
            array(
                'conditionType' => $conditionType,
                'action' => $this->generateUrl(
                    'ngbm_admin_layout_resolver_condition_form_edit',
                    array(
                        'conditionId' => $condition->getId(),
                    )
                ),
            )
        );

        $form->handleRequest($request);

        if (!$form->isSubmitted()) {
            return $this->buildView($form, ViewInterface::CONTEXT_ADMIN);
        }

        if ($form->isValid()) {
            $this->layoutResolverService->updateCondition($condition, $updateStruct);

            return $this->buildView(
                $this->layoutResolverService->loadRuleDraft(
                    $condition->getRuleId()
                ),
                ViewInterface::CONTEXT_ADMIN
            );
        }

        return $this->buildView(
            $form,
            ViewInterface::CONTEXT_ADMIN,
            array(),
            new Response(null, Response::HTTP_UNPROCESSABLE_ENTITY)
        );
    }

    /**
     * Deletes a rule condition.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\Condition $condition
     *
     * @return \Netgen\BlockManager\View\ViewInterface
     */
    public function deleteCondition(Condition $condition)
    {
        $this->layoutResolverService->deleteCondition($condition);

        return $this->buildView(
            $this->layoutResolverService->loadRuleDraft(
                $condition->getRuleId()
            ),
            ViewInterface::CONTEXT_ADMIN
        );
    }

    protected function checkPermissions()
    {
        $this->denyAccessUnlessGranted('ROLE_NGBM_ADMIN');
    }
}
