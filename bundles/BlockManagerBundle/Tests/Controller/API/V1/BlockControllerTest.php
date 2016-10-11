<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\Controller\API\V1;

use Netgen\Bundle\BlockManagerBundle\Tests\Controller\API\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Response;

class BlockControllerTest extends JsonApiTestCase
{
    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::view
     */
    public function testView()
    {
        $this->client->request('GET', '/bm/api/v1/blocks/1');

        $this->assertResponse(
            $this->client->getResponse(),
            'v1/blocks/view_block',
            Response::HTTP_OK,
            array('html')
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::view
     */
    public function testViewWithNonExistentBlock()
    {
        $this->client->request('GET', '/bm/api/v1/blocks/9999');

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_NOT_FOUND
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::create
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator\BlockValidator::validateCreateBlock
     */
    public function testCreate()
    {
        $data = $this->jsonEncode(
            array(
                'block_type' => 'title',
                'layout_id' => 1,
                'zone_identifier' => 'bottom',
                'position' => 0,
            )
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/blocks',
            array(),
            array(),
            array(),
            $data
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'v1/blocks/create_block',
            Response::HTTP_CREATED,
            array('html')
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::create
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator\BlockValidator::validateCreateBlock
     */
    public function testCreateWithNoPosition()
    {
        $data = $this->jsonEncode(
            array(
                'block_type' => 'title',
                'layout_id' => 1,
                'zone_identifier' => 'right',
            )
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/blocks',
            array(),
            array(),
            array(),
            $data
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'v1/blocks/create_block_at_end',
            Response::HTTP_CREATED,
            array('html')
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::create
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator\BlockValidator::validateCreateBlock
     */
    public function testCreateWithInvalidBlockType()
    {
        $data = $this->jsonEncode(
            array(
                'block_type' => 42,
                'layout_id' => 1,
                'zone_identifier' => 'bottom',
                'position' => 0,
            )
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/blocks',
            array(),
            array(),
            array(),
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_BAD_REQUEST
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::create
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator\BlockValidator::validateCreateBlock
     */
    public function testCreateWithMissingBlockType()
    {
        $data = $this->jsonEncode(
            array(
                'layout_id' => 1,
                'zone_identifier' => 'bottom',
                'position' => 0,
            )
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/blocks',
            array(),
            array(),
            array(),
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_BAD_REQUEST
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::create
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator\BlockValidator::validateCreateBlock
     */
    public function testCreateWithInvalidLayoutId()
    {
        $data = $this->jsonEncode(
            array(
                'block_type' => 'title',
                'layout_id' => array(),
                'zone_identifier' => 'bottom',
                'position' => 0,
            )
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/blocks',
            array(),
            array(),
            array(),
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_BAD_REQUEST
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::create
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator\BlockValidator::validateCreateBlock
     */
    public function testCreateWithMissingLayoutId()
    {
        $data = $this->jsonEncode(
            array(
                'block_type' => 'title',
                'zone_identifier' => 'bottom',
                'position' => 0,
            )
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/blocks',
            array(),
            array(),
            array(),
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_BAD_REQUEST
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::create
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator\BlockValidator::validateCreateBlock
     */
    public function testCreateWithInvalidZoneIdentifier()
    {
        $data = $this->jsonEncode(
            array(
                'block_type' => 'title',
                'layout_id' => 1,
                'zone_identifier' => 42,
                'position' => 0,
            )
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/blocks',
            array(),
            array(),
            array(),
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_BAD_REQUEST
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::create
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator\BlockValidator::validateCreateBlock
     */
    public function testCreateWithMissingZoneIdentifier()
    {
        $data = $this->jsonEncode(
            array(
                'block_type' => 'title',
                'layout_id' => 1,
                'position' => 0,
            )
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/blocks',
            array(),
            array(),
            array(),
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_BAD_REQUEST
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::create
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator\BlockValidator::validateCreateBlock
     */
    public function testCreateWithInvalidPosition()
    {
        $data = $this->jsonEncode(
            array(
                'block_type' => 'title',
                'layout_id' => 1,
                'zone_identifier' => 'bottom',
                'position' => '0',
            )
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/blocks',
            array(),
            array(),
            array(),
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_BAD_REQUEST
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::create
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator\BlockValidator::validateCreateBlock
     */
    public function testCreateWithNonExistentBlockType()
    {
        $data = $this->jsonEncode(
            array(
                'block_type' => 'unknown',
                'layout_id' => 1,
                'zone_identifier' => 'bottom',
                'position' => 0,
            )
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/blocks',
            array(),
            array(),
            array(),
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_UNPROCESSABLE_ENTITY
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::create
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator\BlockValidator::validateCreateBlock
     */
    public function testCreateWithNonExistentLayout()
    {
        $data = $this->jsonEncode(
            array(
                'block_type' => 'title',
                'layout_id' => 9999,
                'zone_identifier' => 'bottom',
                'position' => 0,
            )
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/blocks',
            array(),
            array(),
            array(),
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_UNPROCESSABLE_ENTITY
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::create
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator\BlockValidator::validateCreateBlock
     */
    public function testCreateWithNonExistentLayoutZone()
    {
        $data = $this->jsonEncode(
            array(
                'block_type' => 'title',
                'layout_id' => 1,
                'zone_identifier' => 'unknown',
                'position' => 0,
            )
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/blocks',
            array(),
            array(),
            array(),
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_UNPROCESSABLE_ENTITY
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::create
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator\BlockValidator::validateCreateBlock
     */
    public function testCreateWithOutOfRangePosition()
    {
        $data = $this->jsonEncode(
            array(
                'block_type' => 'title',
                'layout_id' => 1,
                'zone_identifier' => 'bottom',
                'position' => 9999,
            )
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/blocks',
            array(),
            array(),
            array(),
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_UNPROCESSABLE_ENTITY
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::create
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator\BlockValidator::validateCreateBlock
     */
    public function testCreateWithNotAllowedBlockDefinition()
    {
        $data = $this->jsonEncode(
            array(
                'block_type' => 'list',
                'layout_id' => 1,
                'zone_identifier' => 'top',
                'position' => 0,
            )
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/blocks',
            array(),
            array(),
            array(),
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_UNPROCESSABLE_ENTITY
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::copy
     */
    public function testCopy()
    {
        $data = $this->jsonEncode(
            array(
                'zone_identifier' => 'left',
            )
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/blocks/1/copy',
            array(),
            array(),
            array(),
            $data
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'v1/blocks/copy_block',
            Response::HTTP_CREATED,
            array('html')
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::copy
     */
    public function testCopyWithNoZoneIdentifier()
    {
        $data = $this->jsonEncode(array());

        $this->client->request(
            'POST',
            '/bm/api/v1/blocks/1/copy',
            array(),
            array(),
            array(),
            $data
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'v1/blocks/copy_block_in_same_zone',
            Response::HTTP_CREATED,
            array('html')
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::copy
     */
    public function testCopyWithNonExistentBlock()
    {
        $data = $this->jsonEncode(array());

        $this->client->request(
            'POST',
            '/bm/api/v1/blocks/9999/copy',
            array(),
            array(),
            array(),
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_NOT_FOUND
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::copy
     */
    public function testCopyWithNonExistentZoneIdentifier()
    {
        $data = $this->jsonEncode(
            array(
                'zone_identifier' => 'unknown',
            )
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/blocks/1/copy',
            array(),
            array(),
            array(),
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_UNPROCESSABLE_ENTITY
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::copy
     */
    public function testCopyWithNotAllowedBlockDefinition()
    {
        $data = $this->jsonEncode(
            array(
                'zone_identifier' => 'top',
            )
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/blocks/1/copy',
            array(),
            array(),
            array(),
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_UNPROCESSABLE_ENTITY
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::copy
     */
    public function testCopyWithInvalidZoneIdentifier()
    {
        $data = $this->jsonEncode(
            array(
                'zone_identifier' => 42,
            )
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/blocks/1/copy',
            array(),
            array(),
            array(),
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_BAD_REQUEST
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::move
     */
    public function testMove()
    {
        $data = $this->jsonEncode(
            array(
                'zone_identifier' => 'left',
                'position' => 0,
            )
        );

        $this->client->request(
            'PATCH',
            '/bm/api/v1/blocks/1/move',
            array(),
            array(),
            array(),
            $data
        );

        $this->assertEmptyResponse($this->client->getResponse());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::move
     */
    public function testMoveWithNoZoneIdentifier()
    {
        $data = $this->jsonEncode(
            array(
                'position' => 1,
            )
        );

        $this->client->request(
            'PATCH',
            '/bm/api/v1/blocks/1/move',
            array(),
            array(),
            array(),
            $data
        );

        $this->assertEmptyResponse($this->client->getResponse());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::move
     */
    public function testMoveWithNonExistentBlock()
    {
        $data = $this->jsonEncode(array());

        $this->client->request(
            'PATCH',
            '/bm/api/v1/blocks/9999/move',
            array(),
            array(),
            array(),
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_NOT_FOUND
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::move
     */
    public function testMoveWithNonExistentZoneIdentifier()
    {
        $data = $this->jsonEncode(
            array(
                'zone_identifier' => 'unknown',
                'position' => 1,
            )
        );

        $this->client->request(
            'PATCH',
            '/bm/api/v1/blocks/1/move',
            array(),
            array(),
            array(),
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_UNPROCESSABLE_ENTITY
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::move
     */
    public function testMoveWithNotAllowedBlockDefinition()
    {
        $data = $this->jsonEncode(
            array(
                'zone_identifier' => 'top',
                'position' => 0,
            )
        );

        $this->client->request(
            'PATCH',
            '/bm/api/v1/blocks/1/move',
            array(),
            array(),
            array(),
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_UNPROCESSABLE_ENTITY
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::move
     */
    public function testMoveWithInvalidZoneIdentifier()
    {
        $data = $this->jsonEncode(
            array(
                'zone_identifier' => 42,
                'position' => 1,
            )
        );

        $this->client->request(
            'PATCH',
            '/bm/api/v1/blocks/1/move',
            array(),
            array(),
            array(),
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_BAD_REQUEST
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::move
     */
    public function testMoveWithInvalidPosition()
    {
        $data = $this->jsonEncode(
            array(
                'zone_identifier' => 'left',
                'position' => '1',
            )
        );

        $this->client->request(
            'PATCH',
            '/bm/api/v1/blocks/1/move',
            array(),
            array(),
            array(),
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_BAD_REQUEST
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::move
     */
    public function testMoveWithMissingPosition()
    {
        $data = $this->jsonEncode(
            array(
                'zone_identifier' => 'left',
            )
        );

        $this->client->request(
            'PATCH',
            '/bm/api/v1/blocks/1/move',
            array(),
            array(),
            array(),
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_BAD_REQUEST
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::restore
     */
    public function testRestore()
    {
        $data = $this->jsonEncode(array());

        $this->client->request(
            'POST',
            '/bm/api/v1/blocks/1/restore',
            array(),
            array(),
            array(),
            $data
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'v1/blocks/restore_block',
            Response::HTTP_OK,
            array('html')
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::restore
     */
    public function testRestoreWithNonExistentBlock()
    {
        $data = $this->jsonEncode(array());

        $this->client->request(
            'POST',
            '/bm/api/v1/blocks/9999/restore',
            array(),
            array(),
            array(),
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_NOT_FOUND
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::delete
     */
    public function testDelete()
    {
        $data = $this->jsonEncode(array());

        $this->client->request(
            'DELETE',
            '/bm/api/v1/blocks/1',
            array(),
            array(),
            array(),
            $data
        );

        $this->assertEmptyResponse($this->client->getResponse());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::delete
     */
    public function testDeleteWithNonExistentBlock()
    {
        $data = $this->jsonEncode(array());

        $this->client->request(
            'DELETE',
            '/bm/api/v1/blocks/9999',
            array(),
            array(),
            array(),
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_NOT_FOUND
        );
    }
}
