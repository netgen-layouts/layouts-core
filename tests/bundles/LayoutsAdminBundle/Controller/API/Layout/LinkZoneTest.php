<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\Layout;

use Netgen\Bundle\LayoutsAdminBundle\Controller\API\Layout\LinkZone;
use Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\ApiTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\HttpFoundation\Response;

#[CoversClass(LinkZone::class)]
final class LinkZoneTest extends ApiTestCase
{
    public function testLinkZone(): void
    {
        $data = [
            'linked_layout_id' => '399ad9ac-777a-50ba-945a-06e9f57add12',
            'linked_zone_identifier' => 'right',
        ];

        $this->browser()
            ->post(
                '/nglayouts/app/api/layouts/81168ed3-86f9-55ea-b153-101f96f2c136/zones/right/link',
                ['json' => $data],
            )->assertStatus(Response::HTTP_NO_CONTENT);
    }

    public function testLinkZoneWithNonExistentZone(): void
    {
        $this->browser()
            ->post(
                '/nglayouts/app/api/layouts/81168ed3-86f9-55ea-b153-101f96f2c136/zones/unknown/link',
                ['json' => []],
            )->assertJson()
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJsonMatches('message', 'Could not find zone with identifier "unknown"');
    }

    public function testLinkZoneWithNonExistentLayout(): void
    {
        $this->browser()
            ->post(
                '/nglayouts/app/api/layouts/ffffffff-ffff-ffff-ffff-ffffffffffff/zones/right/link',
                ['json' => []],
            )->assertJson()
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJsonMatches('message', 'Could not find layout with identifier "ffffffff-ffff-ffff-ffff-ffffffffffff"');
    }

    public function testLinkZoneWithMissingLinkedLayoutId(): void
    {
        $data = [
            'linked_zone_identifier' => 'right',
        ];

        $this->browser()
            ->post(
                '/nglayouts/app/api/layouts/81168ed3-86f9-55ea-b153-101f96f2c136/zones/right/link',
                ['json' => $data],
            )->assertJson()
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJsonMatches('message', 'There was an error validating "linked_layout_id": This value should not be blank.');
    }

    public function testLinkZoneWithInvalidLinkedLayoutId(): void
    {
        $data = [
            'linked_layout_id' => 42,
            'linked_zone_identifier' => 'right',
        ];

        $this->browser()
            ->post(
                '/nglayouts/app/api/layouts/81168ed3-86f9-55ea-b153-101f96f2c136/zones/right/link',
                ['json' => $data],
            )->assertJson()
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJsonMatches('message', 'There was an error validating "linked_layout_id": This value is not a valid UUID.');
    }

    public function testLinkZoneWithNonExistentLinkedZone(): void
    {
        $data = [
            'linked_layout_id' => '399ad9ac-777a-50ba-945a-06e9f57add12',
            'linked_zone_identifier' => 'unknown',
        ];

        $this->browser()
            ->post(
                '/nglayouts/app/api/layouts/81168ed3-86f9-55ea-b153-101f96f2c136/zones/right/link',
                ['json' => $data],
            )->assertJson()
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJsonMatches('message', 'Could not find zone with identifier "unknown"');
    }

    public function testLinkZoneWithNonExistentLinkedLayout(): void
    {
        $data = [
            // This is a random UUID
            'linked_layout_id' => 'e1513a93-e707-493a-8e6b-5d0bfb7a0594',
            'linked_zone_identifier' => 'right',
        ];

        $this->browser()
            ->post(
                '/nglayouts/app/api/layouts/81168ed3-86f9-55ea-b153-101f96f2c136/zones/right/link',
                ['json' => $data],
            )->assertJson()
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJsonMatches('message', 'Could not find layout with identifier "e1513a93-e707-493a-8e6b-5d0bfb7a0594"');
    }

    public function testLinkZoneWithNonSharedLinkedLayout(): void
    {
        $data = [
            'linked_layout_id' => '71cbe281-430c-51d5-8e21-c3cc4e656dac',
            'linked_zone_identifier' => 'right',
        ];

        $this->browser()
            ->post(
                '/nglayouts/app/api/layouts/81168ed3-86f9-55ea-b153-101f96f2c136/zones/right/link',
                ['json' => $data],
            )->assertJson()
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonMatches('message', 'Argument "linkedZone" has an invalid state. Linked zone is not in the shared layout.');
    }
}
