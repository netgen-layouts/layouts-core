<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\Templating\Twig\Node;

use Netgen\Bundle\LayoutsBundle\Templating\Twig\Node\DefaultContext;
use Twig\Node\Expression\ConstantExpression;
use Twig\Node\Expression\NameExpression;

/**
 * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\Node\DefaultContext::compile
 */
final class DefaultContextTest extends NodeTestBase
{
    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\Node\DefaultContext::__construct
     */
    public function testConstructor(): void
    {
        $var = new NameExpression('foo', 1);
        $node = new DefaultContext($var, 1);

        self::assertSame($var, $node->getNode('expr'));
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\Node\DefaultContext::__construct
     */
    public function testConstructorWithConstant(): void
    {
        $var = new ConstantExpression('foo', 1);
        $node = new DefaultContext($var, 1);

        self::assertSame($var, $node->getNode('expr'));
    }

    /**
     * @return mixed[]
     */
    public function getTests(): array
    {
        $environment = $this->getEnvironment();
        $environment->enableStrictVariables();

        $var = new NameExpression('foo', 1);
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
