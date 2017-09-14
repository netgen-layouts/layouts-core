<?php

namespace Netgen\Bundle\BlockManagerAdminBundle\Controller\App;

use Netgen\BlockManager\API\Service\BlockService;
use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\Block\Form\ConfigureTranslationType;
use Netgen\BlockManager\Config\Form\EditType as ConfigEditType;
use Netgen\BlockManager\Exception\Core\ConfigException;
use Netgen\BlockManager\Serializer\Values\View;
use Netgen\BlockManager\Serializer\Version;
use Netgen\BlockManager\View\ViewInterface;
use Netgen\Bundle\BlockManagerBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BlockController extends Controller
{
    /**
     * @var \Netgen\BlockManager\API\Service\BlockService
     */
    protected $blockService;

    public function __construct(BlockService $blockService)
    {
        $this->blockService = $blockService;
    }

    /**
     * Displays block edit interface.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function edit(Block $block)
    {
        return $this->render(
            '@NetgenBlockManagerAdmin/app/block/edit.html.twig',
            array(
                'block' => $block,
            )
        );
    }

    /**
     * Displays and processes block draft edit form.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     * @param string $locale
     * @param string $formName
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Netgen\BlockManager\View\ViewInterface|\Netgen\BlockManager\Serializer\Values\View|\Symfony\Component\HttpFoundation\Response
     */
    public function editForm(Block $block, $locale, $formName, Request $request)
    {
        $blockDefinition = $block->getDefinition();
        $blockDefinitionConfig = $blockDefinition->getConfig();

        $updateStruct = $this->blockService->newBlockUpdateStruct($locale, $block);

        $form = $this->createForm(
            $blockDefinitionConfig->getForm($formName)->getType(),
            $updateStruct,
            array(
                'block' => $block,
                'action' => $this->generateUrl(
                    'ngbm_app_block_form_edit',
                    array(
                        'blockId' => $block->getId(),
                        'locale' => $locale,
                        'formName' => $formName,
                    )
                ),
            )
        );

        $form->handleRequest($request);

        if (!$form->isSubmitted()) {
            return $this->buildView($form, ViewInterface::CONTEXT_API);
        }

        if ($form->isValid()) {
            $block = $this->blockService->updateBlock($block, $form->getData());

            return new View($block, Version::API_V1);
        }

        return $this->buildView(
            $form,
            ViewInterface::CONTEXT_API,
            array(),
            new Response(null, Response::HTTP_UNPROCESSABLE_ENTITY)
        );
    }

    /**
     * Displays and processes block config edit form.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     * @param string $locale
     * @param string $configKey
     *
     * @throws \Netgen\BlockManager\Exception\Core\ConfigException If config key does not exist
     *
     * @return \Netgen\BlockManager\View\ViewInterface|\Symfony\Component\HttpFoundation\Response
     */
    public function editConfigForm(Request $request, Block $block, $locale, $configKey = null)
    {
        if ($configKey !== null) {
            if (!$block->isConfigEnabled($configKey)) {
                throw ConfigException::configNotEnabled($configKey);
            }
        }

        $updateStruct = $this->blockService->newBlockUpdateStruct($locale, $block);

        $form = $this->createForm(
            ConfigEditType::class,
            $updateStruct,
            array(
                'configurable' => $block,
                'config_key' => $configKey,
                'label_prefix' => 'config.block',
                'action' => $this->generateUrl(
                    'ngbm_app_block_form_edit_config',
                    array(
                        'blockId' => $block->getId(),
                        'locale' => $locale,
                        'configKey' => $configKey,
                    )
                ),
            )
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
            array(),
            new Response(null, Response::HTTP_UNPROCESSABLE_ENTITY)
        );
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
    public function configureTranslationForm(Block $block, $locale, Request $request)
    {
        $form = $this->createForm(
            ConfigureTranslationType::class,
            null,
            array(
                'block' => $block,
                'action' => $this->generateUrl(
                    'ngbm_app_block_form_configure_translation',
                    array(
                        'blockId' => $block->getId(),
                        'locale' => $locale,
                    )
                ),
            )
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $isTranslatable = $form->get('translatable')->getData();

            if ($block->isTranslatable() && !$isTranslatable) {
                $this->blockService->disableTranslations($block);
            } elseif (!$block->isTranslatable() && $isTranslatable) {
                $this->blockService->enableTranslations($block);
            }

            return new Response(null, Response::HTTP_NO_CONTENT);
        }

        return $this->buildView(
            $form,
            ViewInterface::CONTEXT_API,
            array(),
            new Response(
                null,
                $form->isSubmitted() ?
                    Response::HTTP_UNPROCESSABLE_ENTITY :
                    Response::HTTP_OK
            )
        );
    }

    protected function checkPermissions()
    {
        $this->denyAccessUnlessGranted('ROLE_NGBM_EDITOR');
    }
}
