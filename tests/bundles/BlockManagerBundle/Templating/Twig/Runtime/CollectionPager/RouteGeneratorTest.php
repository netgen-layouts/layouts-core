<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Tests\Templating\Twig\Runtime\CollectionPager;

use Netgen\BlockManager\Context\ContextInterface;
use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\CollectionPager\RouteGenerator;
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
     * @var \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\CollectionPager\RouteGenerator
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
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\CollectionPager\RouteGenerator::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\CollectionPager\RouteGenerator::__invoke
     * @dataProvider invokeProvider
     */
    public function testInvoke(int $page, string $signedUri, string $signedUriSuffix): void
    {
        $block = new Block(
            [
                'id' => 42,
                'locale' => 'en',
            ]
        );

        $this->contextMock->expects($this->once())
            ->method('all')
            ->will($this->returnValue(['var' => 'value']));

        $this->urlGeneratorMock->expects($this->once())
            ->method('generate')
            ->with(
                $this->identicalTo('ngbm_ajax_block'),
                $this->identicalTo(
                    [
                        'blockId' => 42,
                        'locale' => 'en',
                        'collectionIdentifier' => 'default',
                        'ngbmContext' => ['var' => 'value'],
                    ]
                )
            )
            ->will($this->returnValue('/generated/uri'));

        $this->uriSignerMock->expects($this->once())
            ->method('sign')
            ->with($this->identicalTo('/generated/uri'))
            ->will($this->returnValue($signedUri));

        $routeGenerator = $this->routeGenerator;
        $url = $routeGenerator($block, 'default', $page);

        $this->assertSame($signedUri . $signedUriSuffix, $url);
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
