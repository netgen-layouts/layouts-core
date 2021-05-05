<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\Collection;

use Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class DeleteSlotTest extends JsonApiTestCase
{
    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Controller\API\Collection\DeleteSlot::__construct
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Controller\API\Collection\DeleteSlot::__invoke
     */
    public function testDeleteSlot(): void
    {
        $this->client->request(
            Request::METHOD_DELETE,
            '/nglayouts/app/api/collections/slots/de3a0641-c67f-48e0-96e7-7c83b6735265',
            [],
            [],
            [],
            $this->jsonEncode([]),
        );

        $this->assertEmptyResponse($this->client->getResponse());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Controller\API\Collection\DeleteSlot::__invoke
     */
    public function testDeleteSlotWithNonExistentSlot(): void
    {
        $this->client->request(
            Request::METHOD_DELETE,
            '/nglayouts/app/api/collections/slots/ffffffff-ffff-ffff-ffff-ffffffffffff',
            [],
            [],
            [],
            $this->jsonEncode([]),
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_NOT_FOUND,
            'Could not find slot with identifier "ffffffff-ffff-ffff-ffff-ffffffffffff"',
        );
    }
}
