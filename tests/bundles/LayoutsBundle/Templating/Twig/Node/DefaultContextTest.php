<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\Templating\Twig\Node;

use Netgen\Bundle\LayoutsBundle\Templating\Twig\Node\DefaultContext;
use PHPUnit\Framework\Attributes\CoversClass;
use Twig\Node\Expression\ConstantExpression;
use Twig\Node\Expression\Variable\ContextVariable;

#[CoversClass(DefaultContext::class)]
final class DefaultContextTest extends NodeTestBase
{
    public function testConstructor(): void
    {
        $var = new ContextVariable('foo', 1);
        $node = new DefaultContext($var, 1);

        self::assertSame($var, $node->getNode('expr'));
    }

    public function testConstructorWithConstant(): void
    {
        $var = new ConstantExpression('foo', 1);
        $node = new DefaultContext($var, 1);

        self::assertSame($var, $node->getNode('expr'));
    }

    public static function getTests(): array
    {
        $environment = self::getEnvironment();
        $environment->enableStrictVariables();

        $var = new ContextVariable('foo', 1);
        $string = new ConstantExpression('foo', 1);

        return [
            [
                new DefaultContext($var, 1),
                '',
                $environment,
            ],
            [
                new DefaultContext($string, 1),
                '',
                $environment,
            ],
        ];
    }
}
