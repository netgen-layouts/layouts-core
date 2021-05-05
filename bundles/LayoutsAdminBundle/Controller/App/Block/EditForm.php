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
    private BlockService $blockService;

    public function __construct(BlockService $blockService)
    {
        $this->blockService = $blockService;
    }

    /**
     * Displays and processes block draft edit form.
     *
     * @return \Netgen\Layouts\View\ViewInterface|\Netgen\Bundle\LayoutsAdminBundle\Serializer\Values\View|\Symfony\Component\HttpFoundation\Response
     */
    public function __invoke(Block $block, string $locale, string $formName, Request $request)
    {
        $updateStruct = $this->blockService->newBlockUpdateStruct($locale, $block);

        $form = $this->createForm(
            $block->getDefinition()->getForm($formName)->getType(),
            $updateStruct,
            [
                'block' => $block,
                'action' => $this->generateUrl(
                    'nglayouts_app_block_form_edit',
                    [
                        'blockId' => $block->getId()->toString(),
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
                'block_definition' => $block->getDefinition(),
                'layout' => $block->getLayoutId()->toString(),
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
