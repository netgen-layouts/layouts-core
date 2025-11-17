<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\Block;

use Netgen\Bundle\LayoutsAdminBundle\Controller\API\Block\MoveToZone;
use Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\ApiTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\HttpFoundation\Response;

#[CoversClass(MoveToZone::class)]
final class MoveToZoneTest extends ApiTestCase
{
    public function testMoveToZone(): void
    {
        $data = [
            'layout_id' => '81168ed3-86f9-55ea-b153-101f96f2c136',
            'zone_identifier' => 'left',
            'parent_position' => 0,
        ];

        $this->browser()
            ->post(
                '/nglayouts/app/api/en/blocks/28df256a-2467-5527-b398-9269ccc652de/move/zone',
                ['json' => $data],
            )->assertStatus(Response::HTTP_NO_CONTENT);
    }

    public function testMoveToZoneWithNonExistentBlock(): void
    {
        $this->browser()
            ->post(
                '/nglayouts/app/api/en/blocks/ffffffff-ffff-ffff-ffff-ffffffffffff/move/zone',
                ['json' => []],
            )->assertJson()
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJsonMatches('message', 'Could not find block with identifier "ffffffff-ffff-ffff-ffff-ffffffffffff"');
    }

    public function testMoveToZoneWithNonExistentLayout(): void
    {
        $data = [
            // This is a random UUID.
            'layout_id' => 'd8edc29f-8dd9-4eec-ba72-77ccbeb34d2d',
            'zone_identifier' => 'left',
            'parent_position' => 1,
        ];

        $this->browser()
            ->post(
                '/nglayouts/app/api/en/blocks/28df256a-2467-5527-b398-9269ccc652de/move/zone',
                ['json' => $data],
            )->assertJson()
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJsonMatches('message', 'Could not find layout with identifier "d8edc29f-8dd9-4eec-ba72-77ccbeb34d2d"');
    }

    public function testMoveToZoneWithNonExistentZoneIdentifier(): void
    {
        $data = [
            'layout_id' => '81168ed3-86f9-55ea-b153-101f96f2c136',
            'zone_identifier' => 'unknown',
            'parent_position' => 1,
        ];

        $this->browser()
            ->post(
                '/nglayouts/app/api/en/blocks/28df256a-2467-5527-b398-9269ccc652de/move/zone',
                ['json' => $data],
            )->assertJson()
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJsonMatches('message', 'Could not find zone with identifier "unknown"');
    }

    public function testMoveToZoneWithNotAllowedBlockDefinition(): void
    {
        $data = [
            'layout_id' => '81168ed3-86f9-55ea-b153-101f96f2c136',
            'zone_identifier' => 'top',
            'parent_position' => 0,
        ];

        $this->browser()
            ->post(
                '/nglayouts/app/api/en/blocks/28df256a-2467-5527-b398-9269ccc652de/move/zone',
                ['json' => $data],
            )->assertJson()
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonMatches('message', 'Argument "zone" has an invalid state. Block is not allowed in specified zone.');
    }

    public function testMoveToZoneWithOutOfRangePosition(): void
    {
        $data = [
            'layout_id' => '81168ed3-86f9-55ea-b153-101f96f2c136',
            'zone_identifier' => 'left',
            'parent_position' => 9999,
        ];

        $this->browser()
            ->post(
                '/nglayouts/app/api/en/blocks/28df256a-2467-5527-b398-9269ccc652de/move/zone',
                ['json' => $data],
            )->assertJson()
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonMatches('message', 'Argument "position" has an invalid state. Position is out of range.');
    }

    public function testMoveToZoneWithInvalidLayoutId(): void
    {
        $data = [
            'layout_id' => 42,
            'zone_identifier' => 'left',
            'parent_position' => 1,
        ];

        $this->browser()
            ->post(
                '/nglayouts/app/api/en/blocks/28df256a-2467-5527-b398-9269ccc652de/move/zone',
                ['json' => $data],
            )->assertJson()
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJsonMatches('message', 'There was an error validating "layout_id": This value is not a valid UUID.');
    }

    public function testMoveToZoneWithMissingLayoutId(): void
    {
        $data = [
            'zone_identifier' => 'left',
            'parent_position' => 1,
        ];

        $this->browser()
            ->post(
                '/nglayouts/app/api/en/blocks/28df256a-2467-5527-b398-9269ccc652de/move/zone',
                ['json' => $data],
            )->assertJson()
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJsonMatches('message', 'There was an error validating "layout_id": This value should not be blank.');
    }
}
