<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\Controller\API\V1\Collection;

use Netgen\Bundle\BlockManagerBundle\Tests\Controller\API\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Response;

final class LoadItemTest extends JsonApiTestCase
{
    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Collection\LoadItem::__invoke
     */
    public function testLoadItem()
    {
        $this->client->request('GET', '/bm/api/v1/collections/items/7');

        $this->assertResponse(
            $this->client->getResponse(),
            'v1/collections/load_item',
            Response::HTTP_OK
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Collection\LoadItem::__invoke
     */
    public function testLoadItemWithNonExistentItem()
    {
        $this->client->request('GET', '/bm/api/v1/collections/items/9999');

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_NOT_FOUND,
            'Could not find item with identifier "9999"'
        );
    }
}
