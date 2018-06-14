<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerAdminBundle\Controller\App\Block;

use Netgen\BlockManager\API\Service\BlockService;
use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\Block\Form\ConfigureTranslationType;
use Netgen\BlockManager\View\ViewInterface;
use Netgen\Bundle\BlockManagerAdminBundle\Controller\App\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class ConfigureTranslationForm extends Controller
{
    /**
     * @var \Netgen\BlockManager\API\Service\BlockService
     */
    private $blockService;

    public function __construct(BlockService $blockService)
    {
        $this->blockService = $blockService;
    }

    /**
     * Displays and processes form for configuring the block translations.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     * @param string $locale
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Netgen\BlockManager\View\ViewInterface|\Symfony\Component\HttpFoundation\Response
     */
    public function __invoke(Block $block, string $locale, Request $request)
    {
        $form = $this->createForm(
            ConfigureTranslationType::class,
            null,
            [
                'block' => $block,
                'action' => $this->generateUrl(
                    'ngbm_app_block_form_configure_translation',
                    [
                        'blockId' => $block->getId(),
                        'locale' => $locale,
                    ]
                ),
            ]
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $isTranslatable = $form->get('translatable')->getData();

            if ($block->isTranslatable() && $isTranslatable !== true) {
                $this->blockService->disableTranslations($block);
            } elseif (!$block->isTranslatable() && $isTranslatable === true) {
                $this->blockService->enableTranslations($block);
            }

            return new Response(null, Response::HTTP_NO_CONTENT);
        }

        return $this->buildView(
            $form,
            ViewInterface::CONTEXT_API,
            [],
            new Response(
                null,
                $form->isSubmitted() ?
                    Response::HTTP_UNPROCESSABLE_ENTITY :
                    Response::HTTP_OK
            )
        );
    }
}
