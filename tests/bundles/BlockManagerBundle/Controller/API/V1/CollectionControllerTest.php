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
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\CollectionController::loadCollectionResult
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator\Validator::validateOffsetAndLimit
     */
    public function testLoadCollectionResult()
    {
        $this->client->request('GET', '/bm/api/v1/collections/4/result');

        $this->assertResponse(
            $this->client->getResponse(),
            'v1/collections/load_collection_result',
            Response::HTTP_OK
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\CollectionController::loadCollectionResult
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator\Validator::validateOffsetAndLimit
     */
    public function testLoadCollectionResultWithOffsetAndLimit()
    {
        $this->client->request('GET', '/bm/api/v1/collections/4/result?offset=2&limit=2');

        $this->assertResponse(
            $this->client->getResponse(),
            'v1/collections/load_collection_result_limited',
            Response::HTTP_OK
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\CollectionController::loadCollectionResult
     */
    public function testLoadCollectionResultWithNonExistentCollection()
    {
        $this->client->request('GET', '/bm/api/v1/collections/9999/result');

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
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\CollectionController::addItems
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator\CollectionValidator::validateAddItems
     */
    public function testAddItems()
    {
        $data = $this->jsonEncode(
            array(
                'items' => array(
                    array(
                        'type' => Item::TYPE_MANUAL,
                        'value_id' => 73,
                        'value_type' => 'ezlocation',
                        'position' => 3,
                    ),
                    array(
                        'type' => Item::TYPE_MANUAL,
                        'value_id' => 74,
                        'value_type' => 'ezlocation',
                    ),
                ),
            )
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/collections/1/items',
            array(),
            array(),
            array(),
            $data
        );

        $this->assertEmptyResponse($this->client->getResponse());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\CollectionController::addItems
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator\CollectionValidator::validateAddItems
     */
    public function testAddItemsWithNonExistentCollection()
    {
        $data = $this->jsonEncode(
            array(
                'items' => array(
                    array(
                        'type' => Item::TYPE_MANUAL,
                        'value_id' => 73,
                        'value_type' => 'ezlocation',
                        'position' => 3,
                    ),
                    array(
                        'type' => Item::TYPE_MANUAL,
                        'value_id' => 74,
                        'value_type' => 'ezlocation',
                    ),
                ),
            )
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/collections/9999/items',
            array(),
            array(),
            array(),
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_NOT_FOUND,
            'Could not find collection with identifier "9999"'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\CollectionController::addItems
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator\CollectionValidator::validateAddItems
     */
    public function testAddItemsWithEmptyItems()
    {
        $data = $this->jsonEncode(
            array(
                'items' => array(),
            )
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/collections/1/items',
            array(),
            array(),
            array(),
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "items": This value should not be blank.'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\CollectionController::addItems
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator\CollectionValidator::validateAddItems
     */
    public function testAddItemsWithInvalidItems()
    {
        $data = $this->jsonEncode(
            array(
                'items' => 42,
            )
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/collections/1/items',
            array(),
            array(),
            array(),
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "items": Expected argument of type "array or Traversable", "integer" given'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\CollectionController::addItems
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator\CollectionValidator::validateAddItems
     */
    public function testAddItemsWithMissingItems()
    {
        $data = $this->jsonEncode(array());

        $this->client->request(
            'POST',
            '/bm/api/v1/collections/1/items',
            array(),
            array(),
            array(),
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "items": This value should not be blank.'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\CollectionController::addItems
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator\CollectionValidator::validateAddItems
     */
    public function testAddItemsWithInvalidItemType()
    {
        $data = $this->jsonEncode(
            array(
                'items' => array(
                    array(
                        'type' => 'type',
                        'value_id' => 73,
                        'value_type' => 'ezlocation',
                        'position' => 3,
                    ),
                ),
            )
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/collections/1/items',
            array(),
            array(),
            array(),
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "[0][type]": This value should be of type int.'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\CollectionController::addItems
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator\CollectionValidator::validateAddItems
     */
    public function testAddItemsWithMissingItemType()
    {
        $data = $this->jsonEncode(
            array(
                'items' => array(
                    array(
                        'value_id' => 73,
                        'value_type' => 'ezlocation',
                        'position' => 3,
                    ),
                ),
            )
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/collections/1/items',
            array(),
            array(),
            array(),
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "[0][type]": This field is missing.'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\CollectionController::addItems
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator\CollectionValidator::validateAddItems
     */
    public function testAddItemsWithInvalidValueId()
    {
        $data = $this->jsonEncode(
            array(
                'items' => array(
                    array(
                        'type' => Item::TYPE_MANUAL,
                        'value_id' => array(42),
                        'value_type' => 'ezlocation',
                        'position' => 3,
                    ),
                ),
            )
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/collections/1/items',
            array(),
            array(),
            array(),
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "[0][value_id]": This value should be of type scalar.'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\CollectionController::addItems
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator\CollectionValidator::validateAddItems
     */
    public function testAddItemsWithMissingValueId()
    {
        $data = $this->jsonEncode(
            array(
                'items' => array(
                    array(
                        'type' => Item::TYPE_MANUAL,
                        'value_type' => 'ezlocation',
                        'position' => 3,
                    ),
                ),
            )
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/collections/1/items',
            array(),
            array(),
            array(),
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "[0][value_id]": This field is missing.'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\CollectionController::addItems
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator\CollectionValidator::validateAddItems
     */
    public function testAddItemsWithInvalidValueType()
    {
        $data = $this->jsonEncode(
            array(
                'items' => array(
                    array(
                        'type' => Item::TYPE_MANUAL,
                        'value_id' => 73,
                        'value_type' => 42,
                        'position' => 3,
                    ),
                ),
            )
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/collections/1/items',
            array(),
            array(),
            array(),
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "[0][value_type]": This value should be of type string.'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\CollectionController::addItems
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator\CollectionValidator::validateAddItems
     */
    public function testAddItemsWithMissingValueType()
    {
        $data = $this->jsonEncode(
            array(
                'items' => array(
                    array(
                        'type' => Item::TYPE_MANUAL,
                        'value_id' => 73,
                        'position' => 3,
                    ),
                ),
            )
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/collections/1/items',
            array(),
            array(),
            array(),
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "[0][value_type]": This field is missing.'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\CollectionController::addItems
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator\CollectionValidator::validateAddItems
     */
    public function testAddItemsWithInvalidPosition()
    {
        $data = $this->jsonEncode(
            array(
                'items' => array(
                    array(
                        'type' => Item::TYPE_MANUAL,
                        'value_id' => 73,
                        'value_type' => 'ezlocation',
                        'position' => '3',
                    ),
                ),
            )
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/collections/1/items',
            array(),
            array(),
            array(),
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "[0][position]": This value should be of type int.'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\CollectionController::addItems
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator\CollectionValidator::validateAddItems
     */
    public function testAddItemsWithMissingPosition()
    {
        $data = $this->jsonEncode(
            array(
                'items' => array(
                    array(
                        'type' => Item::TYPE_MANUAL,
                        'value_id' => 73,
                        'value_type' => 'ezlocation',
                    ),
                ),
            )
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/collections/3/items',
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
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\CollectionController::addItems
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator\CollectionValidator::validateAddItems
     */
    public function testAddItemsWithOutOfRangePosition()
    {
        $data = $this->jsonEncode(
            array(
                'items' => array(
                    array(
                        'type' => Item::TYPE_MANUAL,
                        'value_id' => 73,
                        'value_type' => 'ezlocation',
                        'position' => 9999,
                    ),
                ),
            )
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/collections/1/items',
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
