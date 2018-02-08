<?php

namespace Netgen\Bundle\BlockManagerAdminBundle\Controller\App;

use Netgen\BlockManager\API\Service\CollectionService;
use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\BlockManager\API\Values\Collection\Item;
use Netgen\BlockManager\API\Values\Collection\Query;
use Netgen\BlockManager\Collection\Form\EditType;
use Netgen\BlockManager\Config\Form\EditType as ConfigEditType;
use Netgen\BlockManager\Exception\Core\ConfigException;
use Netgen\BlockManager\Item\ItemLoaderInterface;
use Netgen\BlockManager\View\ViewInterface;
use Netgen\Bundle\BlockManagerBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class CollectionController extends Controller
{
    /**
     * @var \Netgen\BlockManager\API\Service\CollectionService
     */
    private $collectionService;

    /**
     * @var \Netgen\BlockManager\Item\ItemLoaderInterface
     */
    private $itemLoader;

    public function __construct(CollectionService $collectionService, ItemLoaderInterface $itemLoader)
    {
        $this->collectionService = $collectionService;
        $this->itemLoader = $itemLoader;
    }

    /**
     * Displays and processes collection draft edit form.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Collection $collection
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Netgen\BlockManager\View\ViewInterface|\Symfony\Component\HttpFoundation\Response
     */
    public function collectionEditForm(Collection $collection, Request $request)
    {
        $updateStruct = $this->collectionService->newCollectionUpdateStruct($collection);

        $form = $this->createForm(
            EditType::class,
            $updateStruct,
            array(
                'collection' => $collection,
                'action' => $this->generateUrl(
                    'ngbm_app_collection_collection_form_edit',
                    array(
                        'collectionId' => $collection->getId(),
                    )
                ),
            )
        );

        $form->handleRequest($request);

        if (!$form->isSubmitted()) {
            return $this->buildView($form, ViewInterface::CONTEXT_API);
        }

        if ($form->isValid()) {
            $this->collectionService->updateCollection($collection, $form->getData());

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
     * Displays and processes item config edit form.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Netgen\BlockManager\API\Values\Collection\Item $item
     * @param string $configKey
     *
     * @throws \Netgen\BlockManager\Exception\Core\ConfigException If config key does not exist
     *
     * @return \Netgen\BlockManager\View\ViewInterface|\Symfony\Component\HttpFoundation\Response
     */
    public function editItemConfigForm(Request $request, Item $item, $configKey = null)
    {
        if ($configKey !== null) {
            if (!$item->isConfigEnabled($configKey)) {
                throw ConfigException::configNotEnabled($configKey);
            }
        }

        $cmsItem = $this->itemLoader->load($item->getValue(), $item->getValueType());

        $updateStruct = $this->collectionService->newItemUpdateStruct($item);

        $form = $this->createForm(
            ConfigEditType::class,
            $updateStruct,
            array(
                'configurable' => $item,
                'config_key' => $configKey,
                'label_prefix' => 'config.collection_item',
                'action' => $this->generateUrl(
                    'ngbm_app_collection_form_edit_item_config',
                    array(
                        'itemId' => $item->getId(),
                        'configKey' => $configKey,
                    )
                ),
            )
        );

        $form->handleRequest($request);

        if (!$form->isSubmitted()) {
            return $this->buildView($form, ViewInterface::CONTEXT_API, array('cms_item' => $cmsItem));
        }

        if ($form->isValid()) {
            $this->collectionService->updateItem($item, $form->getData());

            return new Response(null, Response::HTTP_NO_CONTENT);
        }

        return $this->buildView(
            $form,
            ViewInterface::CONTEXT_API,
            array('cms_item' => $cmsItem),
            new Response(null, Response::HTTP_UNPROCESSABLE_ENTITY)
        );
    }

    /**
     * Displays and processes query draft edit form.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Query $query
     * @param string $locale
     * @param string $formName
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Netgen\BlockManager\View\ViewInterface|\Symfony\Component\HttpFoundation\Response
     */
    public function queryEditForm(Query $query, $locale, $formName, Request $request)
    {
        $updateStruct = $this->collectionService->newQueryUpdateStruct($locale, $query);

        $form = $this->createForm(
            $query->getQueryType()->getForm($formName)->getType(),
            $updateStruct,
            array(
                'query' => $query,
                'action' => $this->generateUrl(
                    'ngbm_app_collection_query_form_edit',
                    array(
                        'queryId' => $query->getId(),
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
            $this->collectionService->updateQuery($query, $form->getData());

            return new Response(null, Response::HTTP_NO_CONTENT);
        }

        return $this->buildView(
            $form,
            ViewInterface::CONTEXT_API,
            array(),
            new Response(null, Response::HTTP_UNPROCESSABLE_ENTITY)
        );
    }

    protected function checkPermissions()
    {
        $this->denyAccessUnlessGranted('ROLE_NGBM_EDITOR');
    }
}
