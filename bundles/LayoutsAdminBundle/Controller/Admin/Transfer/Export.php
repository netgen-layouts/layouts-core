<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Controller\Admin\Transfer;

use Netgen\Bundle\LayoutsBundle\Controller\AbstractController;
use Netgen\Layouts\Transfer\Output\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\String\Inflector\EnglishInflector;

use function count;
use function date;
use function json_encode;
use function reset;
use function sprintf;

use const JSON_PRETTY_PRINT;
use const JSON_THROW_ON_ERROR;

final class Export extends AbstractController
{
    private SerializerInterface $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * Exports the provided list of entities.
     */
    public function __invoke(Request $request): Response
    {
        $this->denyAccessUnlessGranted('nglayouts:ui:access');

        /** @var string[] $entityIds */
        $entityIds = Kernel::VERSION_ID >= 50100 ?
            $request->request->all('entities') :
            (array) ($request->request->get('entities') ?? []);

        if (count($entityIds) === 0) {
            throw new BadRequestHttpException('List of entities to export cannot be empty.');
        }

        $serializedEntities = $this->serializer->serialize($entityIds);
        $json = json_encode($serializedEntities, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR);
        $response = new Response($json);

        $exportType = $this->getTypePlural(reset($entityIds));
        $fileName = sprintf('netgen_layouts_export_%s_%s.json', $exportType, date('Y-m-d_H-i-s'));
        $disposition = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $fileName,
        );

        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Content-Disposition', $disposition);
        // X-Filename header is needed for AJAX file download support
        $response->headers->set('X-Filename', $fileName);

        return $response;
    }

    /**
     * Returns a plural form of the provided entity type.
     */
    private function getTypePlural(string $type): string
    {
        return (new EnglishInflector())->pluralize($type)[0] ?? $type;
    }
}
