<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\Templating\Twig\Node;

use Netgen\Bundle\LayoutsBundle\Templating\Twig\Node\RenderZone;
use Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\RenderingRuntime;
use Netgen\Layouts\API\Values\Layout\Zone;
use Netgen\Layouts\View\Twig\ContextualizedTwigTemplate;
use Netgen\Layouts\View\ViewInterface;
use Twig\Environment;
use Twig\Node\Expression\ConstantExpression;
use Twig\Node\Expression\NameExpression;
use Twig\Node\Expression\Variable\ContextVariable;

use function class_exists;

/**
 * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\Node\RenderZone::compile
 * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\Node\RenderZone::compileContextNode
 * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\Node\RenderZone::compileRenderNode
 */
final class RenderZoneTest extends NodeTestBase
{
    private static string $contextVariableClass = NameExpression::class;

    protected function setUp(): void
    {
        if (Environment::MAJOR_VERSION === 2) {
            self::markTestSkipped('Test requires twig/twig 3.9 to run');
        }

        if (class_exists(ContextVariable::class)) {
            self::$contextVariableClass = ContextVariable::class;
        }
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\Node\RenderZone::__construct
     */
    public function testConstructor(): void
    {
        /** @var \Twig\Node\Expression\AbstractExpression $zone */
        $zone = new self::$contextVariableClass('zone', 1);

        /** @var \Twig\Node\Expression\AbstractExpression $context */
        $context = new self::$contextVariableClass('context', 1);
        $node = new RenderZone($zone, $context, 1);

        self::assertSame($zone, $node->getNode('zone'));
        self::assertSame($context, $node->getNode('context'));
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\Node\RenderZone::__construct
     */
    public function testConstructorWithNoContext(): void
    {
        /** @var \Twig\Node\Expression\AbstractExpression $zone */
        $zone = new self::$contextVariableClass('zone', 1);
        $node = new RenderZone($zone, null, 1);

        self::assertSame($zone, $node->getNode('zone'));
        self::assertFalse($node->hasNode('context'));
    }

    public static function getTests(): array
    {
        $environment = self::getEnvironment();
        $environment->enableStrictVariables();

        $zoneClass = Zone::class;
        $runtimeClass = RenderingRuntime::class;
        $templateClass = ContextualizedTwigTemplate::class;
        $viewInterface = ViewInterface::class;

        /** @var \Twig\Node\Expression\AbstractExpression $zone */
        $zone = new self::$contextVariableClass('zone', 1);
        $zoneName = new ConstantExpression('zone', 1);

        /** @var \Twig\Node\Expression\AbstractExpression $context */
        $context = new self::$contextVariableClass('context', 1);

        $zoneNodeGetter = self::getNodeGetter('zone');
        $contextNodeGetter = self::getNodeGetter('context');

        return [
            [
                new RenderZone($zone, $context, 1),
                <<<EOT
                // line 1
                \$nglZone = {$zoneNodeGetter};
                \$nglZoneIdentifier = \$nglZone instanceof {$zoneClass} ? \$nglZone->getIdentifier() : \$nglZone;
                \$nglContext = {$contextNodeGetter};
                \$nglTemplate = new {$templateClass}(\$this, \$context, \$blocks);
                yield \$this->env->getRuntime("{$runtimeClass}")->renderZone(\$context["nglayouts"]->getLayout(), \$nglZoneIdentifier, \$nglContext, \$nglTemplate);
                EOT,
                $environment,
            ],
            [
                new RenderZone($zoneName, $context, 1),
                <<<EOT
                // line 1
                \$nglZone = "zone";
                \$nglZoneIdentifier = \$nglZone instanceof {$zoneClass} ? \$nglZone->getIdentifier() : \$nglZone;
                \$nglContext = {$contextNodeGetter};
                \$nglTemplate = new {$templateClass}(\$this, \$context, \$blocks);
                yield \$this->env->getRuntime("{$runtimeClass}")->renderZone(\$context["nglayouts"]->getLayout(), \$nglZoneIdentifier, \$nglContext, \$nglTemplate);
                EOT,
                $environment,
            ],
            [
                new RenderZone($zone, null, 1),
                <<<EOT
                // line 1
                \$nglZone = {$zoneNodeGetter};
                \$nglZoneIdentifier = \$nglZone instanceof {$zoneClass} ? \$nglZone->getIdentifier() : \$nglZone;
                \$nglContext = {$viewInterface}::CONTEXT_DEFAULT;
                \$nglTemplate = new {$templateClass}(\$this, \$context, \$blocks);
                yield \$this->env->getRuntime("{$runtimeClass}")->renderZone(\$context["nglayouts"]->getLayout(), \$nglZoneIdentifier, \$nglContext, \$nglTemplate);
                EOT,
                $environment,
            ],
            [
                new RenderZone($zoneName, null, 1),
                <<<EOT
                // line 1
                \$nglZone = "zone";
                \$nglZoneIdentifier = \$nglZone instanceof {$zoneClass} ? \$nglZone->getIdentifier() : \$nglZone;
                \$nglContext = {$viewInterface}::CONTEXT_DEFAULT;
                \$nglTemplate = new {$templateClass}(\$this, \$context, \$blocks);
                yield \$this->env->getRuntime("{$runtimeClass}")->renderZone(\$context["nglayouts"]->getLayout(), \$nglZoneIdentifier, \$nglContext, \$nglTemplate);
                EOT,
                $environment,
            ],
        ];
    }
}
