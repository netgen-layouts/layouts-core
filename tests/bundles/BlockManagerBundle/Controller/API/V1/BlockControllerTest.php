<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\Controller\API\V1;

use Netgen\Bundle\BlockManagerBundle\Tests\Controller\API\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Response;

final class BlockControllerTest extends JsonApiTestCase
{
    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::checkPermissions
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::view
     */
    public function testView()
    {
        $this->client->request('GET', '/bm/api/v1/en/blocks/31?html=false');

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
        $this->client->request('GET', '/bm/api/v1/en/blocks/31?published=true&html=false');

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
        $this->client->request('GET', '/bm/api/v1/en/blocks/9999');

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
            [
                'block_type' => 'list',
                'parent_placeholder' => 'left',
                'parent_position' => 0,
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/en/blocks/33?html=false',
            [],
            [],
            [],
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
            [
                'block_type' => 'grid',
                'parent_placeholder' => 'left',
                'parent_position' => 0,
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/en/blocks/33?html=false',
            [],
            [],
            [],
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
            [
                'block_type' => 'test_grid',
                'parent_placeholder' => 'left',
                'parent_position' => 0,
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/en/blocks/33?html=false',
            [],
            [],
            [],
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
            [
                'block_type' => 'list',
                'parent_placeholder' => 'left',
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/en/blocks/33?html=false',
            [],
            [],
            [],
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
            [
                'block_type' => 'list',
                'parent_placeholder' => 'main',
                'parent_position' => 0,
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/en/blocks/31',
            [],
            [],
            [],
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
            [
                'block_type' => 'column',
                'parent_placeholder' => 'left',
                'parent_position' => 0,
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/en/blocks/33',
            [],
            [],
            [],
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
            [
                'block_type' => 42,
                'parent_placeholder' => 'main',
                'parent_position' => 0,
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/en/blocks/33',
            [],
            [],
            [],
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
            [
                'parent_placeholder' => 'main',
                'parent_position' => 0,
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/en/blocks/33',
            [],
            [],
            [],
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
            [
                'block_type' => 'title',
                'parent_placeholder' => 42,
                'parent_position' => 0,
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/en/blocks/33',
            [],
            [],
            [],
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
            [
                'block_type' => 'title',
                'parent_position' => 0,
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/en/blocks/33',
            [],
            [],
            [],
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
            [
                'block_type' => 'title',
                'parent_placeholder' => 'main',
                'parent_position' => '0',
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/en/blocks/33',
            [],
            [],
            [],
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
            [
                'block_type' => 'unknown',
                'parent_placeholder' => 'main',
                'parent_position' => 0,
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/en/blocks/33',
            [],
            [],
            [],
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
            [
                'block_type' => 'title',
                'parent_placeholder' => 'unknown',
                'parent_position' => 0,
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/en/blocks/33',
            [],
            [],
            [],
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
            [
                'block_type' => 'list',
                'parent_placeholder' => 'left',
                'parent_position' => 9999,
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/en/blocks/33',
            [],
            [],
            [],
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_UNPROCESSABLE_ENTITY,
            'Argument "position" has an invalid state. Position is out of range.'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::createBlockCreateStruct
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::createInZone
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator\BlockValidator::validateCreateBlock
     */
    public function testCreateInZone()
    {
        $data = $this->jsonEncode(
            [
                'block_type' => 'list',
                'layout_id' => 1,
                'zone_identifier' => 'bottom',
                'parent_position' => 0,
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/en/blocks?html=false',
            [],
            [],
            [],
            $data
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'v1/blocks/create_block_in_zone',
            Response::HTTP_CREATED
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::createBlockCreateStruct
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::createInZone
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator\BlockValidator::validateCreateBlock
     */
    public function testCreateInZoneWithNoPosition()
    {
        $data = $this->jsonEncode(
            [
                'block_type' => 'list',
                'layout_id' => 1,
                'zone_identifier' => 'right',
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/en/blocks?html=false',
            [],
            [],
            [],
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
            [
                'block_type' => 42,
                'layout_id' => 1,
                'zone_identifier' => 'bottom',
                'parent_position' => 0,
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/en/blocks',
            [],
            [],
            [],
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
            [
                'layout_id' => 1,
                'zone_identifier' => 'bottom',
                'parent_position' => 0,
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/en/blocks',
            [],
            [],
            [],
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
            [
                'block_type' => 'title',
                'layout_id' => [42],
                'zone_identifier' => 'bottom',
                'parent_position' => 0,
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/en/blocks',
            [],
            [],
            [],
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
            [
                'block_type' => 'title',
                'zone_identifier' => 'bottom',
                'parent_position' => 0,
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/en/blocks',
            [],
            [],
            [],
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
            [
                'block_type' => 'title',
                'layout_id' => 1,
                'zone_identifier' => 42,
                'parent_position' => 0,
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/en/blocks',
            [],
            [],
            [],
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "identifier": This value should be of type string.'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::createInZone
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator\BlockValidator::validateCreateBlock
     */
    public function testCreateInZoneWithMissingZoneIdentifier()
    {
        $data = $this->jsonEncode(
            [
                'block_type' => 'title',
                'layout_id' => 1,
                'parent_position' => 0,
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/en/blocks',
            [],
            [],
            [],
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "identifier": This value should not be blank.'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::createInZone
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator\BlockValidator::validateCreateBlock
     */
    public function testCreateInZoneWithInvalidPosition()
    {
        $data = $this->jsonEncode(
            [
                'block_type' => 'title',
                'layout_id' => 1,
                'zone_identifier' => 'bottom',
                'parent_position' => '0',
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/en/blocks',
            [],
            [],
            [],
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
            [
                'block_type' => 'unknown',
                'layout_id' => 1,
                'zone_identifier' => 'bottom',
                'parent_position' => 0,
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/en/blocks',
            [],
            [],
            [],
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
            [
                'block_type' => 'title',
                'layout_id' => 9999,
                'zone_identifier' => 'bottom',
                'parent_position' => 0,
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/en/blocks',
            [],
            [],
            [],
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_NOT_FOUND,
            'Could not find zone with identifier "bottom"'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::createInZone
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator\BlockValidator::validateCreateBlock
     */
    public function testCreateInZoneWithNonExistentLayoutZone()
    {
        $data = $this->jsonEncode(
            [
                'block_type' => 'title',
                'layout_id' => 1,
                'zone_identifier' => 'unknown',
                'parent_position' => 0,
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/en/blocks',
            [],
            [],
            [],
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
            [
                'block_type' => 'title',
                'layout_id' => 1,
                'zone_identifier' => 'bottom',
                'parent_position' => 9999,
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/en/blocks',
            [],
            [],
            [],
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
            [
                'block_type' => 'list',
                'layout_id' => 1,
                'zone_identifier' => 'top',
                'parent_position' => 0,
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/en/blocks',
            [],
            [],
            [],
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
            [
                'parent_block_id' => 33,
                'parent_placeholder' => 'left',
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/en/blocks/34/copy?html=false',
            [],
            [],
            [],
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
        $data = $this->jsonEncode([]);

        $this->client->request(
            'POST',
            '/bm/api/v1/en/blocks/9999/copy',
            [],
            [],
            [],
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
            [
                'parent_block_id' => 9999,
                'parent_placeholder' => 'main',
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/en/blocks/34/copy',
            [],
            [],
            [],
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
            [
                'parent_block_id' => 33,
                'parent_placeholder' => 'unknown',
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/en/blocks/34/copy',
            [],
            [],
            [],
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
            [
                'parent_block_id' => 37,
                'parent_placeholder' => 'main',
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/en/blocks/34/copy',
            [],
            [],
            [],
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
            [
                'parent_block_id' => 38,
                'parent_placeholder' => 'main',
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/en/blocks/33/copy',
            [],
            [],
            [],
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
            [
                'parent_block_id' => [42],
                'parent_placeholder' => 'main',
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/en/blocks/34/copy',
            [],
            [],
            [],
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
            [
                'parent_block_id' => 33,
                'parent_placeholder' => 42,
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/en/blocks/34/copy',
            [],
            [],
            [],
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
            [
                'parent_placeholder' => 'main',
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/en/blocks/34/copy',
            [],
            [],
            [],
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
            [
                'parent_block_id' => 33,
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/en/blocks/34/copy',
            [],
            [],
            [],
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
            [
                'layout_id' => 1,
                'zone_identifier' => 'left',
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/en/blocks/31/copy/zone?html=false',
            [],
            [],
            [],
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
        $data = $this->jsonEncode([]);

        $this->client->request(
            'POST',
            '/bm/api/v1/en/blocks/9999/copy/zone',
            [],
            [],
            [],
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
            [
                'layout_id' => 9999,
                'zone_identifier' => 'left',
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/en/blocks/31/copy/zone',
            [],
            [],
            [],
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_NOT_FOUND,
            'Could not find zone with identifier "left"'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::copyToZone
     */
    public function testCopyToZoneWithNonExistentZone()
    {
        $data = $this->jsonEncode(
            [
                'layout_id' => 1,
                'zone_identifier' => 'unknown',
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/en/blocks/31/copy/zone',
            [],
            [],
            [],
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
            [
                'layout_id' => 1,
                'zone_identifier' => 'top',
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/en/blocks/31/copy/zone',
            [],
            [],
            [],
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_UNPROCESSABLE_ENTITY,
            'Argument "zone" has an invalid state. Block is not allowed in specified zone.'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::copyToZone
     */
    public function testCopyToZoneWithInvalidLayoutId()
    {
        $data = $this->jsonEncode(
            [
                'layout_id' => [42],
                'zone_identifier' => 'left',
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/en/blocks/31/copy/zone',
            [],
            [],
            [],
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
            [
                'layout_id' => 1,
                'zone_identifier' => 42,
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/en/blocks/31/copy/zone',
            [],
            [],
            [],
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "identifier": This value should be of type string.'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::copyToZone
     */
    public function testCopyToZoneWithMissingLayoutId()
    {
        $data = $this->jsonEncode(
            [
                'zone_identifier' => 'left',
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/en/blocks/31/copy/zone',
            [],
            [],
            [],
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
            [
                'layout_id' => 1,
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/en/blocks/31/copy/zone',
            [],
            [],
            [],
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "identifier": This value should not be blank.'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::move
     */
    public function testMove()
    {
        $data = $this->jsonEncode(
            [
                'parent_block_id' => 33,
                'parent_placeholder' => 'left',
                'parent_position' => 0,
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/en/blocks/34/move',
            [],
            [],
            [],
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
            [
                'parent_block_id' => 33,
                'parent_placeholder' => 'right',
                'parent_position' => 0,
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/en/blocks/37/move',
            [],
            [],
            [],
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
            [
                'parent_block_id' => 38,
                'parent_placeholder' => 'main',
                'parent_position' => 0,
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/en/blocks/37/move',
            [],
            [],
            [],
            $data
        );

        $this->assertEmptyResponse($this->client->getResponse());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::move
     */
    public function testMoveWithNonExistentBlock()
    {
        $data = $this->jsonEncode([]);

        $this->client->request(
            'POST',
            '/bm/api/v1/en/blocks/9999/move/zone',
            [],
            [],
            [],
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
            [
                'parent_block_id' => 9999,
                'parent_placeholder' => 'main',
                'parent_position' => 1,
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/en/blocks/32/move',
            [],
            [],
            [],
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
            [
                'parent_block_id' => 33,
                'parent_placeholder' => 'unknown',
                'parent_position' => 1,
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/en/blocks/34/move',
            [],
            [],
            [],
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
            [
                'parent_block_id' => 32,
                'parent_placeholder' => 'main',
                'parent_position' => 0,
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/en/blocks/31/move',
            [],
            [],
            [],
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
            [
                'parent_block_id' => 33,
                'parent_placeholder' => 'left',
                'parent_position' => 9999,
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/en/blocks/34/move',
            [],
            [],
            [],
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
            [
                'parent_block_id' => 38,
                'parent_placeholder' => 'main',
                'parent_position' => 0,
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/en/blocks/33/move',
            [],
            [],
            [],
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
            [
                'parent_block_id' => [42],
                'parent_placeholder' => 'main',
                'parent_position' => 1,
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/en/blocks/32/move',
            [],
            [],
            [],
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
            [
                'parent_block_id' => 33,
                'parent_placeholder' => 42,
                'parent_position' => 1,
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/en/blocks/34/move',
            [],
            [],
            [],
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
            [
                'parent_block_id' => 33,
                'parent_placeholder' => 'main',
                'parent_position' => '1',
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/en/blocks/34/move',
            [],
            [],
            [],
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
            [
                'parent_placeholder' => 'main',
                'parent_position' => 1,
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/en/blocks/32/move',
            [],
            [],
            [],
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
            [
                'parent_block_id' => 33,
                'parent_position' => 1,
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/en/blocks/34/move',
            [],
            [],
            [],
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
            [
                'parent_block_id' => 33,
                'parent_placeholder' => 'main',
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/en/blocks/34/move',
            [],
            [],
            [],
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
            [
                'layout_id' => 1,
                'zone_identifier' => 'left',
                'parent_position' => 0,
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/en/blocks/31/move/zone',
            [],
            [],
            [],
            $data
        );

        $this->assertEmptyResponse($this->client->getResponse());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::moveToZone
     */
    public function testMoveToZoneWithNonExistentBlock()
    {
        $data = $this->jsonEncode([]);

        $this->client->request(
            'POST',
            '/bm/api/v1/en/blocks/9999/move/zone',
            [],
            [],
            [],
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
            [
                'layout_id' => 9999,
                'zone_identifier' => 'left',
                'parent_position' => 1,
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/en/blocks/31/move/zone',
            [],
            [],
            [],
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_NOT_FOUND,
            'Could not find zone with identifier "left"'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::moveToZone
     */
    public function testMoveToZoneWithNonExistentZoneIdentifier()
    {
        $data = $this->jsonEncode(
            [
                'layout_id' => 1,
                'zone_identifier' => 'unknown',
                'parent_position' => 1,
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/en/blocks/31/move/zone',
            [],
            [],
            [],
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
            [
                'layout_id' => 1,
                'zone_identifier' => 'top',
                'parent_position' => 0,
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/en/blocks/31/move/zone',
            [],
            [],
            [],
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_UNPROCESSABLE_ENTITY,
            'Argument "zone" has an invalid state. Block is not allowed in specified zone.'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::moveToZone
     */
    public function testMoveToZoneWithOutOfRangePosition()
    {
        $data = $this->jsonEncode(
            [
                'layout_id' => 1,
                'zone_identifier' => 'left',
                'parent_position' => 9999,
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/en/blocks/31/move/zone',
            [],
            [],
            [],
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
            [
                'layout_id' => [42],
                'zone_identifier' => 'left',
                'parent_position' => 1,
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/en/blocks/31/move/zone',
            [],
            [],
            [],
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
            [
                'layout_id' => 1,
                'zone_identifier' => 42,
                'parent_position' => 1,
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/en/blocks/31/move/zone',
            [],
            [],
            [],
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "identifier": This value should be of type string.'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::moveToZone
     */
    public function testMoveToZoneWithInvalidPosition()
    {
        $data = $this->jsonEncode(
            [
                'layout_id' => 1,
                'zone_identifier' => 'left',
                'parent_position' => '1',
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/en/blocks/31/move/zone',
            [],
            [],
            [],
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
            [
                'zone_identifier' => 'left',
                'parent_position' => 1,
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/en/blocks/31/move/zone',
            [],
            [],
            [],
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
            [
                'layout_id' => 1,
                'parent_position' => 1,
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/en/blocks/31/move/zone',
            [],
            [],
            [],
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "identifier": This value should not be blank.'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::moveToZone
     */
    public function testMoveToZoneWithMissingPosition()
    {
        $data = $this->jsonEncode(
            [
                'layout_id' => 1,
                'zone_identifier' => 'left',
            ]
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/en/blocks/31/move/zone',
            [],
            [],
            [],
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
        $data = $this->jsonEncode([]);

        $this->client->request(
            'POST',
            '/bm/api/v1/en/blocks/31/restore?html=false',
            [],
            [],
            [],
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
        $data = $this->jsonEncode([]);

        $this->client->request(
            'POST',
            '/bm/api/v1/en/blocks/9999/restore',
            [],
            [],
            [],
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
        $data = $this->jsonEncode([]);

        $this->client->request(
            'DELETE',
            '/bm/api/v1/en/blocks/31',
            [],
            [],
            [],
            $data
        );

        $this->assertEmptyResponse($this->client->getResponse());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\BlockController::delete
     */
    public function testDeleteWithNonExistentBlock()
    {
        $data = $this->jsonEncode([]);

        $this->client->request(
            'DELETE',
            '/bm/api/v1/en/blocks/9999',
            [],
            [],
            [],
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_NOT_FOUND,
            'Could not find block with identifier "9999"'
        );
    }
}
