<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\Controller\API\V1;

use Netgen\Bundle\BlockManagerBundle\Tests\Controller\API\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Response;

class BlockControllerTest extends JsonApiTestCase
{
    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::checkPermissions
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
            Response::HTTP_NOT_FOUND,
            'Could not find block with identifier "9999"'
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
                'placeholder' => 'left',
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
    public function testCreateWithViewType()
    {
        $data = $this->jsonEncode(
            array(
                'block_type' => 'grid',
                'placeholder' => 'left',
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
            'v1/blocks/create_block_with_view_type',
            Response::HTTP_CREATED
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::create
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::createBlockCreateStruct
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator\BlockValidator::validateCreateBlock
     */
    public function testCreateWithItemViewType()
    {
        $data = $this->jsonEncode(
            array(
                'block_type' => 'test_grid',
                'placeholder' => 'left',
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
            'v1/blocks/create_block_with_item_view_type',
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
                'placeholder' => 'left',
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
            Response::HTTP_UNPROCESSABLE_ENTITY,
            'Argument "targetBlock" has an invalid state. Target block is not a container.'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::create
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator\BlockValidator::validateCreateBlock
     */
    public function testCreateWithContainerInsideContainer()
    {
        $data = $this->jsonEncode(
            array(
                'block_type' => 'column',
                'placeholder' => 'left',
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
            Response::HTTP_UNPROCESSABLE_ENTITY,
            'Argument "blockCreateStruct" has an invalid state. Containers cannot be placed inside containers.'
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
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "block_type": This value should be of type string.'
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
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "block_type": This value should not be blank.'
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
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "placeholder": This value should be of type string.'
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
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "placeholder": This value should not be blank.'
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
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "position": This value should be of type int.'
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
            Response::HTTP_UNPROCESSABLE_ENTITY,
            'Argument "block_type" has an invalid state. Block type does not exist.'
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
            Response::HTTP_UNPROCESSABLE_ENTITY,
            'Argument "placeholder" has an invalid state. Target block does not have the specified placeholder.'
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
                'placeholder' => 'left',
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
            Response::HTTP_UNPROCESSABLE_ENTITY,
            'Argument "position" has an invalid state. Position is out of range.'
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
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "block_type": This value should be of type string.'
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
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "block_type": This value should not be blank.'
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
                'layout_id' => array(42),
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
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "layoutId": This value should be of type scalar.'
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
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "layoutId": This value should not be blank.'
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
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "zoneIdentifier": This value should be of type string.'
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
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "zoneIdentifier": This value should not be blank.'
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
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "position": This value should be of type int.'
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
            Response::HTTP_UNPROCESSABLE_ENTITY,
            'Argument "block_type" has an invalid state. Block type does not exist.'
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
            Response::HTTP_NOT_FOUND,
            'Could not find layout with identifier "9999"'
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
            Response::HTTP_NOT_FOUND,
            'Could not find zone with identifier "unknown"'
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
            Response::HTTP_UNPROCESSABLE_ENTITY,
            'Argument "position" has an invalid state. Position is out of range.'
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
            Response::HTTP_UNPROCESSABLE_ENTITY,
            'Argument "zone" has an invalid state. Block is not allowed in specified zone.'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::copy
     */
    public function testCopy()
    {
        $data = $this->jsonEncode(
            array(
                'block_id' => 33,
                'placeholder' => 'left',
            )
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/blocks/31/copy?html=false',
            array(),
            array(),
            array(),
            $data
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'v1/blocks/copy_block',
            Response::HTTP_CREATED
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
            Response::HTTP_NOT_FOUND,
            'Could not find block with identifier "9999"'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::copy
     */
    public function testCopyWithNonExistentTargetBlock()
    {
        $data = $this->jsonEncode(
            array(
                'block_id' => 9999,
                'placeholder' => 'main',
            )
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/blocks/31/copy',
            array(),
            array(),
            array(),
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_NOT_FOUND,
            'Could not find block with identifier "9999"'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::copy
     */
    public function testCopyWithNonExistentPlaceholder()
    {
        $data = $this->jsonEncode(
            array(
                'block_id' => 33,
                'placeholder' => 'unknown',
            )
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/blocks/31/copy',
            array(),
            array(),
            array(),
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_UNPROCESSABLE_ENTITY,
            'Argument "placeholder" has an invalid state. Target block does not have the specified placeholder.'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::copy
     */
    public function testCopyWithNonContainerTargetBlock()
    {
        $data = $this->jsonEncode(
            array(
                'block_id' => 37,
                'placeholder' => 'main',
            )
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/blocks/31/copy',
            array(),
            array(),
            array(),
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_UNPROCESSABLE_ENTITY,
            'Argument "targetBlock" has an invalid state. Target block is not a container.'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::copy
     */
    public function testCopyWithContainerInsideContainer()
    {
        $data = $this->jsonEncode(
            array(
                'block_id' => 38,
                'placeholder' => 'main',
            )
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/blocks/33/copy',
            array(),
            array(),
            array(),
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_UNPROCESSABLE_ENTITY,
            'Argument "block" has an invalid state. Containers cannot be placed inside containers.'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::copy
     */
    public function testCopyWithInvalidBlockId()
    {
        $data = $this->jsonEncode(
            array(
                'block_id' => array(42),
                'placeholder' => 'main',
            )
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/blocks/31/copy',
            array(),
            array(),
            array(),
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "blockId": This value should be of type scalar.'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::copy
     */
    public function testCopyWithInvalidPlaceholder()
    {
        $data = $this->jsonEncode(
            array(
                'block_id' => 33,
                'placeholder' => 42,
            )
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/blocks/31/copy',
            array(),
            array(),
            array(),
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "placeholder": This value should be of type string.'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::copy
     */
    public function testCopyWithMissingBlockId()
    {
        $data = $this->jsonEncode(
            array(
                'placeholder' => 'main',
            )
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/blocks/31/copy',
            array(),
            array(),
            array(),
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "blockId": This value should not be blank.'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::copy
     */
    public function testCopyWithMissingPlaceholder()
    {
        $data = $this->jsonEncode(
            array(
                'block_id' => 33,
            )
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/blocks/31/copy',
            array(),
            array(),
            array(),
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "placeholder": This value should not be blank.'
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
            Response::HTTP_NOT_FOUND,
            'Could not find block with identifier "9999"'
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
            Response::HTTP_NOT_FOUND,
            'Could not find layout with identifier "9999"'
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
            Response::HTTP_NOT_FOUND,
            'Could not find zone with identifier "unknown"'
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
            Response::HTTP_UNPROCESSABLE_ENTITY,
            'Argument "zoneIdentifier" has an invalid state. Block is not allowed in specified zone.'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::copyToZone
     */
    public function testCopyToZoneWithInvalidLayoutId()
    {
        $data = $this->jsonEncode(
            array(
                'layout_id' => array(42),
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
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "layoutId": This value should be of type scalar.'
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
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "zoneIdentifier": This value should be of type string.'
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
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "layoutId": This value should not be blank.'
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
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "zoneIdentifier": This value should not be blank.'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::move
     */
    public function testMove()
    {
        $data = $this->jsonEncode(
            array(
                'block_id' => 33,
                'placeholder' => 'left',
                'position' => 0,
            )
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/blocks/32/move',
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
    public function testMoveToDifferentPlaceholder()
    {
        $data = $this->jsonEncode(
            array(
                'block_id' => 33,
                'placeholder' => 'right',
                'position' => 0,
            )
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/blocks/37/move',
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
    public function testMoveToDifferentBlock()
    {
        $data = $this->jsonEncode(
            array(
                'block_id' => 38,
                'placeholder' => 'main',
                'position' => 0,
            )
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/blocks/37/move',
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
            'POST',
            '/bm/api/v1/blocks/9999/move/zone',
            array(),
            array(),
            array(),
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_NOT_FOUND,
            'Could not find block with identifier "9999"'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::move
     */
    public function testMoveWithNonExistentTargetBlock()
    {
        $data = $this->jsonEncode(
            array(
                'block_id' => 9999,
                'placeholder' => 'main',
                'position' => 1,
            )
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/blocks/32/move',
            array(),
            array(),
            array(),
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_NOT_FOUND,
            'Could not find block with identifier "9999"'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::move
     */
    public function testMoveWithNonExistentPlaceholder()
    {
        $data = $this->jsonEncode(
            array(
                'block_id' => 33,
                'placeholder' => 'unknown',
                'position' => 1,
            )
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/blocks/32/move',
            array(),
            array(),
            array(),
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_UNPROCESSABLE_ENTITY,
            'Argument "placeholder" has an invalid state. Target block does not have the specified placeholder.'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::move
     */
    public function testMoveWithNonContainerTargetBlock()
    {
        $data = $this->jsonEncode(
            array(
                'block_id' => 32,
                'placeholder' => 'main',
                'position' => 0,
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
            Response::HTTP_UNPROCESSABLE_ENTITY,
            'Argument "targetBlock" has an invalid state. Target block is not a container.'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::move
     */
    public function testMoveWithOutOfRangePosition()
    {
        $data = $this->jsonEncode(
            array(
                'block_id' => 33,
                'placeholder' => 'left',
                'position' => 9999,
            )
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/blocks/32/move',
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
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::move
     */
    public function testMoveWithContainerInsideContainer()
    {
        $data = $this->jsonEncode(
            array(
                'block_id' => 38,
                'placeholder' => 'main',
                'position' => 0,
            )
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/blocks/33/move',
            array(),
            array(),
            array(),
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_UNPROCESSABLE_ENTITY,
            'Argument "block" has an invalid state. Containers cannot be placed inside containers.'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::move
     */
    public function testMoveWithInvalidBlockId()
    {
        $data = $this->jsonEncode(
            array(
                'block_id' => array(42),
                'placeholder' => 'main',
                'position' => 1,
            )
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/blocks/32/move',
            array(),
            array(),
            array(),
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "blockId": This value should be of type scalar.'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::move
     */
    public function testMoveWithInvalidPlaceholder()
    {
        $data = $this->jsonEncode(
            array(
                'block_id' => 33,
                'placeholder' => 42,
                'position' => 1,
            )
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/blocks/32/move',
            array(),
            array(),
            array(),
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "placeholder": This value should be of type string.'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::move
     */
    public function testMoveWithInvalidPosition()
    {
        $data = $this->jsonEncode(
            array(
                'block_id' => 33,
                'placeholder' => 'main',
                'position' => '1',
            )
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/blocks/32/move',
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
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::move
     */
    public function testMoveWithMissingBlockId()
    {
        $data = $this->jsonEncode(
            array(
                'placeholder' => 'main',
                'position' => 1,
            )
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/blocks/32/move',
            array(),
            array(),
            array(),
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "blockId": This value should not be blank.'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::move
     */
    public function testMoveWithMissingPlaceholder()
    {
        $data = $this->jsonEncode(
            array(
                'block_id' => 33,
                'position' => 1,
            )
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/blocks/32/move',
            array(),
            array(),
            array(),
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "placeholder": This value should not be blank.'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::move
     */
    public function testMoveWithMissingPosition()
    {
        $data = $this->jsonEncode(
            array(
                'block_id' => 33,
                'placeholder' => 'main',
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
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "position": This value should not be blank.'
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
            Response::HTTP_NOT_FOUND,
            'Could not find block with identifier "9999"'
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
            Response::HTTP_NOT_FOUND,
            'Could not find layout with identifier "9999"'
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
            Response::HTTP_NOT_FOUND,
            'Could not find zone with identifier "unknown"'
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
            Response::HTTP_UNPROCESSABLE_ENTITY,
            'Argument "zoneIdentifier" has an invalid state. Block is not allowed in specified zone.'
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
            Response::HTTP_UNPROCESSABLE_ENTITY,
            'Argument "position" has an invalid state. Position is out of range.'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::moveToZone
     */
    public function testMoveToZoneWithInvalidLayoutId()
    {
        $data = $this->jsonEncode(
            array(
                'layout_id' => array(42),
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
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "layoutId": This value should be of type scalar.'
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
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "zoneIdentifier": This value should be of type string.'
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
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "position": This value should be of type int.'
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
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "layoutId": This value should not be blank.'
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
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "zoneIdentifier": This value should not be blank.'
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
            '/bm/api/v1/blocks/31/move/zone',
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
            Response::HTTP_NOT_FOUND,
            'Could not find block with identifier "9999"'
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
            Response::HTTP_NOT_FOUND,
            'Could not find block with identifier "9999"'
        );
    }
}
