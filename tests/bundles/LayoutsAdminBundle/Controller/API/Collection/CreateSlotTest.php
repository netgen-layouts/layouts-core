<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\Collection;

use Netgen\Bundle\LayoutsAdminBundle\Controller\API\Collection\CreateSlot;
use Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\ApiTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\HttpFoundation\Response;

#[CoversClass(CreateSlot::class)]
final class CreateSlotTest extends ApiTestCase
{
    public function testCreateSlot(): void
    {
        $data = [
            'position' => 42,
        ];

        $this->browser()
            ->post(
                '/nglayouts/app/api/collections/a79dde13-1f5c-51a6-bea9-b766236be49e/slots',
                ['json' => $data],
            )->assertJson()
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJsonIs('collections/create_slot');
    }

    public function testCreateSlotWithNonExistentCollection(): void
    {
        $this->browser()
            ->post(
                '/nglayouts/app/api/collections/ffffffff-ffff-ffff-ffff-ffffffffffff/slots',
                ['json' => []],
            )->assertJson()
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJsonMatches('message', 'Could not find collection with identifier "ffffffff-ffff-ffff-ffff-ffffffffffff"');
    }

    public function testCreateSlotWithInvalidPosition(): void
    {
        $data = [
            'position' => '0',
        ];

        $this->browser()
            ->post(
                '/nglayouts/app/api/collections/a79dde13-1f5c-51a6-bea9-b766236be49e/slots',
                ['json' => $data],
            )->assertJson()
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJsonMatches('message', 'There was an error validating "position": This value should be of type int.');
    }

    public function testCreateSlotWithMissingPosition(): void
    {
        $this->browser()
            ->post(
                '/nglayouts/app/api/collections/a79dde13-1f5c-51a6-bea9-b766236be49e/slots',
                ['json' => []],
            )->assertJson()
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJsonMatches('message', 'There was an error validating "position": This value should not be blank.');
    }

    public function testCreateSlotWithNegativePosition(): void
    {
        $data = [
            'position' => -2,
        ];

        $this->browser()
            ->post(
                '/nglayouts/app/api/collections/a79dde13-1f5c-51a6-bea9-b766236be49e/slots',
                ['json' => $data],
            )->assertJson()
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJsonMatches('message', 'There was an error validating "position": This value should be either positive or zero.');
    }
}
