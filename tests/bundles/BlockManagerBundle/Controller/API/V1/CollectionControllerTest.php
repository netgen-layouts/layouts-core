<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\Controller\API\V1;

use Netgen\BlockManager\API\Values\Collection\Item;
use Netgen\Bundle\BlockManagerBundle\Tests\Controller\API\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Response;

class CollectionControllerTest extends JsonApiTestCase
{
    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\CollectionController::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\CollectionController::loadCollection
     */
    public function testLoadCollection()
    {
        $this->client->request('GET', '/bm/api/v1/collections/3');

        $this->assertResponse(
            $this->client->getResponse(),
            'v1/collections/load_collection',
            Response::HTTP_OK
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\CollectionController::loadCollection
     */
    public function testLoadCollectionWithNonExistentCollection()
    {
        $this->client->request('GET', '/bm/api/v1/collections/9999');

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_NOT_FOUND,
            'Could not find collection with identifier "9999"'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\CollectionController::loadCollectionItems
     */
    public function testLoadCollectionItems()
    {
        $this->client->request('GET', '/bm/api/v1/collections/3/items');

        $this->assertResponse(
            $this->client->getResponse(),
            'v1/collections/load_collection_items',
            Response::HTTP_OK
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\CollectionController::loadCollectionItems
     */
    public function testLoadCollectionItemsWithNonExistentCollection()
    {
        $this->client->request('GET', '/bm/api/v1/collections/9999/items');

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_NOT_FOUND,
            'Could not find collection with identifier "9999"'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\CollectionController::loadCollectionQuery
     */
    public function testLoadCollectionQuery()
    {
        $this->client->request('GET', '/bm/api/v1/collections/3/query');

        $this->assertResponse(
            $this->client->getResponse(),
            'v1/collections/load_collection_query',
            Response::HTTP_OK
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\CollectionController::loadCollectionQuery
     */
    public function testLoadCollectionQueryWithNonExistentCollection()
    {
        $this->client->request('GET', '/bm/api/v1/collections/9999/query');

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_NOT_FOUND,
            'Could not find collection with identifier "9999"'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\CollectionController::loadItem
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
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\CollectionController::loadItem
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

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\CollectionController::moveItem
     */
    public function testMoveItem()
    {
        $data = $this->jsonEncode(
            array(
                'position' => 2,
            )
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/collections/items/1/move',
            array(),
            array(),
            array(),
            $data
        );

        $this->assertEmptyResponse($this->client->getResponse());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\CollectionController::moveItem
     */
    public function testMoveItemWithNonExistentItem()
    {
        $data = $this->jsonEncode(array());

        $this->client->request(
            'POST',
            '/bm/api/v1/collections/items/9999/move',
            array(),
            array(),
            array(),
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_NOT_FOUND,
            'Could not find item with identifier "9999"'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\CollectionController::moveItem
     */
    public function testMoveItemWithOutOfRangePosition()
    {
        $data = $this->jsonEncode(
            array(
                'position' => 9999,
            )
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/collections/items/1/move',
            array(),
            array(),
            array(),
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_UNPROCESSABLE_ENTITY,
            'Argument "position" has an invalid state. Position is out of range.'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\CollectionController::moveItem
     */
    public function testMoveItemWithInvalidPosition()
    {
        $data = $this->jsonEncode(
            array(
                'position' => '1',
            )
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/collections/items/1/move',
            array(),
            array(),
            array(),
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "position": This value should be of type int.'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\CollectionController::moveItem
     */
    public function testMoveItemWithMissingPosition()
    {
        $data = $this->jsonEncode(array());

        $this->client->request(
            'POST',
            '/bm/api/v1/collections/items/1/move',
            array(),
            array(),
            array(),
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "position": This value should not be blank.'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\CollectionController::deleteItem
     */
    public function testDeleteItem()
    {
        $data = $this->jsonEncode(array());

        $this->client->request(
            'DELETE',
            '/bm/api/v1/collections/items/7',
            array(),
            array(),
            array(),
            $data
        );

        $this->assertEmptyResponse($this->client->getResponse());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\CollectionController::deleteItem
     */
    public function testDeleteItemWithNonExistentItem()
    {
        $data = $this->jsonEncode(array());

        $this->client->request(
            'DELETE',
            '/bm/api/v1/collections/items/9999',
            array(),
            array(),
            array(),
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_NOT_FOUND,
            'Could not find item with identifier "9999"'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\CollectionController::loadQuery
     */
    public function testLoadQuery()
    {
        $this->client->request('GET', '/bm/api/v1/collections/query/2');

        $this->assertResponse(
            $this->client->getResponse(),
            'v1/collections/load_query',
            Response::HTTP_OK
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\CollectionController::loadQuery
     */
    public function testLoadQueryWithNonExistentQuery()
    {
        $this->client->request('GET', '/bm/api/v1/collections/query/9999');

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_NOT_FOUND,
            'Could not find query with identifier "9999"'
        );
    }
}
