<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\Layout;

use Netgen\Bundle\LayoutsAdminBundle\Controller\API\Layout\Create;
use Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\ApiTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\HttpFoundation\Response;

#[CoversClass(Create::class)]
final class CreateTest extends ApiTestCase
{
    public function testCreate(): void
    {
        $data = [
            'layout_type' => 'test_layout_1',
            'name' => 'My new layout',
            'description' => 'My new layout description',
            'locale' => 'en',
        ];

        $this->browser()
            ->post(
                '/nglayouts/app/api/layouts?html=false',
                ['json' => $data],
            )->assertJson()
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJsonIs('layouts/create_layout');
    }

    public function testCreateWithMissingDescription(): void
    {
        $data = [
            'layout_type' => 'test_layout_1',
            'name' => 'My new layout',
            'locale' => 'en',
        ];

        $this->browser()
            ->post(
                '/nglayouts/app/api/layouts?html=false',
                ['json' => $data],
            )->assertJson()
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJsonIs('layouts/create_layout_empty_description');
    }

    public function testCreateWithEmptyDescription(): void
    {
        $data = [
            'layout_type' => 'test_layout_1',
            'name' => 'My new layout',
            'description' => '',
            'locale' => 'en',
        ];

        $this->browser()
            ->post(
                '/nglayouts/app/api/layouts?html=false',
                ['json' => $data],
            )->assertJson()
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJsonIs('layouts/create_layout_empty_description');
    }

    public function testCreateWithInvalidLayoutType(): void
    {
        $data = [
            'layout_type' => 42,
            'name' => 'My new layout',
            'locale' => 'en',
        ];

        $this->browser()
            ->post(
                '/nglayouts/app/api/layouts',
                ['json' => $data],
            )->assertJson()
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJsonMatches('message', 'There was an error validating "layout_type": This value should be of type string.');
    }

    public function testCreateWithMissingLayoutType(): void
    {
        $data = [
            'name' => 'My new layout',
            'locale' => 'en',
        ];

        $this->browser()
            ->post(
                '/nglayouts/app/api/layouts',
                ['json' => $data],
            )->assertJson()
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJsonMatches('message', 'There was an error validating "layout_type": This value should not be blank.');
    }

    public function testCreateWithNonExistingLayoutType(): void
    {
        $data = [
            'layout_type' => 'unknown',
            'name' => 'My new layout',
            'locale' => 'en',
        ];

        $this->browser()
            ->post(
                '/nglayouts/app/api/layouts',
                ['json' => $data],
            )->assertJson()
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonMatches('message', 'Argument "layout_type" has an invalid state. Layout type does not exist.');
    }

    public function testCreateWithInvalidName(): void
    {
        $data = [
            'layout_type' => 'test_layout_1',
            'name' => 42,
            'locale' => 'en',
        ];

        $this->browser()
            ->post(
                '/nglayouts/app/api/layouts',
                ['json' => $data],
            )->assertJson()
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJsonMatches('message', 'There was an error validating "name": This value should be of type string.');
    }

    public function testCreateWithEmptyName(): void
    {
        $data = [
            'layout_type' => 'test_layout_1',
            'name' => '',
            'locale' => 'en',
        ];

        $this->browser()
            ->post(
                '/nglayouts/app/api/layouts',
                ['json' => $data],
            )->assertJson()
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJsonMatches('message', 'There was an error validating "name": This value should not be blank.');
    }

    public function testCreateWithMissingName(): void
    {
        $data = [
            'layout_type' => 'test_layout_1',
            'locale' => 'en',
        ];

        $this->browser()
            ->post(
                '/nglayouts/app/api/layouts',
                ['json' => $data],
            )->assertJson()
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJsonMatches('message', 'There was an error validating "name": This value should not be blank.');
    }

    public function testCreateWithExistingName(): void
    {
        $data = [
            'layout_type' => 'test_layout_1',
            'name' => 'My layout',
            'locale' => 'en',
        ];

        $this->browser()
            ->post(
                '/nglayouts/app/api/layouts',
                ['json' => $data],
            )->assertJson()
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonMatches('message', 'Argument "name" has an invalid state. Layout with provided name already exists.');
    }

    public function testCreateWithInvalidDescription(): void
    {
        $data = [
            'layout_type' => 'test_layout_1',
            'name' => 'My name',
            'description' => 42,
            'locale' => 'en',
        ];

        $this->browser()
            ->post(
                '/nglayouts/app/api/layouts',
                ['json' => $data],
            )->assertJson()
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJsonMatches('message', 'There was an error validating "description": This value should be of type string.');
    }

    public function testCreateWithInvalidLocale(): void
    {
        $data = [
            'layout_type' => 'test_layout_1',
            'name' => 'My new layout',
            'description' => 'My new layout description',
            'locale' => 42,
        ];

        $this->browser()
            ->post(
                '/nglayouts/app/api/layouts',
                ['json' => $data],
            )->assertJson()
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJsonMatches('message', 'There was an error validating "locale": This value should be of type string.');
    }

    public function testCreateWithMissingLocale(): void
    {
        $data = [
            'layout_type' => 'test_layout_1',
            'name' => 'My new layout',
            'description' => 'My new layout description',
        ];

        $this->browser()
            ->post(
                '/nglayouts/app/api/layouts',
                ['json' => $data],
            )->assertJson()
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJsonMatches('message', 'There was an error validating "locale": This value should not be blank.');
    }

    public function testCreateWithNonExistentLocale(): void
    {
        $data = [
            'layout_type' => 'test_layout_1',
            'name' => 'My new layout',
            'description' => 'My new layout description',
            'locale' => 'unknown',
        ];

        $this->browser()
            ->post(
                '/nglayouts/app/api/layouts',
                ['json' => $data],
            )->assertJson()
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJsonMatches('message', 'There was an error validating "locale": This value is not a valid locale.');
    }
}
