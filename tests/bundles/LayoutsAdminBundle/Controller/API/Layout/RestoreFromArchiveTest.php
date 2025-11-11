<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\Layout;

use Netgen\Bundle\LayoutsAdminBundle\Controller\API\Layout\RestoreFromArchive;
use Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\JsonApiTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[CoversClass(RestoreFromArchive::class)]
final class RestoreFromArchiveTest extends JsonApiTestCase
{
    public function testRestoreFromArchive(): void
    {
        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/app/api/layouts/71cbe281-430c-51d5-8e21-c3cc4e656dac/restore',
            [],
            [],
            [],
            $this->jsonEncode([]),
        );

        $this->assertEmptyResponse($this->client->getResponse());
    }

    public function testRestoreFromArchiveWithNonExistentLayout(): void
    {
        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/app/api/layouts/ffffffff-ffff-ffff-ffff-ffffffffffff/restore',
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
