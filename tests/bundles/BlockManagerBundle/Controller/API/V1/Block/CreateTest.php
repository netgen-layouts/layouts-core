<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\Controller\API\V1\Block;

use Netgen\Bundle\BlockManagerBundle\Tests\Controller\API\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Response;

final class CreateTest extends JsonApiTestCase
{
    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\Create::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\Create::__invoke
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\Utils\CreateStructBuilder::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\Utils\CreateStructBuilder::buildCreateStruct
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\Utils\CreateStructValidator::validateCreateBlock
     */
    public function testCreate()
    {
        $data = $this->jsonEncode(
            [
                'block_type' => 'list',
                'placeholder' => 'left',
                'position' => 0,
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
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\Create::__invoke
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\Utils\CreateStructBuilder::buildCreateStruct
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\Utils\CreateStructValidator::validateCreateBlock
     */
    public function testCreateWithViewType()
    {
        $data = $this->jsonEncode(
            [
                'block_type' => 'grid',
                'placeholder' => 'left',
                'position' => 0,
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
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\Create::__invoke
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\Utils\CreateStructBuilder::buildCreateStruct
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\Utils\CreateStructValidator::validateCreateBlock
     */
    public function testCreateWithItemViewType()
    {
        $data = $this->jsonEncode(
            [
                'block_type' => 'test_grid',
                'placeholder' => 'left',
                'position' => 0,
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
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\Create::__invoke
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\Utils\CreateStructBuilder::buildCreateStruct
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\Utils\CreateStructValidator::validateCreateBlock
     */
    public function testCreateWithNoPosition()
    {
        $data = $this->jsonEncode(
            [
                'block_type' => 'list',
                'placeholder' => 'left',
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
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\Create::__invoke
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\Utils\CreateStructValidator::validateCreateBlock
     */
    public function testCreateWithNonContainerTargetBlock()
    {
        $data = $this->jsonEncode(
            [
                'block_type' => 'list',
                'placeholder' => 'main',
                'position' => 0,
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
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\Create::__invoke
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\Utils\CreateStructValidator::validateCreateBlock
     */
    public function testCreateWithContainerInsideContainer()
    {
        $data = $this->jsonEncode(
            [
                'block_type' => 'column',
                'placeholder' => 'left',
                'position' => 0,
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
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\Create::__invoke
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\Utils\CreateStructValidator::validateCreateBlock
     */
    public function testCreateWithInvalidBlockType()
    {
        $data = $this->jsonEncode(
            [
                'block_type' => 42,
                'placeholder' => 'main',
                'position' => 0,
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
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\Create::__invoke
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\Utils\CreateStructValidator::validateCreateBlock
     */
    public function testCreateWithMissingBlockType()
    {
        $data = $this->jsonEncode(
            [
                'placeholder' => 'main',
                'position' => 0,
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
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\Create::__invoke
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\Utils\CreateStructValidator::validateCreateBlock
     */
    public function testCreateWithInvalidPlaceholder()
    {
        $data = $this->jsonEncode(
            [
                'block_type' => 'title',
                'placeholder' => 42,
                'position' => 0,
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
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\Create::__invoke
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\Utils\CreateStructValidator::validateCreateBlock
     */
    public function testCreateWithMissingPlaceholder()
    {
        $data = $this->jsonEncode(
            [
                'block_type' => 'title',
                'position' => 0,
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
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\Create::__invoke
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\Utils\CreateStructValidator::validateCreateBlock
     */
    public function testCreateWithInvalidPosition()
    {
        $data = $this->jsonEncode(
            [
                'block_type' => 'title',
                'placeholder' => 'main',
                'position' => '0',
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
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\Create::__invoke
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\Utils\CreateStructValidator::validateCreateBlock
     */
    public function testCreateWithNonExistentBlockType()
    {
        $data = $this->jsonEncode(
            [
                'block_type' => 'unknown',
                'placeholder' => 'main',
                'position' => 0,
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
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\Create::__invoke
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\Utils\CreateStructValidator::validateCreateBlock
     */
    public function testCreateWithNonExistentPlaceholder()
    {
        $data = $this->jsonEncode(
            [
                'block_type' => 'title',
                'placeholder' => 'unknown',
                'position' => 0,
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
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\Create::__invoke
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\Utils\CreateStructValidator::validateCreateBlock
     */
    public function testCreateWithOutOfRangePosition()
    {
        $data = $this->jsonEncode(
            [
                'block_type' => 'list',
                'placeholder' => 'left',
                'position' => 9999,
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
}
