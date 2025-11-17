<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\Layout;

use Netgen\Bundle\LayoutsAdminBundle\Controller\API\Layout\ChangeType;
use Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\ApiTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\HttpFoundation\Response;

#[CoversClass(ChangeType::class)]
final class ChangeTypeTest extends ApiTestCase
{
    public function testChangeType(): void
    {
        $data = [
            'new_type' => '4_zones_b',
            'zone_mappings' => [
                'left' => ['left'],
                'right' => ['right'],
            ],
        ];

        $this->browser()
            ->post(
                '/nglayouts/app/api/layouts/81168ed3-86f9-55ea-b153-101f96f2c136/change_type?html=false',
                ['json' => $data],
            )->assertJson()
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonIs('layouts/change_type');
    }

    public function testChangeTypeWithInvalidNewType(): void
    {
        $data = [
            'new_type' => 42,
            'zone_mappings' => [
                'left' => ['left'],
                'right' => ['right'],
            ],
        ];

        $this->browser()
            ->post(
                '/nglayouts/app/api/layouts/81168ed3-86f9-55ea-b153-101f96f2c136/change_type?html=false',
                ['json' => $data],
            )->assertJson()
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJsonMatches('message', 'There was an error validating "new_type": This value should be of type string.');
    }

    public function testChangeTypeWithMissingNewType(): void
    {
        $data = [
            'zone_mappings' => [
                'left' => ['left'],
                'right' => ['right'],
            ],
        ];

        $this->browser()
            ->post(
                '/nglayouts/app/api/layouts/81168ed3-86f9-55ea-b153-101f96f2c136/change_type?html=false',
                ['json' => $data],
            )->assertJson()
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJsonMatches('message', 'There was an error validating "new_type": This value should not be blank.');
    }

    public function testChangeTypeWithInvalidMappings(): void
    {
        $data = [
            'new_type' => '4_zones_b',
            'zone_mappings' => 42,
        ];

        $this->browser()
            ->post(
                '/nglayouts/app/api/layouts/81168ed3-86f9-55ea-b153-101f96f2c136/change_type?html=false',
                ['json' => $data],
            )->assertJson()
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJsonMatches('message', 'There was an error validating "zone_mappings": This value should be of type associative_array.');
    }

    public function testChangeTypeWithNoMappings(): void
    {
        $data = [
            'new_type' => '4_zones_b',
        ];

        $this->browser()
            ->post(
                '/nglayouts/app/api/layouts/81168ed3-86f9-55ea-b153-101f96f2c136/change_type?html=false',
                ['json' => $data],
            )->assertJson()
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonIs('layouts/change_type_without_mappings');
    }
}
