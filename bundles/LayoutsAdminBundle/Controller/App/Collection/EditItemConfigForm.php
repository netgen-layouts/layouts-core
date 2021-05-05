<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Controller\App\Collection;

use Netgen\Bundle\LayoutsBundle\Controller\AbstractController;
use Netgen\Layouts\API\Service\CollectionService;
use Netgen\Layouts\API\Values\Collection\Item;
use Netgen\Layouts\Config\Form\EditType;
use Netgen\Layouts\View\ViewInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class EditItemConfigForm extends AbstractController
{
    private CollectionService $collectionService;

    public function __construct(CollectionService $collectionService)
    {
        $this->collectionService = $collectionService;
    }

    /**
     * Displays and processes item config edit form.
     *
     * @return \Netgen\Layouts\View\ViewInterface|\Symfony\Component\HttpFoundation\Response
     */
    public function __invoke(Request $request, Item $item, ?string $configKey = null)
    {
        $this->denyAccessUnlessGranted('nglayouts:collection:items');

        $updateStruct = $this->collectionService->newItemUpdateStruct($item);

        $form = $this->createForm(
            EditType::class,
            $updateStruct,
            [
                'configurable' => $item,
                'config_key' => $configKey,
                'label_prefix' => 'config.collection_item',
                'action' => $this->generateUrl(
                    'nglayouts_app_collection_form_edit_item_config',
                    [
                        'itemId' => $item->getId(),
                        'configKey' => $configKey,
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
