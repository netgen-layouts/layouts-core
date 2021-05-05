<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\Layout;

use Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class DeleteTest extends JsonApiTestCase
{
    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Controller\API\Layout\Delete::__construct
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Controller\API\Layout\Delete::__invoke
     */
    public function testDelete(): void
    {
        $this->client->request(
            Request::METHOD_DELETE,
            '/nglayouts/app/api/layouts/81168ed3-86f9-55ea-b153-101f96f2c136',
            [],
            [],
            [],
            $this->jsonEncode([]),
        );

        $this->assertEmptyResponse($this->client->getResponse());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Controller\API\Layout\Delete::__invoke
     */
    public function testDeleteWithNonExistentLayout(): void
    {
        $this->client->request(
            Request::METHOD_DELETE,
            '/nglayouts/app/api/layouts/ffffffff-ffff-ffff-ffff-ffffffffffff',
            [],
            [],
            [],
            $this->jsonEncode([]),
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_NOT_FOUND,
            'Could not find layout with identifier "ffffffff-ffff-ffff-ffff-ffffffffffff"',
        );
    }
}
