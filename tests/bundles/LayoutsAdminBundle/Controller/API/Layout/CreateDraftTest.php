<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\Layout;

use Netgen\Bundle\LayoutsAdminBundle\Controller\API\Layout\CreateDraft;
use Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\JsonApiTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[CoversClass(CreateDraft::class)]
final class CreateDraftTest extends JsonApiTestCase
{
    public function testCreateDraft(): void
    {
        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/app/api/layouts/81168ed3-86f9-55ea-b153-101f96f2c136/draft?html=false',
            [],
            [],
            [],
            $this->jsonEncode([]),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'layouts/create_layout_draft',
            Response::HTTP_CREATED,
        );
    }

    public function testCreateDraftWithNonExistentLayout(): void
    {
        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/app/api/layouts/ffffffff-ffff-ffff-ffff-ffffffffffff/draft',
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
