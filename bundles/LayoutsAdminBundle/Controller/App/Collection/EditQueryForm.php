<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Controller\App\Collection;

use Netgen\Bundle\LayoutsBundle\Controller\AbstractController;
use Netgen\Layouts\API\Service\CollectionService;
use Netgen\Layouts\API\Values\Collection\Query;
use Netgen\Layouts\Collection\Form\QueryEditType;
use Netgen\Layouts\View\ViewInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class EditQueryForm extends AbstractController
{
    private CollectionService $collectionService;

    public function __construct(CollectionService $collectionService)
    {
        $this->collectionService = $collectionService;
    }

    /**
     * Displays and processes query draft edit form.
     *
     * @return \Netgen\Layouts\View\ViewInterface|\Symfony\Component\HttpFoundation\Response
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
                    'nglayouts_app_collection_query_form_edit',
                    [
                        'queryId' => $query->getId()->toString(),
                        'locale' => $locale,
                    ],
                ),
            ],
        );

        $form->handleRequest($request);

        if (!$form->isSubmitted()) {
            $this->denyAccessUnlessGranted('nglayouts:api:read');

            return $this->buildView($form, ViewInterface::CONTEXT_APP);
        }

        $this->denyAccessUnlessGranted('nglayouts:collection:edit');

        if ($form->isValid()) {
            $this->collectionService->updateQuery($query, $form->getData());

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
