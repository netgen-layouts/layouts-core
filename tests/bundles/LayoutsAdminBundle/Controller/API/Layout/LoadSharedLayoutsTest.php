<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\Layout;

use Netgen\Bundle\LayoutsAdminBundle\Controller\API\Layout\LoadSharedLayouts;
use Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\ApiTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\HttpFoundation\Response;

#[CoversClass(LoadSharedLayouts::class)]
final class LoadSharedLayoutsTest extends ApiTestCase
{
    public function testLoadSharedLayouts(): void
    {
        $this->browser()
            ->get('/nglayouts/app/api/layouts/shared')
            ->assertJson()
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonIs('layouts/shared_layouts');
    }
}
