<?php

namespace Netgen\Bundle\BlockManagerAdminBundle\Controller\App;

use Netgen\BlockManager\API\Service\CollectionService;
use Netgen\BlockManager\View\ViewInterface;
use Netgen\Bundle\BlockManagerBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CollectionController extends Controller
{
    /**
     * @var \Netgen\BlockManager\API\Service\CollectionService
     */
    protected $collectionService;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\API\Service\CollectionService $collectionService
     */
    public function __construct(CollectionService $collectionService)
    {
        $this->collectionService = $collectionService;
    }

    /**
     * Displays and processes query draft edit form.
     *
     * @param int|string $queryId
     * @param string $formName
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Netgen\BlockManager\View\ViewInterface|\Symfony\Component\HttpFoundation\Response
     */
    public function queryEditForm($queryId, $formName, Request $request)
    {
        $query = $this->collectionService->loadQueryDraft($queryId);

        $queryTypeConfig = $query->getQueryType()->getConfig();

        $updateStruct = $this->collectionService->newQueryUpdateStruct($query);

        $form = $this->createForm(
            $queryTypeConfig->getForm($formName)->getType(),
            $updateStruct,
            array(
                'query' => $query,
                'action' => $this->generateUrl(
                    'ngbm_app_collection_query_form_edit',
                    array(
                        'queryId' => $query->getId(),
                        'formName' => $formName,
                    )
                ),
            )
        );

        $form->handleRequest($request);

        if (!$form->isSubmitted()) {
            return $this->buildView($form, array(), ViewInterface::CONTEXT_API);
        }

        if ($form->isValid()) {
            $this->collectionService->updateQuery($query, $form->getData());

            return new Response(null, Response::HTTP_NO_CONTENT);
        }

        return $this->buildView(
            $form,
            array(),
            ViewInterface::CONTEXT_API,
            new Response(null, Response::HTTP_UNPROCESSABLE_ENTITY)
        );
    }
}
