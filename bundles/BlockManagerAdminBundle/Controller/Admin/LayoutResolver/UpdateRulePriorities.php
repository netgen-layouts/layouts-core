<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerAdminBundle\Controller\Admin\LayoutResolver;

use Netgen\BlockManager\API\Service\LayoutResolverService;
use Netgen\BlockManager\Exception\BadStateException;
use Netgen\BlockManager\Exception\NotFoundException;
use Netgen\BlockManager\Validator\ValidatorTrait;
use Netgen\Bundle\BlockManagerBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints;
use Throwable;

final class UpdateRulePriorities extends AbstractController
{
    use ValidatorTrait;

    /**
     * @var \Netgen\BlockManager\API\Service\LayoutResolverService
     */
    private $layoutResolverService;

    public function __construct(LayoutResolverService $layoutResolverService)
    {
        $this->layoutResolverService = $layoutResolverService;
    }

    /**
     * Updates rule priorities.
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If an error occurred
     */
    public function __invoke(Request $request): Response
    {
        $this->denyAccessUnlessGranted('nglayouts:mapping:reorder');

        $this->validatePriorities($request);

        try {
            $this->layoutResolverService->transaction(
                function () use ($request): void {
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
        } catch (Throwable $t) {
            throw new BadStateException('rule', $t->getMessage());
        }

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Validates list of rules from the request when updating priorities.
     *
     * @throws \Netgen\BlockManager\Exception\Validation\ValidationException If validation failed
     */
    private function validatePriorities(Request $request): void
    {
        $this->validate(
            $request->request->get('rule_ids'),
            [
                new Constraints\NotBlank(),
                new Constraints\Type(['type' => 'array']),
                new Constraints\All(
                    [
                        'constraints' => [
                            new Constraints\NotBlank(),
                            new Constraints\Type(['type' => 'scalar']),
                        ],
                    ]
                ),
            ],
            'rule_ids'
        );
    }
}
