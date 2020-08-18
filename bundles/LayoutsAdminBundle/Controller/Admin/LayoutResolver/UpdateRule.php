<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Controller\Admin\LayoutResolver;

use Netgen\Bundle\LayoutsBundle\Controller\AbstractController;
use Netgen\Layouts\API\Service\LayoutResolverService;
use Netgen\Layouts\API\Service\LayoutService;
use Netgen\Layouts\API\Values\LayoutResolver\Rule;
use Netgen\Layouts\Exception\BadStateException;
use Netgen\Layouts\Exception\NotFoundException;
use Netgen\Layouts\View\ViewInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\Request;
use function in_array;
use function is_string;
use function sprintf;
use function trim;

final class UpdateRule extends AbstractController
{
    /**
     * @var \Netgen\Layouts\API\Service\LayoutResolverService
     */
    private $layoutResolverService;

    /**
     * @var \Netgen\Layouts\API\Service\LayoutService
     */
    private $layoutService;

    public function __construct(LayoutResolverService $layoutResolverService, LayoutService $layoutService)
    {
        $this->layoutResolverService = $layoutResolverService;
        $this->layoutService = $layoutService;
    }

    /**
     * Updates the rule.
     *
     * @throws \Netgen\Layouts\Exception\BadStateException If provided layout does not exist
     */
    public function __invoke(Rule $rule, Request $request): ViewInterface
    {
        $this->denyAccessUnlessGranted('nglayouts:mapping:edit');

        $layoutId = $request->request->get('layout_id');
        $layoutId = $layoutId !== null ? trim($layoutId) : null;

        $comment = $request->request->get('comment');
        $comment = $comment !== null ? trim($comment) : null;

        // null means we don't update the layout
        // empty (0, 0.0, '0', '', false) means we remove the layout from the rule
        if (is_string($layoutId) && !in_array($layoutId, ['0', ''], true)) {
            try {
                $this->layoutService->loadLayout(Uuid::fromString($layoutId));
            } catch (NotFoundException $e) {
                throw new BadStateException(
                    'layout_id',
                    sprintf(
                        'Layout with UUID "%s" does not exist.',
                        $layoutId
                    ),
                    $e
                );
            }
        }

        $ruleUpdateStruct = $this->layoutResolverService->newRuleUpdateStruct();
        $ruleUpdateStruct->comment = $comment;

        if ($layoutId !== null) {
            $ruleUpdateStruct->layoutId = !in_array($layoutId, ['0', ''], true) ?
                Uuid::fromString($layoutId) :
                false;
        }

        $updatedRule = $this->layoutResolverService->updateRule(
            $rule,
            $ruleUpdateStruct
        );

        return $this->buildView($updatedRule, ViewInterface::CONTEXT_ADMIN);
    }
}
