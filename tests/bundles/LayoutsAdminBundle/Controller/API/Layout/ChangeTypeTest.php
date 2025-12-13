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
            'new_type' => 'test_layout_2',
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
            'new_type' => 'test_layout_2',
            'zone_mappings' => 42,
        ];

        $this->browser()
            ->post(
                '/nglayouts/app/api/layouts/81168ed3-86f9-55ea-b153-101f96f2c136/change_type?html=false',
                ['json' => $data],
            )->assertJson()
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJsonMatches('message', 'Unexpected value for parameter "zone_mappings": expecting "array", got "int".');
    }

    public function testChangeTypeWithNoMappings(): void
    {
        $data = [
            'new_type' => 'test_layout_2',
        ];

        $this->browser()
            ->post(
                '/nglayouts/app/api/layouts/81168ed3-86f9-55ea-b153-101f96f2c136/change_type?html=false',
                ['json' => $data],
            )->assertJson()
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonIs('layouts/change_type_without_mappings');
    }

    public function testChangeTypeWithEmptyMappings(): void
    {
        $data = [
            'new_type' => 'test_layout_2',
            'zone_mappings' => [],
        ];

        $this->browser()
            ->post(
                '/nglayouts/app/api/layouts/81168ed3-86f9-55ea-b153-101f96f2c136/change_type?html=false',
                ['json' => $data],
            )->assertJson()
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJsonMatches('message', 'There was an error validating "zone_mappings": This value should be of type associative_array.');
    }

    public function testChangeTypeWithListMappings(): void
    {
        $data = [
            'new_type' => 'test_layout_2',
            'zone_mappings' => [['left']],
        ];

        $this->browser()
            ->post(
                '/nglayouts/app/api/layouts/81168ed3-86f9-55ea-b153-101f96f2c136/change_type?html=false',
                ['json' => $data],
            )->assertJson()
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJsonMatches('message', 'There was an error validating "zone_mappings": This value should be of type associative_array.');
    }

    public function testChangeTypeWithEmptySingleMapping(): void
    {
        $data = [
            'new_type' => 'test_layout_2',
            'zone_mappings' => ['left' => []],
        ];

        $this->browser()
            ->post(
                '/nglayouts/app/api/layouts/81168ed3-86f9-55ea-b153-101f96f2c136/change_type?html=false',
                ['json' => $data],
            )->assertJson()
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJsonMatches('message', 'There was an error validating "zone_mappings[left]": This collection should contain 1 element or more.');
    }

    public function testChangeTypeWithInvalidSingleMapping(): void
    {
        $data = [
            'new_type' => 'test_layout_2',
            'zone_mappings' => ['left' => 42],
        ];

        $this->browser()
            ->post(
                '/nglayouts/app/api/layouts/81168ed3-86f9-55ea-b153-101f96f2c136/change_type?html=false',
                ['json' => $data],
            )->assertJson()
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJsonMatches('message', 'There was an error validating "zone_mappings[left]": This value should be of type list.');
    }

    public function testChangeTypeWithEmptySingleMappingItem(): void
    {
        $data = [
            'new_type' => 'test_layout_2',
            'zone_mappings' => ['left' => ['']],
        ];

        $this->browser()
            ->post(
                '/nglayouts/app/api/layouts/81168ed3-86f9-55ea-b153-101f96f2c136/change_type?html=false',
                ['json' => $data],
            )->assertJson()
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJsonMatches('message', 'There was an error validating "zone_mappings[left][0]": This value should not be blank.');
    }

    public function testChangeTypeWithInvalidSingleMappingItem(): void
    {
        $data = [
            'new_type' => 'test_layout_2',
            'zone_mappings' => ['left' => [42]],
        ];

        $this->browser()
            ->post(
                '/nglayouts/app/api/layouts/81168ed3-86f9-55ea-b153-101f96f2c136/change_type?html=false',
                ['json' => $data],
            )->assertJson()
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJsonMatches('message', 'There was an error validating "zone_mappings[left][0]": This value should be of type string.');
    }
}
