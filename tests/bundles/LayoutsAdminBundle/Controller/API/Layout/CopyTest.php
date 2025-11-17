<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\Layout;

use Netgen\Bundle\LayoutsAdminBundle\Controller\API\Layout\Copy;
use Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\ApiTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\HttpFoundation\Response;

#[CoversClass(Copy::class)]
final class CopyTest extends ApiTestCase
{
    public function testCopy(): void
    {
        $data = [
            'name' => 'My new layout name',
            'description' => 'My new layout description',
        ];

        $this->browser()
            ->post(
                '/nglayouts/app/api/layouts/81168ed3-86f9-55ea-b153-101f96f2c136/copy?html=false',
                ['json' => $data],
            )->assertJson()
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJsonIs('layouts/copy_layout');
    }

    public function testCopyInPublishedState(): void
    {
        $data = [
            'name' => 'My new layout name',
            'description' => 'My new layout description',
        ];

        $this->browser()
            ->post(
                '/nglayouts/app/api/layouts/7900306c-0351-5f0a-9b33-5d4f5a1f3943/copy?published=true&html=false',
                ['json' => $data],
            )->assertJson()
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJsonIs('layouts/copy_published_layout');
    }

    public function testCopyWithNonExistingDescription(): void
    {
        $data = [
            'name' => 'My new layout name',
        ];

        $this->browser()
            ->post(
                '/nglayouts/app/api/layouts/81168ed3-86f9-55ea-b153-101f96f2c136/copy?html=false',
                ['json' => $data],
            )->assertJson()
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJsonIs('layouts/copy_layout_without_description');
    }

    public function testCopyWithEmptyDescription(): void
    {
        $data = [
            'name' => 'My new layout name',
            'description' => '',
        ];

        $this->browser()
            ->post(
                '/nglayouts/app/api/layouts/81168ed3-86f9-55ea-b153-101f96f2c136/copy?html=false',
                ['json' => $data],
            )->assertJson()
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJsonIs('layouts/copy_layout_empty_description');
    }

    public function testCopyWithNonExistingLayout(): void
    {
        $data = [
            'name' => 'My new layout name',
        ];

        $this->browser()
            ->post(
                '/nglayouts/app/api/layouts/ffffffff-ffff-ffff-ffff-ffffffffffff/copy',
                ['json' => $data],
            )->assertJson()
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJsonMatches('message', 'Could not find layout with identifier "ffffffff-ffff-ffff-ffff-ffffffffffff"');
    }

    public function testCopyWithInvalidName(): void
    {
        $data = [
            'name' => 42,
        ];

        $this->browser()
            ->post(
                '/nglayouts/app/api/layouts/81168ed3-86f9-55ea-b153-101f96f2c136/copy',
                ['json' => $data],
            )->assertJson()
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJsonMatches('message', 'There was an error validating "name": This value should be of type string.');
    }

    public function testCopyWithMissingName(): void
    {
        $this->browser()
            ->post(
                '/nglayouts/app/api/layouts/81168ed3-86f9-55ea-b153-101f96f2c136/copy',
                ['json' => []],
            )->assertJson()
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJsonMatches('message', 'There was an error validating "name": This value should not be blank.');
    }

    public function testCopyWithExistingName(): void
    {
        $data = [
            'name' => 'My other layout',
        ];

        $this->browser()
            ->post(
                '/nglayouts/app/api/layouts/81168ed3-86f9-55ea-b153-101f96f2c136/copy',
                ['json' => $data],
            )->assertJson()
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonMatches('message', 'Argument "layoutCopyStruct" has an invalid state. Layout with provided name already exists.');
    }

    public function testCopyWithInvalidDescription(): void
    {
        $data = [
            'name' => 'New name',
            'description' => 42,
        ];

        $this->browser()
            ->post(
                '/nglayouts/app/api/layouts/81168ed3-86f9-55ea-b153-101f96f2c136/copy',
                ['json' => $data],
            )->assertJson()
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJsonMatches('message', 'There was an error validating "description": This value should be of type string.');
    }
}
