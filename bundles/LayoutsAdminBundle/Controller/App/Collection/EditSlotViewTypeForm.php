<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Controller\App\Collection;

use Netgen\Bundle\LayoutsBundle\Controller\AbstractController;
use Netgen\Layouts\API\Service\BlockService;
use Netgen\Layouts\API\Service\CollectionService;
use Netgen\Layouts\API\Values\Collection\Slot;
use Netgen\Layouts\Collection\Form\SlotViewTypeEditType;
use Netgen\Layouts\View\ViewInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class EditSlotViewTypeForm extends AbstractController
{
    private BlockService $blockService;

    private CollectionService $collectionService;

    public function __construct(BlockService $blockService, CollectionService $collectionService)
    {
        $this->blockService = $blockService;
        $this->collectionService = $collectionService;
    }

    /**
     * Displays and processes slot view type edit form.
     *
     * @return \Netgen\Layouts\View\ViewInterface|\Symfony\Component\HttpFoundation\Response
     */
    public function __invoke(Slot $slot, Request $request)
    {
        $this->denyAccessUnlessGranted('nglayouts:collection:items');

        $collection = $this->collectionService->loadCollectionDraft($slot->getCollectionId());
        $block = $this->blockService->loadBlockDraft($collection->getBlockId());

        $updateStruct = $this->collectionService->newSlotUpdateStruct();
        $updateStruct->viewType = $slot->getViewType();

        $form = $this->createForm(
            SlotViewTypeEditType::class,
            $updateStruct,
            [
                'slot' => $slot,
                'block' => $block,
                'action' => $this->generateUrl(
                    'nglayouts_app_collection_slot_view_type_form_edit',
                    [
                        'slotId' => $slot->getId()->toString(),
                    ],
                ),
            ],
        );

        $form->handleRequest($request);

        if (!$form->isSubmitted()) {
            return $this->buildView($form, ViewInterface::CONTEXT_APP);
        }

        if ($form->isValid()) {
            $updatedSlot = $this->collectionService->updateSlot($slot, $form->getData());

            if ($updatedSlot->isEmpty()) {
                $this->collectionService->deleteSlot($updatedSlot);
            }

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
