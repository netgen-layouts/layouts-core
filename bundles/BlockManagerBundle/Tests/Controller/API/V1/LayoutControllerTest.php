<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\Controller\API\V1;

use Netgen\Bundle\BlockManagerBundle\Tests\Controller\API\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Response;

class LayoutControllerTest extends JsonApiTestCase
{
    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\LayoutController::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\LayoutController::loadSharedLayouts
     */
    public function testLoadSharedLayouts()
    {
        $this->client->request('GET', '/bm/api/v1/layouts/shared');

        $this->assertResponse(
            $this->client->getResponse(),
            'v1/layouts/shared_layouts',
            Response::HTTP_OK
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\LayoutController::load
     */
    public function testLoad()
    {
        $this->client->request('GET', '/bm/api/v1/layouts/1?html=false');

        $this->assertResponse(
            $this->client->getResponse(),
            'v1/layouts/load_layout',
            Response::HTTP_OK
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\LayoutController::load
     */
    public function testLoadInPublishedState()
    {
        $this->client->request('GET', '/bm/api/v1/layouts/1?published=true&html=false');

        $this->assertResponse(
            $this->client->getResponse(),
            'v1/layouts/load_published_layout',
            Response::HTTP_OK
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\LayoutController::load
     */
    public function testLoadWithNonExistentLayout()
    {
        $this->client->request('GET', '/bm/api/v1/layouts/9999');

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_NOT_FOUND
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\LayoutController::viewLayoutBlocks
     */
    public function testViewLayoutBlocks()
    {
        $this->client->request('GET', '/bm/api/v1/layouts/1/blocks?html=false');

        $this->assertResponse(
            $this->client->getResponse(),
            'v1/layouts/load_layout_blocks',
            Response::HTTP_OK
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\LayoutController::viewLayoutBlocks
     */
    public function testViewLayoutBlocksInPublishedState()
    {
        $this->client->request('GET', '/bm/api/v1/layouts/1/blocks?published=true&html=false');

        $this->assertResponse(
            $this->client->getResponse(),
            'v1/layouts/load_published_layout_blocks',
            Response::HTTP_OK
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\LayoutController::viewLayoutBlocks
     */
    public function testViewLayoutBlocksWithNonExistentLayout()
    {
        $this->client->request('GET', '/bm/api/v1/layouts/9999/blocks');

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_NOT_FOUND
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\LayoutController::viewZoneBlocks
     */
    public function testViewZoneBlocks()
    {
        $this->client->request('GET', '/bm/api/v1/layouts/1/zones/right/blocks?html=false');

        $this->assertResponse(
            $this->client->getResponse(),
            'v1/layouts/load_zone_blocks',
            Response::HTTP_OK
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\LayoutController::viewZoneBlocks
     */
    public function testViewZoneBlocksInPublishedState()
    {
        $this->client->request('GET', '/bm/api/v1/layouts/1/zones/right/blocks?published=true&html=false');

        $this->assertResponse(
            $this->client->getResponse(),
            'v1/layouts/load_published_zone_blocks',
            Response::HTTP_OK
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\LayoutController::viewZoneBlocks
     */
    public function testViewZoneBlocksWithNonExistentZone()
    {
        $this->client->request('GET', '/bm/api/v1/layouts/1/zones/unknown/blocks');

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_NOT_FOUND
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\LayoutController::viewZoneBlocks
     */
    public function testViewZoneBlocksWithNonExistentLayout()
    {
        $this->client->request('GET', '/bm/api/v1/layouts/9999/zones/right/blocks');

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_NOT_FOUND
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\LayoutController::linkZone
     */
    public function testLinkZone()
    {
        $data = $this->jsonEncode(
            array(
                'linked_layout_id' => 5,
                'linked_zone_identifier' => 'right',
            )
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/layouts/1/zones/right/link',
            array(),
            array(),
            array(),
            $data
        );

        $this->assertEmptyResponse($this->client->getResponse());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\LayoutController::linkZone
     */
    public function testLinkZoneWithNonExistentZone()
    {
        $data = $this->jsonEncode(array());

        $this->client->request(
            'POST',
            '/bm/api/v1/layouts/1/zones/unknown/link',
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
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\LayoutController::linkZone
     */
    public function testLinkZoneWithNonExistentLayout()
    {
        $data = $this->jsonEncode(array());

        $this->client->request(
            'POST',
            '/bm/api/v1/layouts/9999/zones/right/link',
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
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\LayoutController::linkZone
     */
    public function testLinkZoneWithMissingLinkedLayoutId()
    {
        $data = $this->jsonEncode(
            array(
                'linked_zone_identifier' => 'right',
            )
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/layouts/1/zones/right/link',
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
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\LayoutController::linkZone
     */
    public function testLinkZoneWithInvalidLinkedLayoutId()
    {
        $data = $this->jsonEncode(
            array(
                'linked_layout_id' => array(),
                'linked_zone_identifier' => 'right',
            )
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/layouts/1/zones/right/link',
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
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\LayoutController::linkZone
     */
    public function testLinkZoneWithMissingLinkedZoneIdentifier()
    {
        $data = $this->jsonEncode(
            array(
                'linked_layout_id' => 5,
            )
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/layouts/1/zones/right/link',
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
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\LayoutController::linkZone
     */
    public function testLinkZoneWithInvalidLinkedZoneIdentifier()
    {
        $data = $this->jsonEncode(
            array(
                'linked_layout_id' => 5,
                'linked_zone_identifier' => 42,
            )
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/layouts/1/zones/right/link',
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
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\LayoutController::linkZone
     */
    public function testLinkZoneWithNonExistentLinkedZone()
    {
        $data = $this->jsonEncode(
            array(
                'linked_layout_id' => 5,
                'linked_zone_identifier' => 'unknown',
            )
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/layouts/1/zones/right/link',
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
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\LayoutController::linkZone
     */
    public function testLinkZoneWithNonExistentLinkedLayout()
    {
        $data = $this->jsonEncode(
            array(
                'linked_layout_id' => 9999,
                'linked_zone_identifier' => 'right',
            )
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/layouts/1/zones/right/link',
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
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\LayoutController::linkZone
     */
    public function testLinkZoneWithNonSharedLinkedLayout()
    {
        $data = $this->jsonEncode(
            array(
                'linked_layout_id' => 2,
                'linked_zone_identifier' => 'right',
            )
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/layouts/1/zones/right/link',
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
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\LayoutController::unlinkZone
     */
    public function testUnlinkZone()
    {
        $data = $this->jsonEncode(array());

        $this->client->request(
            'DELETE',
            '/bm/api/v1/layouts/1/zones/right/link',
            array(),
            array(),
            array(),
            $data
        );

        $this->assertEmptyResponse($this->client->getResponse());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\LayoutController::unlinkZone
     */
    public function testUnlinkZoneWithNonExistentZone()
    {
        $data = $this->jsonEncode(array());

        $this->client->request(
            'DELETE',
            '/bm/api/v1/layouts/1/zones/unknown/link',
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
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\LayoutController::unlinkZone
     */
    public function testUnlinkZoneWithNonExistentLayout()
    {
        $data = $this->jsonEncode(array());

        $this->client->request(
            'DELETE',
            '/bm/api/v1/layouts/9999/zones/right/link',
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
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\LayoutController::create
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator\LayoutValidator::validateCreateLayout
     */
    public function testCreate()
    {
        $data = $this->jsonEncode(
            array(
                'layout_type' => '4_zones_a',
                'name' => 'My new layout',
            )
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/layouts?html=false',
            array(),
            array(),
            array(),
            $data
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'v1/layouts/create_layout',
            Response::HTTP_CREATED,
            array('created_at', 'updated_at')
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\LayoutController::create
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator\LayoutValidator::validateCreateLayout
     */
    public function testCreateWithInvalidLayoutType()
    {
        $data = $this->jsonEncode(
            array(
                'layout_type' => 42,
                'name' => 'My new layout',
            )
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/layouts',
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
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\LayoutController::create
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator\LayoutValidator::validateCreateLayout
     */
    public function testCreateWithMissingLayoutType()
    {
        $data = $this->jsonEncode(
            array(
                'name' => 'My new layout',
            )
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/layouts',
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
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\LayoutController::create
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator\LayoutValidator::validateCreateLayout
     */
    public function testCreateWithInvalidName()
    {
        $data = $this->jsonEncode(
            array(
                'layout_type' => '4_zones_a',
                'name' => 42,
            )
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/layouts',
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
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\LayoutController::create
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator\LayoutValidator::validateCreateLayout
     */
    public function testCreateWithMissingName()
    {
        $data = $this->jsonEncode(
            array(
                'layout_type' => '4_zones_a',
            )
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/layouts',
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
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\LayoutController::create
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator\LayoutValidator::validateCreateLayout
     */
    public function testCreateWithNonExistingLayoutType()
    {
        $data = $this->jsonEncode(
            array(
                'layout_type' => 'unknown',
                'name' => 'My new layout',
            )
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/layouts',
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
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\LayoutController::create
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator\LayoutValidator::validateCreateLayout
     */
    public function testCreateWithExistingName()
    {
        $data = $this->jsonEncode(
            array(
                'layout_type' => '4_zones_a',
                'name' => 'My layout',
            )
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/layouts',
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
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\LayoutController::copy
     */
    public function testCopy()
    {
        $data = $this->jsonEncode(
            array(
                'name' => 'My new layout name',
            )
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/layouts/1/copy?html=false',
            array(),
            array(),
            array(),
            $data
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'v1/layouts/copy_layout',
            Response::HTTP_CREATED,
            array('created_at', 'updated_at')
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\LayoutController::copy
     */
    public function testCopyInPublishedState()
    {
        $data = $this->jsonEncode(
            array(
                'name' => 'My new layout name',
            )
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/layouts/6/copy?published=true&html=false',
            array(),
            array(),
            array(),
            $data
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'v1/layouts/copy_published_layout',
            Response::HTTP_CREATED,
            array('created_at', 'updated_at')
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\LayoutController::copy
     */
    public function testCopyWithNonExistingLayout()
    {
        $data = $this->jsonEncode(
            array(
                'name' => 'My new layout name',
            )
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/layouts/9999/copy',
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
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\LayoutController::copy
     */
    public function testCopyWithInvalidName()
    {
        $data = $this->jsonEncode(
            array(
                'name' => 42,
            )
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/layouts/1/copy',
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
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\LayoutController::copy
     */
    public function testCopyWithMissingName()
    {
        $data = $this->jsonEncode(array());

        $this->client->request(
            'POST',
            '/bm/api/v1/layouts/1/copy',
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
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\LayoutController::copy
     */
    public function testCopyWithExistingName()
    {
        $data = $this->jsonEncode(
            array(
                'name' => 'My other layout',
            )
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/layouts/1/copy',
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
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\LayoutController::createDraft
     */
    public function testCreateDraft()
    {
        $data = $this->jsonEncode(array());

        $this->client->request(
            'POST',
            '/bm/api/v1/layouts/1/draft?html=false',
            array(),
            array(),
            array(),
            $data
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'v1/layouts/create_layout_draft',
            Response::HTTP_CREATED,
            array('created_at', 'updated_at')
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\LayoutController::createDraft
     */
    public function testCreateDraftWithNonExistentLayout()
    {
        $data = $this->jsonEncode(array());

        $this->client->request(
            'POST',
            '/bm/api/v1/layouts/9999/draft',
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
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\LayoutController::update
     */
    public function testUpdate()
    {
        $data = $this->jsonEncode(
            array(
                'name' => 'My new layout name',
            )
        );

        $this->client->request(
            'PATCH',
            '/bm/api/v1/layouts/1?html=false',
            array(),
            array(),
            array(),
            $data
        );

        $this->assertEmptyResponse($this->client->getResponse());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\LayoutController::update
     */
    public function testUpdateWithNonExistingLayout()
    {
        $data = $this->jsonEncode(
            array(
                'name' => 'My new layout name',
            )
        );

        $this->client->request(
            'PATCH',
            '/bm/api/v1/layouts/9999',
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
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\LayoutController::update
     */
    public function testUpdateWithInvalidName()
    {
        $data = $this->jsonEncode(
            array(
                'name' => 42,
            )
        );

        $this->client->request(
            'PATCH',
            '/bm/api/v1/layouts/1',
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
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\LayoutController::update
     */
    public function testUpdateWithMissingName()
    {
        $data = $this->jsonEncode(array());

        $this->client->request(
            'PATCH',
            '/bm/api/v1/layouts/1',
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
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\LayoutController::update
     */
    public function testUpdateWithExistingName()
    {
        $data = $this->jsonEncode(
            array(
                'name' => 'My other layout',
            )
        );

        $this->client->request(
            'PATCH',
            '/bm/api/v1/layouts/1',
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
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\LayoutController::discardDraft
     */
    public function testDiscardDraft()
    {
        $data = $this->jsonEncode(array());

        $this->client->request(
            'DELETE',
            '/bm/api/v1/layouts/1/draft',
            array(),
            array(),
            array(),
            $data
        );

        $this->assertEmptyResponse($this->client->getResponse());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\LayoutController::discardDraft
     */
    public function testDiscardDraftWithNonExistentLayout()
    {
        $data = $this->jsonEncode(array());

        $this->client->request(
            'DELETE',
            '/bm/api/v1/layouts/9999/draft',
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
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\LayoutController::publishDraft
     */
    public function testPublishDraft()
    {
        $data = $this->jsonEncode(array());

        $this->client->request(
            'POST',
            '/bm/api/v1/layouts/1/publish',
            array(),
            array(),
            array(),
            $data
        );

        $this->assertEmptyResponse($this->client->getResponse());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\LayoutController::publishDraft
     */
    public function testPublishDraftWithNonExistentLayout()
    {
        $data = $this->jsonEncode(array());

        $this->client->request(
            'POST',
            '/bm/api/v1/layouts/9999/publish',
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
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\LayoutController::delete
     */
    public function testDelete()
    {
        $data = $this->jsonEncode(array());

        $this->client->request(
            'DELETE',
            '/bm/api/v1/layouts/1',
            array(),
            array(),
            array(),
            $data
        );

        $this->assertEmptyResponse($this->client->getResponse());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\LayoutController::delete
     */
    public function testDeleteWithNonExistentLayout()
    {
        $data = $this->jsonEncode(array());

        $this->client->request(
            'DELETE',
            '/bm/api/v1/layouts/9999',
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
