<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Tests\Controller\API\V1\Layout;

use Netgen\Bundle\BlockManagerBundle\Tests\Controller\API\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class CreateTest extends JsonApiTestCase
{
    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\Create::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\Create::__invoke
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\Utils\CreateStructValidator::validateCreateLayout
     */
    public function testCreate(): void
    {
        $data = $this->jsonEncode(
            [
                'layout_type' => '4_zones_a',
                'name' => 'My new layout',
                'description' => 'My new layout description',
                'locale' => 'en',
            ]
        );

        $this->client->request(
            Request::METHOD_POST,
            '/bm/api/v1/layouts?html=false',
            [],
            [],
            [],
            $data
        );

        self::assertResponse(
            $this->client->getResponse(),
            'v1/layouts/create_layout',
            Response::HTTP_CREATED
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\Create::__invoke
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\Utils\CreateStructValidator::validateCreateLayout
     */
    public function testCreateWithMissingDescription(): void
    {
        $data = $this->jsonEncode(
            [
                'layout_type' => '4_zones_a',
                'name' => 'My new layout',
                'locale' => 'en',
            ]
        );

        $this->client->request(
            Request::METHOD_POST,
            '/bm/api/v1/layouts?html=false',
            [],
            [],
            [],
            $data
        );

        self::assertResponse(
            $this->client->getResponse(),
            'v1/layouts/create_layout_empty_description',
            Response::HTTP_CREATED
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\Create::__invoke
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\Utils\CreateStructValidator::validateCreateLayout
     */
    public function testCreateWithEmptyDescription(): void
    {
        $data = $this->jsonEncode(
            [
                'layout_type' => '4_zones_a',
                'name' => 'My new layout',
                'description' => '',
                'locale' => 'en',
            ]
        );

        $this->client->request(
            Request::METHOD_POST,
            '/bm/api/v1/layouts?html=false',
            [],
            [],
            [],
            $data
        );

        self::assertResponse(
            $this->client->getResponse(),
            'v1/layouts/create_layout_empty_description',
            Response::HTTP_CREATED
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\Create::__invoke
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\Utils\CreateStructValidator::validateCreateLayout
     */
    public function testCreateWithInvalidLayoutType(): void
    {
        $data = $this->jsonEncode(
            [
                'layout_type' => 42,
                'name' => 'My new layout',
                'locale' => 'en',
            ]
        );

        $this->client->request(
            Request::METHOD_POST,
            '/bm/api/v1/layouts',
            [],
            [],
            [],
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "layout_type": This value should be of type string.'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\Create::__invoke
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\Utils\CreateStructValidator::validateCreateLayout
     */
    public function testCreateWithMissingLayoutType(): void
    {
        $data = $this->jsonEncode(
            [
                'name' => 'My new layout',
                'locale' => 'en',
            ]
        );

        $this->client->request(
            Request::METHOD_POST,
            '/bm/api/v1/layouts',
            [],
            [],
            [],
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "layout_type": This value should not be blank.'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\Create::__invoke
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\Utils\CreateStructValidator::validateCreateLayout
     */
    public function testCreateWithInvalidDescription(): void
    {
        $data = $this->jsonEncode(
            [
                'layout_type' => '4_zones_a',
                'name' => 'My name',
                'description' => 42,
                'locale' => 'en',
            ]
        );

        $this->client->request(
            Request::METHOD_POST,
            '/bm/api/v1/layouts',
            [],
            [],
            [],
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "description": This value should be of type string.'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\Create::__invoke
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\Utils\CreateStructValidator::validateCreateLayout
     */
    public function testCreateWithInvalidLocale(): void
    {
        $data = $this->jsonEncode(
            [
                'layout_type' => '4_zones_a',
                'name' => 'My new layout',
                'description' => 'My new layout description',
                'locale' => 42,
            ]
        );

        $this->client->request(
            Request::METHOD_POST,
            '/bm/api/v1/layouts',
            [],
            [],
            [],
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "locale": Expected argument of type "string", "integer" given'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\Create::__invoke
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\Utils\CreateStructValidator::validateCreateLayout
     */
    public function testCreateWithMissingLocale(): void
    {
        $data = $this->jsonEncode(
            [
                'layout_type' => '4_zones_a',
                'name' => 'My new layout',
                'description' => 'My new layout description',
            ]
        );

        $this->client->request(
            Request::METHOD_POST,
            '/bm/api/v1/layouts',
            [],
            [],
            [],
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "locale": This value should not be blank.'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\Create::__invoke
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\Utils\CreateStructValidator::validateCreateLayout
     */
    public function testCreateWithNonExistentLocale(): void
    {
        $data = $this->jsonEncode(
            [
                'layout_type' => '4_zones_a',
                'name' => 'My new layout',
                'description' => 'My new layout description',
                'locale' => 'unknown',
            ]
        );

        $this->client->request(
            Request::METHOD_POST,
            '/bm/api/v1/layouts',
            [],
            [],
            [],
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "locale": This value is not a valid locale.'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\Create::__invoke
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\Utils\CreateStructValidator::validateCreateLayout
     */
    public function testCreateWithNonExistingLayoutType(): void
    {
        $data = $this->jsonEncode(
            [
                'layout_type' => 'unknown',
                'name' => 'My new layout',
                'locale' => 'en',
            ]
        );

        $this->client->request(
            Request::METHOD_POST,
            '/bm/api/v1/layouts',
            [],
            [],
            [],
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_UNPROCESSABLE_ENTITY,
            'Argument "layout_type" has an invalid state. Layout type does not exist.'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\Create::__invoke
     * @covers \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Layout\Utils\CreateStructValidator::validateCreateLayout
     */
    public function testCreateWithExistingName(): void
    {
        $data = $this->jsonEncode(
            [
                'layout_type' => '4_zones_a',
                'name' => 'My layout',
                'locale' => 'en',
            ]
        );

        $this->client->request(
            Request::METHOD_POST,
            '/bm/api/v1/layouts',
            [],
            [],
            [],
            $data
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_UNPROCESSABLE_ENTITY,
            'Argument "name" has an invalid state. Layout with provided name already exists.'
        );
    }
}
