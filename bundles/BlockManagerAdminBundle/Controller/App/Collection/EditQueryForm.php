<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerAdminBundle\Controller\App\Collection;

use Netgen\BlockManager\API\Service\CollectionService;
use Netgen\BlockManager\API\Values\Collection\Query;
use Netgen\BlockManager\Collection\Form\QueryEditType;
use Netgen\BlockManager\View\ViewInterface;
use Netgen\Bundle\BlockManagerBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class EditQueryForm extends Controller
{
    /**
     * @var \Netgen\BlockManager\API\Service\CollectionService
     */
    private $collectionService;

    public function __construct(CollectionService $collectionService)
    {
        $this->collectionService = $collectionService;
    }

    /**
     * Displays and processes query draft edit form.
     *
     * @return \Netgen\BlockManager\View\ViewInterface|\Symfony\Component\HttpFoundation\Response
     */
    public function __invoke(Query $query, string $locale, Request $request)
    {
        $updateStruct = $this->collectionService->newQueryUpdateStruct($locale, $query);

        $form = $this->createForm(
            QueryEditType::class,
            $updateStruct,
            [
                'query' => $query,
                'action' => $this->generateUrl(
                    'ngbm_app_collection_query_form_edit',
                    [
                        'queryId' => $query->getId(),
                        'locale' => $locale,
                    ]
                ),
            ]
        );

        $form->handleRequest($request);

        if (!$form->isSubmitted()) {
            return $this->buildView($form, ViewInterface::CONTEXT_API);
        }

        $this->denyAccessUnlessGranted('nglayouts:collection:edit');

        if ($form->isValid()) {
            $this->collectionService->updateQuery($query, $form->getData());

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
