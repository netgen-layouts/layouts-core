<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\Controller\API\Layout;

use Netgen\Bundle\LayoutsBundle\Tests\Controller\API\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class RestoreFromArchiveTest extends JsonApiTestCase
{
    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Controller\API\Layout\RestoreFromArchive::__construct
     * @covers \Netgen\Bundle\LayoutsBundle\Controller\API\Layout\RestoreFromArchive::__invoke
     */
    public function testRestoreFromArchive(): void
    {
        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/api/v1/layouts/71cbe281-430c-51d5-8e21-c3cc4e656dac/restore',
            [],
            [],
            [],
            $this->jsonEncode([])
        );

        $this->assertEmptyResponse($this->client->getResponse());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Controller\API\Layout\RestoreFromArchive::__invoke
     */
    public function testRestoreFromArchiveWithNonExistentLayout(): void
    {
        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/api/v1/layouts/ffffffff-ffff-ffff-ffff-ffffffffffff/restore',
            [],
            [],
            [],
            $this->jsonEncode([])
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_NOT_FOUND,
            'Could not find layout with identifier "ffffffff-ffff-ffff-ffff-ffffffffffff"'
        );
    }
}
