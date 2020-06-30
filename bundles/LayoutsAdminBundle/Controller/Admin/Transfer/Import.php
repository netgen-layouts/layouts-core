<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Controller\Admin\Transfer;

use Netgen\Bundle\LayoutsAdminBundle\Form\Admin\Type\ImportType;
use Netgen\Bundle\LayoutsBundle\Controller\AbstractController;
use Netgen\Layouts\Transfer\Input\ImporterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use function file_get_contents;

final class Import extends AbstractController
{
    /**
     * @var \Netgen\Layouts\Transfer\Input\ImporterInterface
     */
    private $importer;

    public function __construct(ImporterInterface $importer)
    {
        $this->importer = $importer;
    }

    /**
     * Displays and processes the form for importing various entities.
     */
    public function __invoke(Request $request): Response
    {
        $this->denyAccessUnlessGranted('nglayouts:ui:access');

        $form = $this->createForm(
            ImportType::class,
            null,
            [
                'action' => $this->generateUrl('nglayouts_admin_transfer_index'),
            ]
        );

        $form->handleRequest($request);

        $results = [];

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var \Symfony\Component\HttpFoundation\File\UploadedFile $jsonFile */
            $jsonFile = $form->get('file')->getData();
            $overwriteExisting = $form->get('overwriteExisting')->getData();

            $json = (string) file_get_contents($jsonFile->getPathname());
            foreach ($this->importer->importData($json, $overwriteExisting) as $result) {
                $results[] = $result;
            }
        }

        return $this->render(
            '@NetgenLayoutsAdmin/admin/transfer/import.html.twig',
            [
                'form' => $form->createView(),
                'results' => $results,
            ]
        );
    }
}
