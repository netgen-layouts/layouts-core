<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\Block;

use Netgen\Bundle\LayoutsAdminBundle\Controller\API\Block\CopyToZone;
use Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\ApiTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\HttpFoundation\Response;

#[CoversClass(CopyToZone::class)]
final class CopyToZoneTest extends ApiTestCase
{
    public function testCopyToZone(): void
    {
        $data = [
            'layout_id' => '81168ed3-86f9-55ea-b153-101f96f2c136',
            'zone_identifier' => 'left',
        ];

        $this->browser()
            ->post(
                '/nglayouts/app/api/en/blocks/28df256a-2467-5527-b398-9269ccc652de/copy/zone?html=false',
                ['json' => $data],
            )->assertJson()
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJsonIs('blocks/copy_block_to_zone');
    }

    public function testCopyToZoneWithNonExistentBlock(): void
    {
        $this->browser()
            ->post(
                '/nglayouts/app/api/en/blocks/ffffffff-ffff-ffff-ffff-ffffffffffff/copy/zone',
                ['json' => []],
            )->assertJson()
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJsonMatches('message', 'Could not find block with identifier "ffffffff-ffff-ffff-ffff-ffffffffffff"');
    }

    public function testCopyToZoneWithNonExistentLayout(): void
    {
        $data = [
            // This is a random UUID.
            'layout_id' => 'd37383cc-fb37-46d5-9d3d-936970331dab',
            'zone_identifier' => 'left',
        ];

        $this->browser()
            ->post(
                '/nglayouts/app/api/en/blocks/28df256a-2467-5527-b398-9269ccc652de/copy/zone',
                ['json' => $data],
            )->assertJson()
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJsonMatches('message', 'Could not find layout with identifier "d37383cc-fb37-46d5-9d3d-936970331dab"');
    }

    public function testCopyToZoneWithNonExistentZone(): void
    {
        $data = [
            'layout_id' => '81168ed3-86f9-55ea-b153-101f96f2c136',
            'zone_identifier' => 'unknown',
        ];

        $this->browser()
            ->post(
                '/nglayouts/app/api/en/blocks/28df256a-2467-5527-b398-9269ccc652de/copy/zone',
                ['json' => $data],
            )->assertJson()
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJsonMatches('message', 'Could not find zone with identifier "unknown"');
    }

    public function testCopyToZoneWithNotAllowedBlockDefinition(): void
    {
        $data = [
            'layout_id' => '81168ed3-86f9-55ea-b153-101f96f2c136',
            'zone_identifier' => 'top',
        ];

        $this->browser()
            ->post(
                '/nglayouts/app/api/en/blocks/28df256a-2467-5527-b398-9269ccc652de/copy/zone',
                ['json' => $data],
            )->assertJson()
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonMatches('message', 'Argument "zone" has an invalid state. Block is not allowed in specified zone.');
    }

    public function testCopyToZoneWithInvalidLayoutId(): void
    {
        $data = [
            'layout_id' => 42,
            'zone_identifier' => 'left',
        ];

        $this->browser()
            ->post(
                '/nglayouts/app/api/en/blocks/28df256a-2467-5527-b398-9269ccc652de/copy/zone',
                ['json' => $data],
            )->assertJson()
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJsonMatches('message', 'There was an error validating "layout_id": This value is not a valid UUID.');
    }

    public function testCopyToZoneWithMissingLayoutId(): void
    {
        $data = [
            'zone_identifier' => 'left',
        ];

        $this->browser()
            ->post(
                '/nglayouts/app/api/en/blocks/28df256a-2467-5527-b398-9269ccc652de/copy/zone',
                ['json' => $data],
            )->assertJson()
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJsonMatches('message', 'There was an error validating "layout_id": This value should not be blank.');
    }
}
