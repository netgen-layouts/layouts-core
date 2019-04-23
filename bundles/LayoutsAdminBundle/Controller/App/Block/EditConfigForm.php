<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Controller\App\Block;

use Netgen\Bundle\LayoutsBundle\Controller\AbstractController;
use Netgen\Layouts\API\Service\BlockService;
use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\Config\Form\EditType;
use Netgen\Layouts\View\ViewInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class EditConfigForm extends AbstractController
{
    /**
     * @var \Netgen\Layouts\API\Service\BlockService
     */
    private $blockService;

    public function __construct(BlockService $blockService)
    {
        $this->blockService = $blockService;
    }

    /**
     * Displays and processes block config edit form.
     *
     * @return \Netgen\Layouts\View\ViewInterface|\Symfony\Component\HttpFoundation\Response
     */
    public function __invoke(Request $request, Block $block, string $locale, ?string $configKey = null)
    {
        $this->denyAccessUnlessGranted(
            'nglayouts:block:edit_config',
            [
                'block_definition' => $block->getDefinition(),
                'layout' => $block->getLayoutId()->toString(),
            ]
        );

        $updateStruct = $this->blockService->newBlockUpdateStruct($locale, $block);

        $form = $this->createForm(
            EditType::class,
            $updateStruct,
            [
                'configurable' => $block,
                'config_key' => $configKey,
                'label_prefix' => 'config.block',
                'action' => $this->generateUrl(
                    'nglayouts_app_block_form_edit_config',
                    [
                        'blockId' => $block->getId(),
                        'locale' => $locale,
                        'configKey' => $configKey,
                    ]
                ),
            ]
        );

        $form->handleRequest($request);

        if (!$form->isSubmitted()) {
            return $this->buildView($form, ViewInterface::CONTEXT_API);
        }

        if ($form->isValid()) {
            $this->blockService->updateBlock($block, $form->getData());

            return new Response(null, Response::HTTP_NO_CONTENT);
        }

        return $this->buildView(
            $form,
            ViewInterface::CONTEXT_API,
            [],
            new Response(null, Response::HTTP_UNPROCESSABLE_ENTITY)
        );
    }
}
