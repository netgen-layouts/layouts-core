<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\Layout;

use Netgen\Bundle\LayoutsAdminBundle\Controller\API\Layout\DiscardDraft;
use Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\JsonApiTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[CoversClass(DiscardDraft::class)]
final class DiscardDraftTest extends JsonApiTestCase
{
    public function testDiscardDraft(): void
    {
        $this->client->request(
            Request::METHOD_DELETE,
            '/nglayouts/app/api/layouts/81168ed3-86f9-55ea-b153-101f96f2c136/draft',
            [],
            [],
            [],
            $this->jsonEncode([]),
        );

        $this->assertEmptyResponse($this->client->getResponse());
    }

    public function testDiscardDraftWithNonExistentLayout(): void
    {
        $this->client->request(
            Request::METHOD_DELETE,
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
