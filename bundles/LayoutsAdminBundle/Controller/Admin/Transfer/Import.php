<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Controller\Admin\Transfer;

use Netgen\Bundle\LayoutsAdminBundle\Form\Admin\Type\ImportType;
use Netgen\Bundle\LayoutsBundle\Controller\AbstractController;
use Netgen\Layouts\Exception\Transfer\ImportException;
use Netgen\Layouts\Transfer\Input\ImporterInterface;
use Netgen\Layouts\Transfer\Input\ImportOptions;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use function file_get_contents;

final class Import extends AbstractController
{
    private ImporterInterface $importer;

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
            ],
        );

        $form->handleRequest($request);

        $results = [];

        if ($form->isSubmitted() && $form->isValid()) {
            $jsonFile = $form->get('file')->getData();

            if ($jsonFile instanceof UploadedFile && $jsonFile->isValid()) {
                $options = (new ImportOptions())
                    ->setMode($form->get('import_mode')->getData());

                $json = (string) file_get_contents($jsonFile->getPathname());

                try {
                    foreach ($this->importer->importData($json, $options) as $result) {
                        $results[] = $result;
                    }
                } catch (ImportException $e) {
                    $form->get('file')->addError(
                        new FormError($e->getMessage(), null, [], null, $e),
                    );
                }
            }
        }

        return $this->render(
            '@NetgenLayoutsAdmin/admin/transfer/import.html.twig',
            [
                'form' => $form->createView(),
                'results' => $results,
            ],
        );
    }
}
