<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\Layout;

use Netgen\Bundle\LayoutsAdminBundle\Controller\API\Layout\CreateDraft;
use Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\ApiTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\HttpFoundation\Response;

#[CoversClass(CreateDraft::class)]
final class CreateDraftTest extends ApiTestCase
{
    public function testCreateDraft(): void
    {
        $this->browser()
            ->post(
                '/nglayouts/app/api/layouts/81168ed3-86f9-55ea-b153-101f96f2c136/draft?html=false',
                ['json' => []],
            )->assertJson()
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJsonIs('layouts/create_layout_draft');
    }

    public function testCreateDraftWithNonExistentLayout(): void
    {
        $this->browser()
            ->post(
                '/nglayouts/app/api/layouts/ffffffff-ffff-ffff-ffff-ffffffffffff/draft',
                ['json' => []],
            )->assertJson()
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJsonMatches('message', 'Could not find layout with identifier "ffffffff-ffff-ffff-ffff-ffffffffffff"');
    }
}
