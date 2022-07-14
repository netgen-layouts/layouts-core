<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Controller\Admin\LayoutResolver;

use Netgen\Bundle\LayoutsBundle\Controller\AbstractController;
use Netgen\Layouts\API\Service\LayoutResolverService;
use Netgen\Layouts\API\Values\LayoutResolver\RuleGroup;
use Netgen\Layouts\Exception\BadStateException;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Kernel;

use function count;
use function sprintf;

final class UpdatePriorities extends AbstractController
{
    private LayoutResolverService $layoutResolverService;

    public function __construct(LayoutResolverService $layoutResolverService)
    {
        $this->layoutResolverService = $layoutResolverService;
    }

    /**
     * Updates priorities in the provided group.
     *
     * @throws \Netgen\Layouts\Exception\BadStateException If an error occurred
     */
    public function __invoke(RuleGroup $ruleGroup, Request $request): Response
    {
        $this->denyAccessUnlessGranted('nglayouts:mapping:reorder', $ruleGroup);

        $ids = Kernel::VERSION_ID >= 50100 ?
            $request->request->all('ids') :
            (array) ($request->request->get('ids') ?? []);

        if (count($ids) === 0) {
            throw new BadStateException('ids', 'List of entities to reorder cannot be empty.');
        }

        $this->layoutResolverService->transaction(
            function () use ($ids, $ruleGroup): void {
                $priority = 10 * count($ids);

                foreach ($ids as $id => $type) {
                    if ($type === 'rule') {
                        $this->updateRulePriority((string) $id, $priority, $ruleGroup);
                    } elseif ($type === 'rule_group') {
                        $this->updateRuleGroupPriority((string) $id, $priority, $ruleGroup);
                    }

                    $priority -= 10;
                }
            },
        );

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Updates the priority of the rule with provided ID.
     *
     * @throws \Netgen\Layouts\Exception\BadStateException if the rule with provided ID does not belong to provided rule group
     */
    private function updateRulePriority(string $ruleId, int $priority, RuleGroup $ruleGroup): void
    {
        $rule = $this->layoutResolverService->loadRule(Uuid::fromString($ruleId));

        if ($rule->getRuleGroupId()->toString() !== $ruleGroup->getId()->toString()) {
            throw new BadStateException(
                'rule',
                sprintf(
                    'Rule with ID %s does not belong to provided group.',
                    $rule->getRuleGroupId()->toString(),
                ),
            );
        }

        $updateStruct = $this->layoutResolverService->newRuleMetadataUpdateStruct();
        $updateStruct->priority = $priority;

        $this->layoutResolverService->updateRuleMetadata($rule, $updateStruct);
    }

    /**
     * Updates the priority of the rule group with provided ID.
     *
     * @throws \Netgen\Layouts\Exception\BadStateException if the rule group with provided ID does not belong to provided parent group
     */
    private function updateRuleGroupPriority(string $ruleGroupId, int $priority, RuleGroup $parentGroup): void
    {
        $ruleGroup = $this->layoutResolverService->loadRuleGroup(Uuid::fromString($ruleGroupId));

        if ($ruleGroup->getParentId() === null || $ruleGroup->getParentId()->toString() !== $parentGroup->getId()->toString()) {
            throw new BadStateException(
                'rule group',
                sprintf(
                    'Rule group with ID %s does not belong to provided group.',
                    $ruleGroup->getId()->toString(),
                ),
            );
        }

        $updateStruct = $this->layoutResolverService->newRuleGroupMetadataUpdateStruct();
        $updateStruct->priority = $priority;

        $this->layoutResolverService->updateRuleGroupMetadata($ruleGroup, $updateStruct);
    }
}
