<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\Controller\API\V1\Layout;

use Netgen\Bundle\LayoutsBundle\Tests\Controller\API\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class DeleteTest extends JsonApiTestCase
{
    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Controller\API\V1\Layout\Delete::__construct
     * @covers \Netgen\Bundle\LayoutsBundle\Controller\API\V1\Layout\Delete::__invoke
     */
    public function testDelete(): void
    {
        $data = $this->jsonEncode([]);

        $this->client->request(
            Request::METHOD_DELETE,
            '/nglayouts/api/v1/layouts/81168ed3-86f9-55ea-b153-101f96f2c136',
            [],
            [],
            [],
            $data
        );

        $this->assertEmptyResponse($this->client->getResponse());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Controller\API\V1\Layout\Delete::__invoke
     */
    public function testDeleteWithNonExistentLayout(): void
    {
        $data = $this->jsonEncode([]);

        $this->client->request(
            Request::METHOD_DELETE,
            '/nglayouts/api/v1/layouts/ffffffff-ffff-ffff-ffff-ffffffffffff',
            [],
            [],
            [],
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_NOT_FOUND,
            'Could not find layout with identifier "ffffffff-ffff-ffff-ffff-ffffffffffff"'
        );
    }
}
