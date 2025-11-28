<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API;

use Netgen\Layouts\Tests\Persistence\Doctrine\DatabaseTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Security\Core\User\InMemoryUser;
use Zenstruck\Browser\Test\HasBrowser;

abstract class ApiTestCase extends KernelTestCase
{
    use DatabaseTrait;
    use HasBrowser {
        browser as protected baseBrowser;
    }

    /**
     * @param array<string, mixed> $options
     * @param array<string, mixed> $server
     */
    final protected function browser(array $options = [], array $server = []): KernelBrowser
    {
        return $this->baseBrowser($options, $server)
            ->actingAs(new InMemoryUser('admin', 'admin', ['ROLE_NGLAYOUTS_ADMIN']))
            ->use(
                function (): void {
                    $this->createDatabase();
                },
            );
    }
}
