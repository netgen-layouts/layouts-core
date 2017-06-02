<?php

namespace Netgen\Bundle\BlockManagerAdminBundle\Controller\App;

use Netgen\BlockManager\API\Service\BlockService;
use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\Config\Form\EditType as ConfigEditType;
use Netgen\BlockManager\Exception\Core\ConfigException;
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

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\API\Service\BlockService $blockService
     */
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
        $collectionReferences = $this->blockService->loadCollectionReferences($block);

        $collections = array();
        foreach ($collectionReferences as $collectionReference) {
            $collections[$collectionReference->getIdentifier()] = $collectionReference->getCollection();
        }

        return $this->render(
            'NetgenBlockManagerAdminBundle:app/block:edit.html.twig',
            array(
                'block' => $block,
                'collections' => $collections,
            )
        );
    }

    /**
     * Displays and processes block draft edit form.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     * @param string $formName
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Netgen\BlockManager\View\ViewInterface|\Symfony\Component\HttpFoundation\Response
     */
    public function editForm(Block $block, $formName, Request $request)
    {
        $blockDefinition = $block->getDefinition();
        $blockDefinitionConfig = $blockDefinition->getConfig();

        $updateStruct = $this->blockService->newBlockUpdateStruct($block);

        $form = $this->createForm(
            $blockDefinitionConfig->getForm($formName)->getType(),
            $updateStruct,
            array(
                'block' => $block,
                'action' => $this->generateUrl(
                    'ngbm_app_block_form_edit',
                    array(
                        'blockId' => $block->getId(),
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
     * Displays and processes block config edit form.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     * @param string $configKey
     *
     * @throws \Netgen\BlockManager\Exception\Core\ConfigException If config key does not exist
     *
     * @return \Netgen\BlockManager\View\ViewInterface|\Symfony\Component\HttpFoundation\Response
     */
    public function editConfigForm(Request $request, Block $block, $configKey = null)
    {
        if ($configKey !== null) {
            if (!$block->isConfigEnabled($configKey)) {
                throw ConfigException::configNotEnabled($configKey);
            }
        }

        $updateStruct = $this->blockService->newBlockUpdateStruct($block);

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
     * Performs access checks on the controller.
     */
    protected function checkPermissions()
    {
        $this->denyAccessUnlessGranted('ROLE_NGBM_EDITOR');
    }
}
