<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\Templating\Twig\Runtime\CollectionPager;

use Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\CollectionPager\RouteGenerator;
use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\Context\ContextInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\UriSigner;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class RouteGeneratorTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $contextMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $uriSignerMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $urlGeneratorMock;

    /**
     * @var \Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\CollectionPager\RouteGenerator
     */
    private $routeGenerator;

    public function setUp(): void
    {
        $this->contextMock = $this->createMock(ContextInterface::class);
        $this->uriSignerMock = $this->createMock(UriSigner::class);
        $this->urlGeneratorMock = $this->createMock(UrlGeneratorInterface::class);

        $this->routeGenerator = new RouteGenerator(
            $this->contextMock,
            $this->uriSignerMock,
            $this->urlGeneratorMock
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\CollectionPager\RouteGenerator::__construct
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\CollectionPager\RouteGenerator::__invoke
     * @dataProvider invokeProvider
     */
    public function testInvoke(int $page, string $signedUri, string $signedUriSuffix): void
    {
        $block = Block::fromArray(
            [
                'id' => 42,
                'locale' => 'en',
            ]
        );

        $this->contextMock->expects(self::once())
            ->method('all')
            ->willReturn(['var' => 'value']);

        $this->urlGeneratorMock->expects(self::once())
            ->method('generate')
            ->with(
                self::identicalTo('nglayouts_ajax_block'),
                self::identicalTo(
                    [
                        'blockId' => 42,
                        'locale' => 'en',
                        'collectionIdentifier' => 'default',
                        'ngbmContext' => ['var' => 'value'],
                    ]
                )
            )
            ->willReturn('/generated/uri');

        $this->uriSignerMock->expects(self::once())
            ->method('sign')
            ->with(self::identicalTo('/generated/uri'))
            ->willReturn($signedUri);

        $url = call_user_func($this->routeGenerator, $block, 'default', $page);

        self::assertSame($signedUri . $signedUriSuffix, $url);
    }

    public function invokeProvider(): array
    {
        return [
            [-5, '/signed/uri', ''],
            [-1, '/signed/uri', ''],
            [0, '/signed/uri', ''],
            [1, '/signed/uri', ''],
            [2, '/signed/uri', '?page=2'],
            [5, '/signed/uri', '?page=5'],
            [-5, '/signed/uri?foo', ''],
            [-1, '/signed/uri?foo', ''],
            [0, '/signed/uri?foo', ''],
            [1, '/signed/uri?foo', ''],
            [2, '/signed/uri?foo', '&page=2'],
            [5, '/signed/uri?foo', '&page=5'],
        ];
    }
}
