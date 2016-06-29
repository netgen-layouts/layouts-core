<?php

namespace Netgen\Bundle\BlockManagerBundle\Controller\API\V1;

use Netgen\BlockManager\API\Repository;
use Netgen\BlockManager\API\Service\LayoutResolverService;
use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\BlockManager\API\Values\LayoutResolver\ConditionDraft;
use Netgen\BlockManager\API\Values\LayoutResolver\Rule;
use Netgen\BlockManager\API\Values\LayoutResolver\RuleDraft;
use Netgen\BlockManager\API\Values\LayoutResolver\TargetDraft;
use Netgen\BlockManager\Exception\BadStateException;
use Netgen\BlockManager\Exception\NotFoundException;
use Netgen\BlockManager\Serializer\Values\View;
use Netgen\BlockManager\Serializer\Version;
use Netgen\Bundle\BlockManagerBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Exception;

class LayoutResolverController extends Controller
{
    /**
     * @var \Netgen\BlockManager\API\Repository
     */
    protected $repository;

    /**
     * @var \Netgen\BlockManager\API\Service\LayoutResolverService
     */
    protected $layoutResolverService;

    /**
     * @var \Netgen\BlockManager\API\Service\LayoutService
     */
    protected $layoutService;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\API\Repository $repository
     * @param \Netgen\BlockManager\API\Service\LayoutResolverService $layoutResolverService
     * @param \Netgen\BlockManager\API\Service\LayoutService $layoutService
     */
    public function __construct(
        Repository $repository,
        LayoutResolverService $layoutResolverService,
        LayoutService $layoutService
    ) {
        $this->repository = $repository;
        $this->layoutResolverService = $layoutResolverService;
        $this->layoutService = $layoutService;
    }

    /**
     * Creates a new rule.
     *
     * @return \Netgen\BlockManager\Serializer\Values\View
     */
    public function createRule()
    {
        $ruleCreateStruct = $this->layoutResolverService->newRuleCreateStruct();

        $createdRule = $this->layoutResolverService->createRule($ruleCreateStruct);

        return new View($createdRule, Version::API_V1, Response::HTTP_CREATED);
    }

    /**
     * Updates the rule.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\RuleDraft $rule
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If provided layout does not exist.
     *
     * @return \Netgen\BlockManager\Serializer\Values\View
     */
    public function updateRule(RuleDraft $rule, Request $request)
    {
        $layoutId = $request->request->get('layout_id');

        // null means we don't update the layout
        // 0 means we remove the layout from the rule
        if ($layoutId !== null && $layoutId !== 0) {
            try {
                $this->layoutService->loadLayout($layoutId);
            } catch (NotFoundException $e) {
                throw new BadStateException('layout_id', 'Layout does not exist.', $e);
            }
        }

        $ruleUpdateStruct = $this->layoutResolverService->newRuleUpdateStruct();
        $ruleUpdateStruct->layoutId = $layoutId;
        $ruleUpdateStruct->comment = $request->request->get('comment');
        $ruleUpdateStruct->priority = $request->request->get('priority');

        $this->layoutResolverService->updateRule($rule, $ruleUpdateStruct);

        return new View($rule, Version::API_V1);
    }

    /**
     * Enables a rule.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\Rule $rule
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function enableRule(Rule $rule)
    {
        $this->layoutResolverService->enableRule($rule);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Disables a rule.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\Rule $rule
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function disableRule(Rule $rule)
    {
        $this->layoutResolverService->disableRule($rule);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Creates a new rule draft from published state.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\Rule $rule
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If an error occurred
     *
     * @return \Netgen\BlockManager\Serializer\Values\View
     */
    public function createRuleDraft(Rule $rule)
    {
        $ruleDraft = null;

        try {
            $ruleDraft = $this->layoutResolverService->loadRuleDraft($rule->getId());
        } catch (NotFoundException $e) {
            // Do nothing
        }

        $this->repository->beginTransaction();

        try {
            if ($ruleDraft instanceof RuleDraft) {
                $this->layoutResolverService->discardDraft($ruleDraft);
            }

            $createdDraft = $this->layoutResolverService->createDraft($rule);

            $this->repository->commitTransaction();

            return new View($createdDraft, Version::API_V1, Response::HTTP_CREATED);
        } catch (Exception $e) {
            $this->repository->rollbackTransaction();

            throw new BadStateException('rule', $e->getMessage());
        }
    }

    /**
     * Discards a rule draft.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\RuleDraft $rule
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function discardRuleDraft(RuleDraft $rule)
    {
        $this->layoutResolverService->discardDraft($rule);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Publishes a rule draft.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\RuleDraft $rule
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function publishRuleDraft(RuleDraft $rule)
    {
        $this->layoutResolverService->publishRule($rule);

        return new Response(null, Response::HTTP_NO_CONTENT);
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
     * Deletes a rule target.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\TargetDraft $target
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteTarget(TargetDraft $target)
    {
        $this->layoutResolverService->deleteTarget($target);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Deletes a rule condition.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\ConditionDraft $condition
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteCondition(ConditionDraft $condition)
    {
        $this->layoutResolverService->deleteCondition($condition);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
