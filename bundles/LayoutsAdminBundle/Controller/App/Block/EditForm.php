<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Controller\App\Block;

use Netgen\Bundle\LayoutsAdminBundle\Serializer\Values\View;
use Netgen\Bundle\LayoutsBundle\Controller\AbstractController;
use Netgen\Layouts\API\Service\BlockService;
use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\View\ViewInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class EditForm extends AbstractController
{
    public function __construct(
        private BlockService $blockService,
    ) {}

    /**
     * Displays and processes block draft edit form.
     */
    public function __invoke(Block $block, string $locale, string $formName, Request $request): ViewInterface|View|Response
    {
        $updateStruct = $this->blockService->newBlockUpdateStruct($locale, $block);

        $form = $this->createForm(
            $block->definition->getForm($formName)->type,
            $updateStruct,
            [
                'block' => $block,
                'action' => $this->generateUrl(
                    'nglayouts_app_block_form_edit',
                    [
                        'blockId' => $block->id->toString(),
                        'locale' => $locale,
                        'formName' => $formName,
                    ],
                ),
            ],
        );

        $form->handleRequest($request);

        if (!$form->isSubmitted()) {
            $this->denyAccessUnlessGranted('nglayouts:api:read');

            return $this->buildView($form, ViewInterface::CONTEXT_APP);
        }

        $this->denyAccessUnlessGranted(
            'nglayouts:block:edit',
            [
                'block_definition' => $block->definition,
                'layout' => $block->layoutId->toString(),
            ],
        );

        if ($form->isValid()) {
            $updatedBlock = $this->blockService->updateBlock($block, $form->getData());

            return new View($updatedBlock);
        }

        return $this->buildView(
            $form,
            ViewInterface::CONTEXT_APP,
            [],
            new Response(null, Response::HTTP_UNPROCESSABLE_ENTITY),
        );
    }
}
