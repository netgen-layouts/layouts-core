<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\Controller\API\V1\Collection;

use Netgen\Bundle\LayoutsBundle\Tests\Controller\API\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class LoadItemTest extends JsonApiTestCase
{
    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Controller\API\V1\Collection\LoadItem::__invoke
     */
    public function testLoadItem(): void
    {
        $this->client->request(Request::METHOD_GET, '/bm/api/v1/collections/items/7');

        self::assertResponse(
            $this->client->getResponse(),
            'v1/collections/load_item',
            Response::HTTP_OK
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Controller\API\V1\Collection\LoadItem::__invoke
     */
    public function testLoadItemWithNonExistentItem(): void
    {
        $this->client->request(Request::METHOD_GET, '/bm/api/v1/collections/items/9999');

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_NOT_FOUND,
            'Could not find item with identifier "9999"'
        );
    }
}
