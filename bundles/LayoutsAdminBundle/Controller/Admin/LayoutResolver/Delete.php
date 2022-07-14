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

final class Delete extends AbstractController
{
    private LayoutResolverService $layoutResolverService;

    public function __construct(LayoutResolverService $layoutResolverService)
    {
        $this->layoutResolverService = $layoutResolverService;
    }

    /**
     * Deletes the provided list of rules and groups.
     */
    public function __invoke(Request $request, RuleGroup $ruleGroup): Response
    {
        $this->denyAccessUnlessGranted('nglayouts:mapping:delete', $ruleGroup);

        if ($request->getMethod() !== Request::METHOD_DELETE) {
            return $this->render(
                '@NetgenLayoutsAdmin/admin/layout_resolver/form/delete.html.twig',
                [
                    'submitted' => false,
                    'error' => false,
                ],
            );
        }

        $ids = Kernel::VERSION_ID >= 50100 ?
            $request->request->all('ids') :
            (array) ($request->request->get('ids') ?? []);

        if (count($ids) === 0) {
            throw new BadStateException('ids', 'List of entities to delete cannot be empty.');
        }

        $this->layoutResolverService->transaction(
            function () use ($ids, $ruleGroup): void {
                foreach ($ids as $id => $type) {
                    if ($type === 'rule') {
                        $this->deleteRule((string) $id, $ruleGroup);
                    } elseif ($type === 'rule_group') {
                        $this->deleteRuleGroup((string) $id, $ruleGroup);
                    }
                }
            },
        );

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Deletes the rule with provided ID.
     *
     * @throws \Netgen\Layouts\Exception\BadStateException if the rule with provided ID does not belong to provided rule group
     */
    private function deleteRule(string $ruleId, RuleGroup $ruleGroup): void
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

        $this->layoutResolverService->deleteRule($rule);
    }

    /**
     * Deletes the rule group with provided ID.
     *
     * @throws \Netgen\Layouts\Exception\BadStateException if the rule group with provided ID does not belong to provided parent group
     */
    private function deleteRuleGroup(string $ruleGroupId, RuleGroup $parentGroup): void
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

        $this->layoutResolverService->deleteRuleGroup($ruleGroup);
    }
}
