<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\Layout;

use Netgen\Bundle\LayoutsAdminBundle\Controller\API\Layout\RestoreFromArchive;
use Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\ApiTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\HttpFoundation\Response;

#[CoversClass(RestoreFromArchive::class)]
final class RestoreFromArchiveTest extends ApiTestCase
{
    public function testRestoreFromArchive(): void
    {
        $this->browser()
            ->post(
                '/nglayouts/app/api/layouts/71cbe281-430c-51d5-8e21-c3cc4e656dac/restore',
                ['json' => []],
            )->assertStatus(Response::HTTP_NO_CONTENT);
    }

    public function testRestoreFromArchiveWithNonExistentLayout(): void
    {
        $this->browser()
            ->post(
                '/nglayouts/app/api/layouts/ffffffff-ffff-ffff-ffff-ffffffffffff/restore',
                ['json' => []],
            )->assertJson()
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJsonMatches('message', 'Could not find layout with identifier "ffffffff-ffff-ffff-ffff-ffffffffffff"');
    }
}
