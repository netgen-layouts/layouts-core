<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API;

use Netgen\Layouts\Collection\Registry\QueryTypeRegistry;
use Netgen\Layouts\Tests\App\Item\Value;
use Netgen\Layouts\Tests\Collection\Stubs\QueryType;
use Netgen\Layouts\Tests\Persistence\Doctrine\DatabaseTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Security\Core\User\InMemoryUser;
use Zenstruck\Browser\Test\HasBrowser;

use function count;

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
    protected function browser(array $options = [], array $server = []): KernelBrowser
    {
        return $this->baseBrowser($options, $server)
            ->actingAs(new InMemoryUser('admin', 'admin', ['ROLE_NGLAYOUTS_ADMIN']))
            ->use(
                function (): void {
                    $this->mockQueryType(static::getContainer());
                    $this->createDatabase();
                },
            );
    }

    protected function mockQueryType(Container $container): void
    {
        $searchResults = [new Value(140), new Value(79), new Value(78)];

        /** @var \Netgen\Layouts\Collection\Registry\QueryTypeRegistry $queryTypeRegistry */
        $queryTypeRegistry = $container->get('netgen_layouts.collection.registry.query_type.original');

        $queryType = new QueryType('my_query_type', $searchResults, count($searchResults));
        $allQueryTypes = $queryTypeRegistry->getQueryTypes();
        $allQueryTypes['my_query_type'] = $queryType;

        $container->set(
            'netgen_layouts.collection.registry.query_type',
            new QueryTypeRegistry($allQueryTypes),
        );
    }
}
