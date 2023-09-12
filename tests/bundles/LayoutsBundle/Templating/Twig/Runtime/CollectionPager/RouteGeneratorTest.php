<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\Templating\Twig\Runtime\CollectionPager;

use Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\CollectionPager\RouteGenerator;
use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\API\Values\Value;
use Netgen\Layouts\Context\Context;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\UriSigner;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

use function call_user_func;

final class RouteGeneratorTest extends TestCase
{
    private Context $context;

    private MockObject $uriSignerMock;

    private MockObject $urlGeneratorMock;

    private RouteGenerator $routeGenerator;

    private RequestStack $requestStack;

    protected function setUp(): void
    {
        $this->context = new Context();
        $this->uriSignerMock = $this->createMock(UriSigner::class);
        $this->urlGeneratorMock = $this->createMock(UrlGeneratorInterface::class);
        $this->requestStack = new RequestStack();

        $this->routeGenerator = new RouteGenerator(
            $this->context,
            $this->uriSignerMock,
            $this->urlGeneratorMock,
            $this->requestStack,
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\CollectionPager\RouteGenerator::__construct
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\CollectionPager\RouteGenerator::__invoke
     *
     * @dataProvider invokeDataProvider
     */
    public function testInvoke(int $page, string $generatedUri, string $finalUri): void
    {
        $block = Block::fromArray(
            [
                'id' => Uuid::uuid4(),
                'locale' => 'en',
                'status' => Value::STATUS_PUBLISHED,
            ],
        );

        $this->context->set('var', 'value');

        $request = Request::create('/');
        $request->query->set('foo', 'bar');
        $request->query->set('baz', 'bat');

        $this->requestStack->push($request);

        $this->urlGeneratorMock->expects(self::once())
            ->method('generate')
            ->with(
                self::identicalTo('nglayouts_ajax_block'),
                self::identicalTo(
                    [
                        'blockId' => $block->getId()->toString(),
                        'locale' => 'en',
                        'collectionIdentifier' => 'default',
                        'nglContext' => ['var' => 'value'],
                    ] + ['foo' => 'bar', 'baz' => 'bat'],
                ),
            )
            ->willReturn($generatedUri);

        $this->uriSignerMock->expects(self::once())
            ->method('sign')
            ->with(self::identicalTo('?nglContext%5Bvar%5D=value'))
            ->willReturn('?nglContext%5Bvar%5D=value&_hash=signature');

        $url = call_user_func($this->routeGenerator, $block, 'default', $page);

        self::assertSame($finalUri, $url);
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\CollectionPager\RouteGenerator::__construct
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\CollectionPager\RouteGenerator::__invoke
     *
     * @dataProvider invokeDataProvider
     */
    public function testInvokeWithoutRequest(int $page, string $generatedUri, string $finalUri): void
    {
        $block = Block::fromArray(
            [
                'id' => Uuid::uuid4(),
                'locale' => 'en',
                'status' => Value::STATUS_PUBLISHED,
            ],
        );

        $this->context->set('var', 'value');

        $this->urlGeneratorMock->expects(self::once())
            ->method('generate')
            ->with(
                self::identicalTo('nglayouts_ajax_block'),
                self::identicalTo(
                    [
                        'blockId' => $block->getId()->toString(),
                        'locale' => 'en',
                        'collectionIdentifier' => 'default',
                        'nglContext' => ['var' => 'value'],
                    ],
                ),
            )
            ->willReturn($generatedUri);

        $this->uriSignerMock->expects(self::once())
            ->method('sign')
            ->with(self::identicalTo('?nglContext%5Bvar%5D=value'))
            ->willReturn('?nglContext%5Bvar%5D=value&_hash=signature');

        $url = call_user_func($this->routeGenerator, $block, 'default', $page);

        self::assertSame($finalUri, $url);
    }

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
