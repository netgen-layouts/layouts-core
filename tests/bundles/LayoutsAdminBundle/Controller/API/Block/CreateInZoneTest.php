<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\Block;

use Netgen\Bundle\LayoutsAdminBundle\Controller\API\Block\CreateInZone;
use Netgen\Bundle\LayoutsAdminBundle\Controller\API\Block\Utils\CreateStructBuilder;
use Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\ApiTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\HttpFoundation\Response;

#[CoversClass(CreateInZone::class)]
#[CoversClass(CreateStructBuilder::class)]
final class CreateInZoneTest extends ApiTestCase
{
    public function testCreateInZone(): void
    {
        $data = [
            'block_type' => 'list',
            'layout_id' => '81168ed3-86f9-55ea-b153-101f96f2c136',
            'zone_identifier' => 'bottom',
            'parent_position' => 0,
        ];

        $this->browser()
            ->post(
                '/nglayouts/app/api/en/blocks?html=false',
                ['json' => $data],
            )->assertJson()
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJsonIs('blocks/create_block_in_zone');
    }

    public function testCreateInZoneWithNoPosition(): void
    {
        $data = [
            'block_type' => 'list',
            'layout_id' => '81168ed3-86f9-55ea-b153-101f96f2c136',
            'zone_identifier' => 'right',
        ];

        $this->browser()
            ->post(
                '/nglayouts/app/api/en/blocks?html=false',
                ['json' => $data],
            )->assertJson()
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJsonIs('blocks/create_block_in_zone_at_end');
    }

    public function testCreateInZoneWithInvalidLayoutId(): void
    {
        $data = [
            'block_type' => 'title',
            'layout_id' => 42,
            'zone_identifier' => 'bottom',
            'parent_position' => 0,
        ];

        $this->browser()
            ->post(
                '/nglayouts/app/api/en/blocks',
                ['json' => $data],
            )->assertJson()
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJsonMatches('message', 'There was an error validating "layout_id": This value is not a valid UUID.');
    }

    public function testCreateInZoneWithMissingLayoutId(): void
    {
        $data = [
            'block_type' => 'title',
            'zone_identifier' => 'bottom',
            'parent_position' => 0,
        ];

        $this->browser()
            ->post(
                '/nglayouts/app/api/en/blocks',
                ['json' => $data],
            )->assertJson()
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJsonMatches('message', 'There was an error validating "layout_id": This value should not be blank.');
    }

    public function testCreateInZoneWithNonExistentBlockType(): void
    {
        $data = [
            'block_type' => 'unknown',
            'layout_id' => '81168ed3-86f9-55ea-b153-101f96f2c136',
            'zone_identifier' => 'bottom',
            'parent_position' => 0,
        ];

        $this->browser()
            ->post(
                '/nglayouts/app/api/en/blocks',
                ['json' => $data],
            )->assertJson()
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonMatches('message', 'Argument "block_type" has an invalid state. Block type does not exist.');
    }

    public function testCreateInZoneWithNonExistentLayout(): void
    {
        $data = [
            'block_type' => 'title',
            // This is a random UUID.
            'layout_id' => '7418fe82-a082-48ec-b156-03904819c8eb',
            'zone_identifier' => 'bottom',
            'parent_position' => 0,
        ];

        $this->browser()
            ->post(
                '/nglayouts/app/api/en/blocks',
                ['json' => $data],
            )->assertJson()
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJsonMatches('message', 'Could not find layout with identifier "7418fe82-a082-48ec-b156-03904819c8eb"');
    }

    public function testCreateInZoneWithNonExistentLayoutZone(): void
    {
        $data = [
            'block_type' => 'title',
            'layout_id' => '81168ed3-86f9-55ea-b153-101f96f2c136',
            'zone_identifier' => 'unknown',
            'parent_position' => 0,
        ];

        $this->browser()
            ->post(
                '/nglayouts/app/api/en/blocks',
                ['json' => $data],
            )->assertJson()
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJsonMatches('message', 'Could not find zone with identifier "unknown"');
    }

    public function testCreateInZoneWithOutOfRangePosition(): void
    {
        $data = [
            'block_type' => 'title',
            'layout_id' => '81168ed3-86f9-55ea-b153-101f96f2c136',
            'zone_identifier' => 'bottom',
            'parent_position' => 9999,
        ];

        $this->browser()
            ->post(
                '/nglayouts/app/api/en/blocks',
                ['json' => $data],
            )->assertJson()
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonMatches('message', 'Argument "position" has an invalid state. Position is out of range.');
    }

    public function testCreateInZoneWithNotAllowedBlockDefinition(): void
    {
        $data = [
            'block_type' => 'list',
            'layout_id' => '81168ed3-86f9-55ea-b153-101f96f2c136',
            'zone_identifier' => 'top',
            'parent_position' => 0,
        ];

        $this->browser()
            ->post(
                '/nglayouts/app/api/en/blocks',
                ['json' => $data],
            )->assertJson()
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonMatches('message', 'Argument "zone" has an invalid state. Block is not allowed in specified zone.');
    }
}
