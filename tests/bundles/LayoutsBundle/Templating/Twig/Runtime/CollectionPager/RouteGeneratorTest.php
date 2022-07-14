<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\Templating\Twig\Runtime\CollectionPager;

use Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\CollectionPager\RouteGenerator;
use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\Context\Context;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpKernel\UriSigner;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

use function call_user_func;

final class RouteGeneratorTest extends TestCase
{
    private Context $context;

    private MockObject $uriSignerMock;

    private MockObject $urlGeneratorMock;

    private RouteGenerator $routeGenerator;

    protected function setUp(): void
    {
        $this->context = new Context();
        $this->uriSignerMock = $this->createMock(UriSigner::class);
        $this->urlGeneratorMock = $this->createMock(UrlGeneratorInterface::class);

        $this->routeGenerator = new RouteGenerator(
            $this->context,
            $this->uriSignerMock,
            $this->urlGeneratorMock,
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\CollectionPager\RouteGenerator::__construct
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\CollectionPager\RouteGenerator::__invoke
     * @dataProvider invokeDataProvider
     */
    public function testInvoke(int $page, string $signedUri, string $signedUriSuffix): void
    {
        $block = Block::fromArray(
            [
                'id' => Uuid::uuid4(),
                'locale' => 'en',
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
            ->willReturn('/generated/uri');

        $this->uriSignerMock->expects(self::once())
            ->method('sign')
            ->with(self::identicalTo('/generated/uri'))
            ->willReturn($signedUri);

        $url = call_user_func($this->routeGenerator, $block, 'default', $page);

        self::assertSame($signedUri . $signedUriSuffix, $url);
    }

    public function invokeDataProvider(): array
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
