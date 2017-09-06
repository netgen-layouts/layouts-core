<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\Controller\API\V1;

use Netgen\BlockManager\Core\Service\LayoutService;
use Netgen\BlockManager\Core\Values\Layout\Layout;
use Netgen\BlockManager\Exception\BadStateException;
use Netgen\Bundle\BlockManagerBundle\Tests\Controller\API\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Response;

class LayoutControllerTest extends JsonApiTestCase
{
    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\LayoutController::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\LayoutController::checkPermissions
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
            Response::HTTP_NOT_FOUND,
            'Could not find layout with identifier "9999"'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\LayoutController::viewLayoutBlocks
     */
    public function testViewLayoutBlocks()
    {
        $this->client->request('GET', '/bm/api/v1/en/layouts/1/blocks?html=false');

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
        $this->client->request('GET', '/bm/api/v1/en/layouts/1/blocks?published=true&html=false');

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
        $this->client->request('GET', '/bm/api/v1/en/layouts/9999/blocks');

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_NOT_FOUND,
            'Could not find layout with identifier "9999"'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\LayoutController::viewZoneBlocks
     */
    public function testViewZoneBlocks()
    {
        $this->client->request('GET', '/bm/api/v1/en/layouts/1/zones/right/blocks?html=false');

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
        $this->client->request('GET', '/bm/api/v1/en/layouts/1/zones/right/blocks?published=true&html=false');

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
        $this->client->request('GET', '/bm/api/v1/en/layouts/1/zones/unknown/blocks');

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_NOT_FOUND,
            'Could not find zone with identifier "unknown"'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\LayoutController::viewZoneBlocks
     */
    public function testViewZoneBlocksWithNonExistentLayout()
    {
        $this->client->request('GET', '/bm/api/v1/en/layouts/9999/zones/right/blocks');

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_NOT_FOUND,
            'Could not find zone with identifier "right"'
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
            Response::HTTP_NOT_FOUND,
            'Could not find zone with identifier "unknown"'
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
            Response::HTTP_NOT_FOUND,
            'Could not find zone with identifier "right"'
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
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "layoutId": This value should not be blank.'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\LayoutController::linkZone
     */
    public function testLinkZoneWithInvalidLinkedLayoutId()
    {
        $data = $this->jsonEncode(
            array(
                'linked_layout_id' => array(42),
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
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "layoutId": This value should be of type scalar.'
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
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "identifier": This value should not be blank.'
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
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "identifier": This value should be of type string.'
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
            Response::HTTP_NOT_FOUND,
            'Could not find zone with identifier "unknown"'
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
            Response::HTTP_NOT_FOUND,
            'Could not find zone with identifier "right"'
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
            Response::HTTP_UNPROCESSABLE_ENTITY,
            'Argument "linkedZone" has an invalid state. Linked zone is not in the shared layout.'
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
            Response::HTTP_NOT_FOUND,
            'Could not find zone with identifier "unknown"'
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
            Response::HTTP_NOT_FOUND,
            'Could not find zone with identifier "right"'
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
                'description' => 'My new layout description',
                'locale' => 'en',
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
    public function testCreateWithMissingDescription()
    {
        $data = $this->jsonEncode(
            array(
                'layout_type' => '4_zones_a',
                'name' => 'My new layout',
                'locale' => 'en',
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
            'v1/layouts/create_layout_empty_description',
            Response::HTTP_CREATED,
            array('created_at', 'updated_at')
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\LayoutController::create
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator\LayoutValidator::validateCreateLayout
     */
    public function testCreateWithEmptyDescription()
    {
        $data = $this->jsonEncode(
            array(
                'layout_type' => '4_zones_a',
                'name' => 'My new layout',
                'description' => '',
                'locale' => 'en',
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
            'v1/layouts/create_layout_empty_description',
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
                'locale' => 'en',
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
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "layout_type": This value should be of type string.'
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
                'locale' => 'en',
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
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "layout_type": This value should not be blank.'
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
                'locale' => 'en',
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
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "name": This value should be of type string.'
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
                'locale' => 'en',
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
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "name": This value should not be blank.'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\LayoutController::create
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator\LayoutValidator::validateCreateLayout
     */
    public function testCreateWithInvalidDescription()
    {
        $data = $this->jsonEncode(
            array(
                'layout_type' => '4_zones_a',
                'name' => 'My name',
                'description' => 42,
                'locale' => 'en',
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
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "description": This value should be of type string.'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\LayoutController::create
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator\LayoutValidator::validateCreateLayout
     */
    public function testCreateWithInvalidLocale()
    {
        $data = $this->jsonEncode(
            array(
                'layout_type' => '4_zones_a',
                'name' => 'My new layout',
                'description' => 'My new layout description',
                'locale' => 42,
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
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "locale": This value should be of type string.'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\LayoutController::create
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator\LayoutValidator::validateCreateLayout
     */
    public function testCreateWithMissingLocale()
    {
        $data = $this->jsonEncode(
            array(
                'layout_type' => '4_zones_a',
                'name' => 'My new layout',
                'description' => 'My new layout description',
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
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "locale": This value should not be blank.'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\LayoutController::create
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator\LayoutValidator::validateCreateLayout
     */
    public function testCreateWithNonExistentLocale()
    {
        $data = $this->jsonEncode(
            array(
                'layout_type' => '4_zones_a',
                'name' => 'My new layout',
                'description' => 'My new layout description',
                'locale' => 'unknown',
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
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "locale": This value is not a valid locale.'
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
                'locale' => 'en',
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
            Response::HTTP_UNPROCESSABLE_ENTITY,
            'Argument "layout_type" has an invalid state. Layout type does not exist.'
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
                'locale' => 'en',
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
            Response::HTTP_UNPROCESSABLE_ENTITY,
            'Argument "name" has an invalid state. Layout with provided name already exists.'
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
                'description' => 'My new layout description',
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
                'description' => 'My new layout description',
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
    public function testCopyWithNonExistingDescription()
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
            'v1/layouts/copy_layout_without_description',
            Response::HTTP_CREATED,
            array('created_at', 'updated_at')
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\LayoutController::copy
     */
    public function testCopyWithEmptyDescription()
    {
        $data = $this->jsonEncode(
            array(
                'name' => 'My new layout name',
                'description' => '',
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
            'v1/layouts/copy_layout_empty_description',
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
            Response::HTTP_NOT_FOUND,
            'Could not find layout with identifier "9999"'
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
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "name": This value should be of type string.'
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
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "name": This value should not be blank.'
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
            Response::HTTP_UNPROCESSABLE_ENTITY,
            'Argument "layoutCopyStruct" has an invalid state. Layout with provided name already exists.'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\LayoutController::copy
     */
    public function testCopyWithInvalidDescription()
    {
        $data = $this->jsonEncode(
            array(
                'name' => 'New name',
                'description' => 42,
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
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "description": This value should be of type string.'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\LayoutController::changeType
     */
    public function testChangeType()
    {
        $data = $this->jsonEncode(
            array(
                'new_type' => '4_zones_b',
                'zone_mappings' => array(
                    'left' => array('left'),
                    'right' => array('right'),
                ),
            )
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/layouts/1/change_type?html=false',
            array(),
            array(),
            array(),
            $data
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'v1/layouts/change_type',
            Response::HTTP_OK,
            array('created_at', 'updated_at')
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\LayoutController::changeType
     */
    public function testChangeTypeWithoutMappings()
    {
        $data = $this->jsonEncode(
            array(
                'new_type' => '4_zones_b',
            )
        );

        $this->client->request(
            'POST',
            '/bm/api/v1/layouts/1/change_type?html=false',
            array(),
            array(),
            array(),
            $data
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'v1/layouts/change_type_without_mappings',
            Response::HTTP_OK,
            array('created_at', 'updated_at')
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
            Response::HTTP_NOT_FOUND,
            'Could not find layout with identifier "9999"'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\LayoutController::createDraft
     */
    public function testCreateDraftWithTransactionRollback()
    {
        $clientContainer = $this->client->getContainer();

        /** @var \Mockery\MockInterface $locationMock */
        $layoutServiceMock = $clientContainer->mock(
            'netgen_block_manager.api.service.layout',
            LayoutService::class
        );

        $layoutServiceMock
            ->shouldReceive('beginTransaction')
            ->getMock()
                ->shouldReceive('rollbackTransaction')
                ->once()
            ->getMock()
                ->shouldReceive('loadLayout')
                ->andReturn(new Layout())
            ->getMock()
                ->shouldReceive('loadLayoutDraft')
            ->getMock()
                ->shouldReceive('discardDraft')
            ->getMock()
                ->shouldReceive('createDraft')
                ->andThrow(new BadStateException('test', 'Test message.'));

        $data = $this->jsonEncode(array());

        $this->client->request(
            'POST',
            '/bm/api/v1/layouts/1/draft?html=false',
            array(),
            array(),
            array(),
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_UNPROCESSABLE_ENTITY,
            'Argument "test" has an invalid state. Test message.'
        );

        $clientContainer->unmock('netgen_block_manager.api.service.layout');
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
            Response::HTTP_NOT_FOUND,
            'Could not find layout with identifier "9999"'
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
            Response::HTTP_NOT_FOUND,
            'Could not find layout with identifier "9999"'
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
            Response::HTTP_NOT_FOUND,
            'Could not find layout with identifier "9999"'
        );
    }
}
