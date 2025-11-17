<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\BlockCollection;

use Netgen\Bundle\LayoutsAdminBundle\Controller\API\BlockCollection\AddItems;
use Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\ApiTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\HttpFoundation\Response;

#[CoversClass(AddItems::class)]
final class AddItemsTest extends ApiTestCase
{
    public function testAddItems(): void
    {
        $data = [
            'items' => [
                [
                    'value' => 73,
                    'value_type' => 'my_value_type',
                    'position' => 3,
                ],
                [
                    'value' => 74,
                    'value_type' => 'my_value_type',
                ],
            ],
        ];

        $this->browser()
            ->post(
                '/nglayouts/app/api/en/blocks/28df256a-2467-5527-b398-9269ccc652de/collections/default/items',
                ['json' => $data],
            )->assertStatus(Response::HTTP_NO_CONTENT);
    }

    public function testAddItemsWithNonExistentBlock(): void
    {
        $data = [
            'items' => [
                [
                    'value' => 73,
                    'value_type' => 'my_value_type',
                    'position' => 3,
                ],
                [
                    'value' => 74,
                    'value_type' => 'my_value_type',
                ],
            ],
        ];

        $this->browser()
            ->post(
                '/nglayouts/app/api/en/blocks/ffffffff-ffff-ffff-ffff-ffffffffffff/collections/default/items',
                ['json' => $data],
            )->assertJson()
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJsonMatches('message', 'Could not find block with identifier "ffffffff-ffff-ffff-ffff-ffffffffffff"');
    }

    public function testAddItemsWithNonExistentCollection(): void
    {
        $data = [
            'items' => [
                [
                    'value' => 73,
                    'value_type' => 'my_value_type',
                    'position' => 3,
                ],
                [
                    'value' => 74,
                    'value_type' => 'my_value_type',
                ],
            ],
        ];

        $this->browser()
            ->post(
                '/nglayouts/app/api/en/blocks/28df256a-2467-5527-b398-9269ccc652de/collections/unknown/items',
                ['json' => $data],
            )->assertJson()
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJsonMatches('message', 'Collection with "unknown" identifier does not exist in the block.');
    }

    public function testAddItemsWithEmptyItems(): void
    {
        $data = [
            'items' => [],
        ];

        $this->browser()
            ->post(
                '/nglayouts/app/api/en/blocks/28df256a-2467-5527-b398-9269ccc652de/collections/default/items',
                ['json' => $data],
            )->assertJson()
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJsonMatches('message', 'There was an error validating "items": This value should not be blank.');
    }

    public function testAddItemsWithInvalidItems(): void
    {
        $data = [
            'items' => 42,
        ];

        $this->browser()
            ->post(
                '/nglayouts/app/api/en/blocks/28df256a-2467-5527-b398-9269ccc652de/collections/default/items',
                ['json' => $data],
            )->assertJson()
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJsonMatches('message', 'There was an error validating "items": This value should be of type list.');
    }

    public function testAddItemsWithMissingItems(): void
    {
        $this->browser()
            ->post(
                '/nglayouts/app/api/en/blocks/28df256a-2467-5527-b398-9269ccc652de/collections/default/items',
                ['json' => []],
            )->assertJson()
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJsonMatches('message', 'There was an error validating "items": This value should not be blank.');
    }

    public function testAddItemsWithInvalidValue(): void
    {
        $data = [
            'items' => [
                [
                    'value' => [42],
                    'value_type' => 'my_value_type',
                    'position' => 3,
                ],
            ],
        ];

        $this->browser()
            ->post(
                '/nglayouts/app/api/en/blocks/28df256a-2467-5527-b398-9269ccc652de/collections/default/items',
                ['json' => $data],
            )->assertJson()
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJsonMatches('message', 'There was an error validating "items[0][value]": This value should be of type int|string.');
    }

    public function testAddItemsWithMissingValue(): void
    {
        $data = [
            'items' => [
                [
                    'value_type' => 'my_value_type',
                    'position' => 3,
                ],
            ],
        ];

        $this->browser()
            ->post(
                '/nglayouts/app/api/en/blocks/28df256a-2467-5527-b398-9269ccc652de/collections/default/items',
                ['json' => $data],
            )->assertJson()
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJsonMatches('message', 'There was an error validating "items[0][value]": This field is missing.');
    }

    public function testAddItemsWithInvalidValueType(): void
    {
        $data = [
            'items' => [
                [
                    'value' => 73,
                    'value_type' => 42,
                    'position' => 3,
                ],
            ],
        ];

        $this->browser()
            ->post(
                '/nglayouts/app/api/en/blocks/28df256a-2467-5527-b398-9269ccc652de/collections/default/items',
                ['json' => $data],
            )->assertJson()
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJsonMatches('message', 'There was an error validating "items[0][value_type]": This value should be of type string.');
    }

    public function testAddItemsWithMissingValueType(): void
    {
        $data = [
            'items' => [
                [
                    'value' => 73,
                    'position' => 3,
                ],
            ],
        ];

        $this->browser()
            ->post(
                '/nglayouts/app/api/en/blocks/28df256a-2467-5527-b398-9269ccc652de/collections/default/items',
                ['json' => $data],
            )->assertJson()
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJsonMatches('message', 'There was an error validating "items[0][value_type]": This field is missing.');
    }

    public function testAddItemsWithInvalidPosition(): void
    {
        $data = [
            'items' => [
                [
                    'value' => 73,
                    'value_type' => 'my_value_type',
                    'position' => '3',
                ],
            ],
        ];

        $this->browser()
            ->post(
                '/nglayouts/app/api/en/blocks/28df256a-2467-5527-b398-9269ccc652de/collections/default/items',
                ['json' => $data],
            )->assertJson()
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJsonMatches('message', 'There was an error validating "items[0][position]": This value should be of type int.');
    }

    public function testAddItemsWithMissingPosition(): void
    {
        $data = [
            'items' => [
                [
                    'value' => 73,
                    'value_type' => 'my_value_type',
                ],
            ],
        ];

        $this->browser()
            ->post(
                '/nglayouts/app/api/en/blocks/28df256a-2467-5527-b398-9269ccc652de/collections/featured/items',
                ['json' => $data],
            )->assertJson()
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJsonMatches('message', 'There was an error validating "position": This value should not be blank.');
    }

    public function testAddItemsWithOutOfRangePosition(): void
    {
        $data = [
            'items' => [
                [
                    'value' => 73,
                    'value_type' => 'my_value_type',
                    'position' => 9999,
                ],
            ],
        ];

        $this->browser()
            ->post(
                '/nglayouts/app/api/en/blocks/28df256a-2467-5527-b398-9269ccc652de/collections/default/items',
                ['json' => $data],
            )->assertJson()
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonMatches('message', 'Argument "position" has an invalid state. Position is out of range.');
    }
}
