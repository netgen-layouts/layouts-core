<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\Block;

use Netgen\Bundle\LayoutsAdminBundle\Controller\API\Block\Delete;
use Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\ApiTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\HttpFoundation\Response;

#[CoversClass(Delete::class)]
final class DeleteTest extends ApiTestCase
{
    public function testDelete(): void
    {
        $this->browser()
            ->delete('/nglayouts/app/api/en/blocks/28df256a-2467-5527-b398-9269ccc652de')
            ->assertStatus(Response::HTTP_NO_CONTENT);
    }

    public function testDeleteWithNonExistentBlock(): void
    {
        $this->browser()
            ->delete('/nglayouts/app/api/en/blocks/ffffffff-ffff-ffff-ffff-ffffffffffff')
            ->assertJson()
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJsonMatches('message', 'Could not find block with identifier "ffffffff-ffff-ffff-ffff-ffffffffffff"');
    }
}
