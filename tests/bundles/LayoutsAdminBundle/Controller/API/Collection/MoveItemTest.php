<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\Collection;

use Netgen\Bundle\LayoutsAdminBundle\Controller\API\Collection\MoveItem;
use Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\ApiTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\HttpFoundation\Response;

#[CoversClass(MoveItem::class)]
final class MoveItemTest extends ApiTestCase
{
    public function testMoveItem(): void
    {
        $data = [
            'position' => 2,
        ];

        $this->browser()
            ->post(
                '/nglayouts/app/api/collections/items/8ae55a69-8633-51dd-9ff5-d820d040c1c1/move',
                ['json' => $data],
            )->assertStatus(Response::HTTP_NO_CONTENT);
    }

    public function testMoveItemWithNonExistentItem(): void
    {
        $this->browser()
            ->post(
                '/nglayouts/app/api/collections/items/ffffffff-ffff-ffff-ffff-ffffffffffff/move',
                ['json' => []],
            )->assertJson()
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJsonMatches('message', 'Could not find item with identifier "ffffffff-ffff-ffff-ffff-ffffffffffff"');
    }

    public function testMoveItemWithInvalidPosition(): void
    {
        $data = [
            'position' => '0',
        ];

        $this->browser()
            ->post(
                '/nglayouts/app/api/collections/items/8ae55a69-8633-51dd-9ff5-d820d040c1c1/move',
                ['json' => $data],
            )->assertJson()
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJsonMatches('message', 'There was an error validating "position": This value should be of type int.');
    }

    public function testMoveItemWithMissingPosition(): void
    {
        $this->browser()
            ->post(
                '/nglayouts/app/api/collections/items/8ae55a69-8633-51dd-9ff5-d820d040c1c1/move',
                ['json' => []],
            )->assertJson()
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJsonMatches('message', 'There was an error validating "position": This value should not be blank.');
    }

    public function testMoveItemWithNegativePosition(): void
    {
        $data = [
            'position' => -2,
        ];

        $this->browser()
            ->post(
                '/nglayouts/app/api/collections/items/8ae55a69-8633-51dd-9ff5-d820d040c1c1/move',
                ['json' => $data],
            )->assertJson()
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJsonMatches('message', 'There was an error validating "position": This value should be either positive or zero.');
    }

    public function testMoveItemWithOutOfRangePosition(): void
    {
        $data = [
            'position' => 9999,
        ];

        $this->browser()
            ->post(
                '/nglayouts/app/api/collections/items/8ae55a69-8633-51dd-9ff5-d820d040c1c1/move',
                ['json' => $data],
            )->assertJson()
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonMatches('message', 'Argument "position" has an invalid state. Position is out of range.');
    }
}
