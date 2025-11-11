<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\Layout;

use Netgen\Bundle\LayoutsAdminBundle\Controller\API\Layout\LoadSharedLayouts;
use Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API\JsonApiTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[CoversClass(LoadSharedLayouts::class)]
final class LoadSharedLayoutsTest extends JsonApiTestCase
{
    public function testLoadSharedLayouts(): void
    {
        $this->client->request(Request::METHOD_GET, '/nglayouts/app/api/layouts/shared');

        $this->assertResponse(
            $this->client->getResponse(),
            'layouts/shared_layouts',
            Response::HTTP_OK,
        );
    }
}
