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
        $this->client->request('GET', '/bm/api/v1/blocks/31?html=false');

        $this->assertResponse(
            $this->client->getResponse(),
            'v1/blocks/view_block',
            Response::HTTP_OK
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::view
     */
    public function testViewInPublishedState()
    {
        $this->client->request('GET', '/bm/api/v1/blocks/31?published=true&html=false');

        $this->assertResponse(
            $this->client->getResponse(),
            'v1/blocks/view_published_block',
            Response::HTTP_OK
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
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::createBlockCreateStruct
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator\BlockValidator::validateCreateBlock
     */
    public function testCreate()
    {
        $data = $this->jsonEncode(
            array(
                'block_type' => 'list',
                'placeholder' => 'main',
                'position' => 0,
            )
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/blocks/33?html=false',
            array(),
            array(),
            array(),
            $data
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'v1/blocks/create_block',
            Response::HTTP_CREATED
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::create
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::createBlockCreateStruct
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator\BlockValidator::validateCreateBlock
     */
    public function testCreateWithNoPosition()
    {
        $data = $this->jsonEncode(
            array(
                'block_type' => 'list',
                'placeholder' => 'main',
            )
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/blocks/33?html=false',
            array(),
            array(),
            array(),
            $data
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'v1/blocks/create_block_at_end',
            Response::HTTP_CREATED
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::create
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator\BlockValidator::validateCreateBlock
     */
    public function testCreateWithNonContainerTargetBlock()
    {
        $data = $this->jsonEncode(
            array(
                'block_type' => 'list',
                'placeholder' => 'main',
                'position' => 0,
            )
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/blocks/31',
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
    public function testCreateWithNonContainerInsideContainer()
    {
        $data = $this->jsonEncode(
            array(
                'block_type' => 'div_container',
                'placeholder' => 'main',
                'position' => 0,
            )
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/blocks/33',
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
    public function testCreateWithInvalidBlockType()
    {
        $data = $this->jsonEncode(
            array(
                'block_type' => 42,
                'placeholder' => 'main',
                'position' => 0,
            )
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/blocks/33',
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
                'placeholder' => 'main',
                'position' => 0,
            )
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/blocks/33',
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
    public function testCreateWithInvalidPlaceholder()
    {
        $data = $this->jsonEncode(
            array(
                'block_type' => 'title',
                'placeholder' => 42,
                'position' => 0,
            )
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/blocks/33',
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
    public function testCreateWithMissingPlaceholder()
    {
        $data = $this->jsonEncode(
            array(
                'block_type' => 'title',
                'position' => 0,
            )
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/blocks/33',
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
                'placeholder' => 'main',
                'position' => '0',
            )
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/blocks/33',
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
                'placeholder' => 'main',
                'position' => 0,
            )
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/blocks/33',
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
    public function testCreateWithNonExistentPlaceholder()
    {
        $data = $this->jsonEncode(
            array(
                'block_type' => 'title',
                'placeholder' => 'unknown',
                'position' => 0,
            )
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/blocks/33',
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
                'placeholder' => 'main',
                'position' => 9999,
            )
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/blocks/33',
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
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::createInZone
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::createBlockCreateStruct
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator\BlockValidator::validateCreateBlock
     */
    public function testCreateInZone()
    {
        $data = $this->jsonEncode(
            array(
                'block_type' => 'list',
                'layout_id' => 1,
                'zone_identifier' => 'bottom',
                'position' => 0,
            )
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/blocks?html=false',
            array(),
            array(),
            array(),
            $data
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'v1/blocks/create_block_in_zone',
            Response::HTTP_CREATED
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::createInZone
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::createBlockCreateStruct
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator\BlockValidator::validateCreateBlock
     */
    public function testCreateInZoneWithNoPosition()
    {
        $data = $this->jsonEncode(
            array(
                'block_type' => 'list',
                'layout_id' => 1,
                'zone_identifier' => 'right',
            )
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/blocks?html=false',
            array(),
            array(),
            array(),
            $data
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'v1/blocks/create_block_in_zone_at_end',
            Response::HTTP_CREATED
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::createInZone
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator\BlockValidator::validateCreateBlock
     */
    public function testCreateInZoneWithInvalidBlockType()
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
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::createInZone
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator\BlockValidator::validateCreateBlock
     */
    public function testCreateInZoneWithMissingBlockType()
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
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::createInZone
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator\BlockValidator::validateCreateBlock
     */
    public function testCreateInZoneWithInvalidLayoutId()
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
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::createInZone
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator\BlockValidator::validateCreateBlock
     */
    public function testCreateInZoneWithMissingLayoutId()
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
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::createInZone
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator\BlockValidator::validateCreateBlock
     */
    public function testCreateInZoneWithInvalidZoneIdentifier()
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
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::createInZone
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator\BlockValidator::validateCreateBlock
     */
    public function testCreateInZoneWithMissingZoneIdentifier()
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
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::createInZone
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator\BlockValidator::validateCreateBlock
     */
    public function testCreateInZoneWithInvalidPosition()
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
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::createInZone
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator\BlockValidator::validateCreateBlock
     */
    public function testCreateInZoneWithNonExistentBlockType()
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
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::createInZone
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator\BlockValidator::validateCreateBlock
     */
    public function testCreateInZoneWithNonExistentLayout()
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
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::createInZone
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator\BlockValidator::validateCreateBlock
     */
    public function testCreateInZoneWithNonExistentLayoutZone()
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
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::createInZone
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator\BlockValidator::validateCreateBlock
     */
    public function testCreateInZoneWithOutOfRangePosition()
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
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::createInZone
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator\BlockValidator::validateCreateBlock
     */
    public function testCreateInZoneWithNotAllowedBlockDefinition()
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
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::copyToZone
     */
    public function testCopyToZone()
    {
        $data = $this->jsonEncode(
            array(
                'layout_id' => 1,
                'zone_identifier' => 'left',
            )
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/blocks/31/copy/zone?html=false',
            array(),
            array(),
            array(),
            $data
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'v1/blocks/copy_block_to_zone',
            Response::HTTP_CREATED
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::copyToZone
     */
    public function testCopyToZoneWithNonExistentBlock()
    {
        $data = $this->jsonEncode(array());

        $this->client->request(
            'POST',
            '/bm/api/v1/blocks/9999/copy/zone',
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
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::copyToZone
     */
    public function testCopyToZoneWithNonExistentLayout()
    {
        $data = $this->jsonEncode(
            array(
                'layout_id' => 9999,
                'zone_identifier' => 'left',
            )
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/blocks/31/copy/zone',
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
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::copyToZone
     */
    public function testCopyToZoneWithNonExistentZoneIdentifier()
    {
        $data = $this->jsonEncode(
            array(
                'layout_id' => 1,
                'zone_identifier' => 'unknown',
            )
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/blocks/31/copy/zone',
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
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::copyToZone
     */
    public function testCopyToZoneWithNotAllowedBlockDefinition()
    {
        $data = $this->jsonEncode(
            array(
                'layout_id' => 1,
                'zone_identifier' => 'top',
            )
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/blocks/31/copy/zone',
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
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::copyToZone
     */
    public function testCopyToZoneWithInvalidLayoutId()
    {
        $data = $this->jsonEncode(
            array(
                'layout_id' => array(),
                'zone_identifier' => 'left',
            )
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/blocks/31/copy/zone',
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
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::copyToZone
     */
    public function testCopyToZoneWithInvalidZoneIdentifier()
    {
        $data = $this->jsonEncode(
            array(
                'layout_id' => 1,
                'zone_identifier' => 42,
            )
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/blocks/31/copy/zone',
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
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::copyToZone
     */
    public function testCopyToZoneWithMissingLayoutId()
    {
        $data = $this->jsonEncode(
            array(
                'zone_identifier' => 'left',
            )
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/blocks/31/copy/zone',
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
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::copyToZone
     */
    public function testCopyToZoneWithMissingZoneIdentifier()
    {
        $data = $this->jsonEncode(
            array(
                'layout_id' => 1,
            )
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/blocks/31/copy/zone',
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
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::moveToZone
     */
    public function testMoveToZone()
    {
        $data = $this->jsonEncode(
            array(
                'layout_id' => 1,
                'zone_identifier' => 'left',
                'position' => 0,
            )
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/blocks/31/move/zone',
            array(),
            array(),
            array(),
            $data
        );

        $this->assertEmptyResponse($this->client->getResponse());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::moveToZone
     */
    public function testMoveToZoneWithNonExistentBlock()
    {
        $data = $this->jsonEncode(array());

        $this->client->request(
            'POST',
            '/bm/api/v1/blocks/9999/move/zone',
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
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::moveToZone
     */
    public function testMoveToZoneWithNonExistentLayout()
    {
        $data = $this->jsonEncode(
            array(
                'layout_id' => 9999,
                'zone_identifier' => 'left',
                'position' => 1,
            )
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/blocks/31/move/zone',
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
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::moveToZone
     */
    public function testMoveToZoneWithNonExistentZoneIdentifier()
    {
        $data = $this->jsonEncode(
            array(
                'layout_id' => 1,
                'zone_identifier' => 'unknown',
                'position' => 1,
            )
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/blocks/31/move/zone',
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
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::moveToZone
     */
    public function testMoveToZoneWithNotAllowedBlockDefinition()
    {
        $data = $this->jsonEncode(
            array(
                'layout_id' => 1,
                'zone_identifier' => 'top',
                'position' => 0,
            )
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/blocks/31/move/zone',
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
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::moveToZone
     */
    public function testMoveToZoneWithOutOfRangePosition()
    {
        $data = $this->jsonEncode(
            array(
                'layout_id' => 1,
                'zone_identifier' => 'left',
                'position' => 9999,
            )
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/blocks/31/move/zone',
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
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::moveToZone
     */
    public function testMoveToZoneWithInvalidLayoutId()
    {
        $data = $this->jsonEncode(
            array(
                'layout_id' => array(),
                'zone_identifier' => 'left',
                'position' => 1,
            )
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/blocks/31/move/zone',
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
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::moveToZone
     */
    public function testMoveToZoneWithInvalidZoneIdentifier()
    {
        $data = $this->jsonEncode(
            array(
                'layout_id' => 1,
                'zone_identifier' => 42,
                'position' => 1,
            )
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/blocks/31/move/zone',
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
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::moveToZone
     */
    public function testMoveToZoneWithInvalidPosition()
    {
        $data = $this->jsonEncode(
            array(
                'layout_id' => 1,
                'zone_identifier' => 'left',
                'position' => '1',
            )
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/blocks/31/move/zone',
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
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::moveToZone
     */
    public function testMoveToZoneWithMissingLayoutId()
    {
        $data = $this->jsonEncode(
            array(
                'zone_identifier' => 'left',
                'position' => 1,
            )
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/blocks/31/move/zone',
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
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::moveToZone
     */
    public function testMoveToZoneWithMissingZoneIdentifier()
    {
        $data = $this->jsonEncode(
            array(
                'layout_id' => 1,
                'position' => 1,
            )
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/blocks/31/move/zone',
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
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::moveToZone
     */
    public function testMoveToZoneWithMissingPosition()
    {
        $data = $this->jsonEncode(
            array(
                'layout_id' => 1,
                'zone_identifier' => 'left',
            )
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/blocks/31/move',
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
            '/bm/api/v1/blocks/31/restore?html=false',
            array(),
            array(),
            array(),
            $data
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'v1/blocks/restore_block',
            Response::HTTP_OK
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
            '/bm/api/v1/blocks/31',
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
