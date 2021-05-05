<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Controller\App\Collection;

use Netgen\Bundle\LayoutsBundle\Controller\AbstractController;
use Netgen\Layouts\API\Service\BlockService;
use Netgen\Layouts\API\Service\CollectionService;
use Netgen\Layouts\API\Values\Collection\Item;
use Netgen\Layouts\Collection\Form\ItemViewTypeEditType;
use Netgen\Layouts\View\ViewInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class EditItemViewTypeForm extends AbstractController
{
    private BlockService $blockService;

    private CollectionService $collectionService;

    public function __construct(BlockService $blockService, CollectionService $collectionService)
    {
        $this->blockService = $blockService;
        $this->collectionService = $collectionService;
    }

    /**
     * Displays and processes item view type edit form.
     *
     * @return \Netgen\Layouts\View\ViewInterface|\Symfony\Component\HttpFoundation\Response
     */
    public function __invoke(Item $item, Request $request)
    {
        $this->denyAccessUnlessGranted('nglayouts:collection:items');

        $collection = $this->collectionService->loadCollectionDraft($item->getCollectionId());
        $block = $this->blockService->loadBlockDraft($collection->getBlockId());

        $updateStruct = $this->collectionService->newItemUpdateStruct();
        $updateStruct->viewType = $item->getViewType();

        $form = $this->createForm(
            ItemViewTypeEditType::class,
            $updateStruct,
            [
                'item' => $item,
                'block' => $block,
                'action' => $this->generateUrl(
                    'nglayouts_app_collection_item_view_type_form_edit',
                    [
                        'itemId' => $item->getId()->toString(),
                    ],
                ),
            ],
        );

        $form->handleRequest($request);

        if (!$form->isSubmitted()) {
            return $this->buildView($form, ViewInterface::CONTEXT_APP);
        }

        if ($form->isValid()) {
            $this->collectionService->updateItem($item, $form->getData());

            return new Response(null, Response::HTTP_NO_CONTENT);
        }

        return $this->buildView(
            $form,
            ViewInterface::CONTEXT_APP,
            [],
            new Response(null, Response::HTTP_UNPROCESSABLE_ENTITY),
        );
    }
}
