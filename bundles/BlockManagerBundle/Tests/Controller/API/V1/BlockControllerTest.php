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
}
