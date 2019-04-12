<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\Controller\API\V1\Layout;

use Netgen\Bundle\LayoutsBundle\Tests\Controller\API\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class DiscardDraftTest extends JsonApiTestCase
{
    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Controller\API\V1\Layout\DiscardDraft::__construct
     * @covers \Netgen\Bundle\LayoutsBundle\Controller\API\V1\Layout\DiscardDraft::__invoke
     */
    public function testDiscardDraft(): void
    {
        $data = $this->jsonEncode([]);

        $this->client->request(
            Request::METHOD_DELETE,
            '/nglayouts/api/v1/layouts/1/draft',
            [],
            [],
            [],
            $data
        );

        $this->assertEmptyResponse($this->client->getResponse());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Controller\API\V1\Layout\DiscardDraft::__invoke
     */
    public function testDiscardDraftWithNonExistentLayout(): void
    {
        $data = $this->jsonEncode([]);

        $this->client->request(
            Request::METHOD_DELETE,
            '/nglayouts/api/v1/layouts/9999/draft',
            [],
            [],
            [],
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_NOT_FOUND,
            'Could not find layout with identifier "9999"'
        );
    }
}
