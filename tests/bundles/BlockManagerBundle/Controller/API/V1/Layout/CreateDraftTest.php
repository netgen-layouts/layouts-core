<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Tests\Controller\API\V1\Layout;

use Netgen\Bundle\BlockManagerBundle\Tests\Controller\API\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class CreateDraftTest extends JsonApiTestCase
{
    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\CreateDraft::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\CreateDraft::__invoke
     */
    public function testCreateDraft(): void
    {
        $data = $this->jsonEncode([]);

        $this->client->request(
            Request::METHOD_POST,
            '/bm/api/v1/layouts/1/draft?html=false',
            [],
            [],
            [],
            $data
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'v1/layouts/create_layout_draft',
            Response::HTTP_CREATED
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\CreateDraft::__invoke
     */
    public function testCreateDraftWithNonExistentLayout(): void
    {
        $data = $this->jsonEncode([]);

        $this->client->request(
            Request::METHOD_POST,
            '/bm/api/v1/layouts/9999/draft',
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
