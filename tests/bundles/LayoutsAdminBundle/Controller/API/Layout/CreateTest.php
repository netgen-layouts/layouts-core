<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\Layout;

use Netgen\Bundle\LayoutsAdminBundle\Controller\API\Layout\Create;
use Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\JsonApiTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[CoversClass(Create::class)]
final class CreateTest extends JsonApiTestCase
{
    public function testCreate(): void
    {
        $data = $this->jsonEncode(
            [
                'layout_type' => '4_zones_a',
                'name' => 'My new layout',
                'description' => 'My new layout description',
                'locale' => 'en',
            ],
        );

        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/app/api/layouts?html=false',
            [],
            [],
            [],
            $data,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'layouts/create_layout',
            Response::HTTP_CREATED,
        );
    }

    public function testCreateWithMissingDescription(): void
    {
        $data = $this->jsonEncode(
            [
                'layout_type' => '4_zones_a',
                'name' => 'My new layout',
                'locale' => 'en',
            ],
        );

        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/app/api/layouts?html=false',
            [],
            [],
            [],
            $data,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'layouts/create_layout_empty_description',
            Response::HTTP_CREATED,
        );
    }

    public function testCreateWithEmptyDescription(): void
    {
        $data = $this->jsonEncode(
            [
                'layout_type' => '4_zones_a',
                'name' => 'My new layout',
                'description' => '',
                'locale' => 'en',
            ],
        );

        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/app/api/layouts?html=false',
            [],
            [],
            [],
            $data,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'layouts/create_layout_empty_description',
            Response::HTTP_CREATED,
        );
    }

    public function testCreateWithInvalidLayoutType(): void
    {
        $data = $this->jsonEncode(
            [
                'layout_type' => 42,
                'name' => 'My new layout',
                'locale' => 'en',
            ],
        );

        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/app/api/layouts',
            [],
            [],
            [],
            $data,
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "layout_type": This value should be of type string.',
        );
    }

    public function testCreateWithMissingLayoutType(): void
    {
        $data = $this->jsonEncode(
            [
                'name' => 'My new layout',
                'locale' => 'en',
            ],
        );

        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/app/api/layouts',
            [],
            [],
            [],
            $data,
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "layout_type": This value should not be blank.',
        );
    }

    public function testCreateWithNonExistingLayoutType(): void
    {
        $data = $this->jsonEncode(
            [
                'layout_type' => 'unknown',
                'name' => 'My new layout',
                'locale' => 'en',
            ],
        );

        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/app/api/layouts',
            [],
            [],
            [],
            $data,
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_UNPROCESSABLE_ENTITY,
            'Argument "layout_type" has an invalid state. Layout type does not exist.',
        );
    }

    public function testCreateWithInvalidName(): void
    {
        $data = $this->jsonEncode(
            [
                'layout_type' => '4_zones_a',
                'name' => 42,
                'locale' => 'en',
            ],
        );

        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/app/api/layouts',
            [],
            [],
            [],
            $data,
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "name": This value should be of type string.',
        );
    }

    public function testCreateWithEmptyName(): void
    {
        $data = $this->jsonEncode(
            [
                'layout_type' => '4_zones_a',
                'name' => '',
                'locale' => 'en',
            ],
        );

        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/app/api/layouts',
            [],
            [],
            [],
            $data,
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "name": This value should not be blank.',
        );
    }

    public function testCreateWithMissingName(): void
    {
        $data = $this->jsonEncode(
            [
                'layout_type' => '4_zones_a',
                'locale' => 'en',
            ],
        );

        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/app/api/layouts',
            [],
            [],
            [],
            $data,
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "name": This value should not be blank.',
        );
    }

    public function testCreateWithExistingName(): void
    {
        $data = $this->jsonEncode(
            [
                'layout_type' => '4_zones_a',
                'name' => 'My layout',
                'locale' => 'en',
            ],
        );

        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/app/api/layouts',
            [],
            [],
            [],
            $data,
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_UNPROCESSABLE_ENTITY,
            'Argument "name" has an invalid state. Layout with provided name already exists.',
        );
    }

    public function testCreateWithInvalidDescription(): void
    {
        $data = $this->jsonEncode(
            [
                'layout_type' => '4_zones_a',
                'name' => 'My name',
                'description' => 42,
                'locale' => 'en',
            ],
        );

        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/app/api/layouts',
            [],
            [],
            [],
            $data,
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "description": This value should be of type string.',
        );
    }

    public function testCreateWithInvalidLocale(): void
    {
        $data = $this->jsonEncode(
            [
                'layout_type' => '4_zones_a',
                'name' => 'My new layout',
                'description' => 'My new layout description',
                'locale' => 42,
            ],
        );

        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/app/api/layouts',
            [],
            [],
            [],
            $data,
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_BAD_REQUEST,
            '/^There was an error validating "locale": Expected argument of type "string", "int(eger)?" given$/',
        );
    }

    public function testCreateWithMissingLocale(): void
    {
        $data = $this->jsonEncode(
            [
                'layout_type' => '4_zones_a',
                'name' => 'My new layout',
                'description' => 'My new layout description',
            ],
        );

        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/app/api/layouts',
            [],
            [],
            [],
            $data,
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "locale": This value should not be blank.',
        );
    }

    public function testCreateWithNonExistentLocale(): void
    {
        $data = $this->jsonEncode(
            [
                'layout_type' => '4_zones_a',
                'name' => 'My new layout',
                'description' => 'My new layout description',
                'locale' => 'unknown',
            ],
        );

        $this->client->request(
            Request::METHOD_POST,
            '/nglayouts/app/api/layouts',
            [],
            [],
            [],
            $data,
        );

        $this->assertException(
            $this->client->getResponse(),
            Response::HTTP_BAD_REQUEST,
            'There was an error validating "locale": This value is not a valid locale.',
        );
    }
}
