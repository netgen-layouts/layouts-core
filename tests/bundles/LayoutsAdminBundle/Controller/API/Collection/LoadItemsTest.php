<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\Collection;

use Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class LoadItemsTest extends JsonApiTestCase
{
    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Controller\API\Collection\LoadItems::__invoke
     */
    public function testLoadItems(): void
    {
        $this->client->request(Request::METHOD_GET, '/nglayouts/app/api/collections/da050624-8ae0-5fb9-ae85-092bf8242b89/items');

        $this->assertResponse(
            $this->client->getResponse(),
            'collections/load_collection_items',
            Response::HTTP_OK,
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Controller\API\Collection\LoadItems::__invoke
     */
    public function testLoadItemsWithNonExistentCollection(): void
    {
        $this->client->request(Request::METHOD_GET, '/nglayouts/app/api/collections/ffffffff-ffff-ffff-ffff-ffffffffffff/items');

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_NOT_FOUND,
            'Could not find collection with identifier "ffffffff-ffff-ffff-ffff-ffffffffffff"',
        );
    }
}
