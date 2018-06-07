<?php

namespace Netgen\Bundle\BlockManagerAdminBundle\Controller\Admin\Layouts;

use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\BlockManager\Transfer\Output\SerializerInterface;
use Netgen\Bundle\BlockManagerAdminBundle\Controller\Admin\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

final class ExportLayouts extends Controller
{
    /**
     * @var \Netgen\BlockManager\API\Service\LayoutService
     */
    private $layoutService;

    /**
     * @var \Netgen\BlockManager\Transfer\Output\SerializerInterface
     */
    private $transferSerializer;

    public function __construct(LayoutService $layoutService, SerializerInterface $transferSerializer)
    {
        $this->layoutService = $layoutService;
        $this->transferSerializer = $transferSerializer;
    }

    /**
     * Exports the provided list of layouts.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function __invoke(Request $request)
    {
        $serializedLayouts = $this->transferSerializer->serializeLayouts(
            array_unique($request->request->get('layout_ids'))
        );

        $json = json_encode($serializedLayouts, JSON_PRETTY_PRINT);

        $response = new Response($json);

        $fileName = sprintf('layouts_export_%s.json', date('Y-m-d_H-i-s'));
        $disposition = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $fileName
        );

        $response->headers->set('Content-Disposition', $disposition);
        // X-Filename header is needed for AJAX file download support
        $response->headers->set('X-Filename', $fileName);

        return $response;
    }
}
