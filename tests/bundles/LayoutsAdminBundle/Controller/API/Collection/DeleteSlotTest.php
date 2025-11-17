<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\Collection;

use Netgen\Bundle\LayoutsAdminBundle\Controller\API\Collection\DeleteSlot;
use Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\ApiTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\HttpFoundation\Response;

#[CoversClass(DeleteSlot::class)]
final class DeleteSlotTest extends ApiTestCase
{
    public function testDeleteSlot(): void
    {
        $this->browser()
            ->delete('/nglayouts/app/api/collections/slots/de3a0641-c67f-48e0-96e7-7c83b6735265')
            ->assertStatus(Response::HTTP_NO_CONTENT);
    }

    public function testDeleteSlotWithNonExistentSlot(): void
    {
        $this->browser()
            ->delete('/nglayouts/app/api/collections/slots/ffffffff-ffff-ffff-ffff-ffffffffffff')
            ->assertJson()
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJsonMatches('message', 'Could not find slot with identifier "ffffffff-ffff-ffff-ffff-ffffffffffff"');
    }
}
