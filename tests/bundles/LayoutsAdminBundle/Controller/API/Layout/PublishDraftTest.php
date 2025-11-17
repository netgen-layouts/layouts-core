<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\Layout;

use Netgen\Bundle\LayoutsAdminBundle\Controller\API\Layout\PublishDraft;
use Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\ApiTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\HttpFoundation\Response;

#[CoversClass(PublishDraft::class)]
final class PublishDraftTest extends ApiTestCase
{
    public function testPublishDraft(): void
    {
        $this->browser()
            ->post(
                '/nglayouts/app/api/layouts/81168ed3-86f9-55ea-b153-101f96f2c136/publish',
                ['json' => []],
            )->assertStatus(Response::HTTP_NO_CONTENT);
    }

    public function testPublishDraftWithNonExistentLayout(): void
    {
        $this->browser()
            ->post(
                '/nglayouts/app/api/layouts/ffffffff-ffff-ffff-ffff-ffffffffffff/publish',
                ['json' => []],
            )->assertJson()
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJsonMatches('message', 'Could not find layout with identifier "ffffffff-ffff-ffff-ffff-ffffffffffff"');
    }
}
