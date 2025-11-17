<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\Layout;

use Netgen\Bundle\LayoutsAdminBundle\Controller\API\Layout\DiscardDraft;
use Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\ApiTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\HttpFoundation\Response;

#[CoversClass(DiscardDraft::class)]
final class DiscardDraftTest extends ApiTestCase
{
    public function testDiscardDraft(): void
    {
        $this->browser()
            ->delete('/nglayouts/app/api/layouts/81168ed3-86f9-55ea-b153-101f96f2c136/draft')
            ->assertStatus(Response::HTTP_NO_CONTENT);
    }

    public function testDiscardDraftWithNonExistentLayout(): void
    {
        $this->browser()
            ->delete('/nglayouts/app/api/layouts/ffffffff-ffff-ffff-ffff-ffffffffffff/draft')
            ->assertJson()
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJsonMatches('message', 'Could not find layout with identifier "ffffffff-ffff-ffff-ffff-ffffffffffff"');
    }
}
