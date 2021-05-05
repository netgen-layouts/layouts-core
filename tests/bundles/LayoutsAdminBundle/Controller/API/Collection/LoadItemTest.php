<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\Collection;

use Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class LoadItemTest extends JsonApiTestCase
{
    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Controller\API\Collection\LoadItem::__invoke
     */
    public function testLoadItem(): void
    {
        $this->client->request(Request::METHOD_GET, '/nglayouts/app/api/collections/items/89c214a3-204f-5352-85d7-8852b26ab6b0');

        $this->assertResponse(
            $this->client->getResponse(),
            'collections/load_item',
            Response::HTTP_OK,
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Controller\API\Collection\LoadItem::__invoke
     */
    public function testLoadItemWithNonExistentItem(): void
    {
        $this->client->request(Request::METHOD_GET, '/nglayouts/app/api/collections/items/ffffffff-ffff-ffff-ffff-ffffffffffff');

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_NOT_FOUND,
            'Could not find item with identifier "ffffffff-ffff-ffff-ffff-ffffffffffff"',
        );
    }
}
