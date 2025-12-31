<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\Templating\Twig\Runtime\CollectionPager;

use Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\CollectionPager\RouteGenerator;
use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\API\Values\Status;
use Netgen\Layouts\Context\Context;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\UriSigner;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Uid\Uuid;

#[CoversClass(RouteGenerator::class)]
final class RouteGeneratorTest extends TestCase
{
    private Context $context;

    private Stub&UriSigner $uriSignerStub;

    private Stub&UrlGeneratorInterface $urlGeneratorStub;

    private RouteGenerator $routeGenerator;

    private RequestStack $requestStack;

    protected function setUp(): void
    {
        $this->context = new Context();
        $this->uriSignerStub = self::createStub(UriSigner::class);
        $this->urlGeneratorStub = self::createStub(UrlGeneratorInterface::class);
        $this->requestStack = new RequestStack();

        $this->routeGenerator = new RouteGenerator(
            $this->context,
            $this->uriSignerStub,
            $this->urlGeneratorStub,
            $this->requestStack,
        );
    }

    #[DataProvider('invokeDataProvider')]
    public function testInvoke(int $page, string $generatedUri, string $finalUri): void
    {
        $block = Block::fromArray(
            [
                'id' => Uuid::v7(),
                'locale' => 'en',
                'status' => Status::Published,
            ],
        );

        $this->context->set('var', 'value');

        $request = Request::create('/');
        $request->query->set('foo', 'bar');
        $request->query->set('baz', 'bat');

        $this->requestStack->push($request);

        $this->urlGeneratorStub
            ->method('generate')
            ->with(
                self::identicalTo('nglayouts_ajax_block'),
                self::identicalTo(
                    [
                        'blockId' => $block->id->toString(),
                        'locale' => 'en',
                        'collectionIdentifier' => 'default',
                        'nglContext' => ['var' => 'value'],
                        'foo' => 'bar',
                        'baz' => 'bat',
                    ],
                ),
            )
            ->willReturn($generatedUri);

        $this->uriSignerStub
            ->method('sign')
            ->with(self::identicalTo('?nglContext%5Bvar%5D=value'))
            ->willReturn('?nglContext%5Bvar%5D=value&_hash=signature');

        $url = ($this->routeGenerator)($block, 'default', $page);

        self::assertSame($finalUri, $url);
    }

    #[DataProvider('invokeDataProvider')]
    public function testInvokeWithoutRequest(int $page, string $generatedUri, string $finalUri): void
    {
        $block = Block::fromArray(
            [
                'id' => Uuid::v7(),
                'locale' => 'en',
                'status' => Status::Published,
            ],
        );

        $this->context->set('var', 'value');

        $this->urlGeneratorStub
            ->method('generate')
            ->with(
                self::identicalTo('nglayouts_ajax_block'),
                self::identicalTo(
                    [
                        'blockId' => $block->id->toString(),
                        'locale' => 'en',
                        'collectionIdentifier' => 'default',
                        'nglContext' => ['var' => 'value'],
                    ],
                ),
            )
            ->willReturn($generatedUri);

        $this->uriSignerStub
            ->method('sign')
            ->with(self::identicalTo('?nglContext%5Bvar%5D=value'))
            ->willReturn('?nglContext%5Bvar%5D=value&_hash=signature');

        $url = ($this->routeGenerator)($block, 'default', $page);

        self::assertSame($finalUri, $url);
    }

    /**
     * @return iterable<mixed>
     */
    public static function invokeDataProvider(): iterable
    {
        return [
            [-5, '/generated/uri', '/generated/uri?_hash=signature'],
            [-1, '/generated/uri', '/generated/uri?_hash=signature'],
            [0, '/generated/uri', '/generated/uri?_hash=signature'],
            [1, '/generated/uri', '/generated/uri?_hash=signature'],
            [2, '/generated/uri', '/generated/uri?_hash=signature&page=2'],
            [5, '/generated/uri', '/generated/uri?_hash=signature&page=5'],
            [-5, '/generated/uri?foo', '/generated/uri?foo&_hash=signature'],
            [-1, '/generated/uri?foo', '/generated/uri?foo&_hash=signature'],
            [0, '/generated/uri?foo', '/generated/uri?foo&_hash=signature'],
            [1, '/generated/uri?foo', '/generated/uri?foo&_hash=signature'],
            [2, '/generated/uri?foo', '/generated/uri?foo&_hash=signature&page=2'],
            [5, '/generated/uri?foo', '/generated/uri?foo&_hash=signature&page=5'],
        ];
    }
}
