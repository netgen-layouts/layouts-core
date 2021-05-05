<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Controller\App\Block;

use Netgen\Bundle\LayoutsBundle\Controller\AbstractController;
use Netgen\Layouts\API\Values\Block\Block;
use Symfony\Component\HttpFoundation\Response;

final class Edit extends AbstractController
{
    /**
     * Displays block edit interface.
     */
    public function __invoke(Block $block): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');

        return $this->render(
            '@NetgenLayoutsAdmin/app/block/edit.html.twig',
            [
                'block' => $block,
            ],
        );
    }
}
