<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\Templating\Twig\Node;

use Netgen\Bundle\LayoutsBundle\Templating\Twig\Node\RenderZone;
use Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\RenderingRuntime;
use Netgen\Layouts\API\Values\Layout\Zone;
use Netgen\Layouts\View\Twig\ContextualizedTwigTemplate;
use Netgen\Layouts\View\ViewInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use Twig\Node\Expression\ConstantExpression;
use Twig\Node\Expression\Variable\ContextVariable;

#[CoversClass(RenderZone::class)]
final class RenderZoneTest extends NodeTestBase
{
    public function testConstructor(): void
    {
        $zone = new ContextVariable('zone', 1);

        $context = new ContextVariable('context', 1);
        $node = new RenderZone($zone, $context, 1);

        self::assertSame($zone, $node->getNode('zone'));
        self::assertSame($context, $node->getNode('context'));
    }

    public function testConstructorWithNoContext(): void
    {
        $zone = new ContextVariable('zone', 1);
        $node = new RenderZone($zone, null, 1);

        self::assertSame($zone, $node->getNode('zone'));
        self::assertFalse($node->hasNode('context'));
    }

    public static function compileDataProvider(): iterable
    {
        $environment = self::getEnvironment();
        $environment->enableStrictVariables();

        $zoneClass = Zone::class;
        $runtimeClass = RenderingRuntime::class;
        $templateClass = ContextualizedTwigTemplate::class;
        $viewInterface = ViewInterface::class;

        $zone = new ContextVariable('zone', 1);
        $zoneName = new ConstantExpression('zone', 1);

        $context = new ContextVariable('context', 1);

        $zoneNodeGetter = self::getNodeGetter('zone');
        $contextNodeGetter = self::getNodeGetter('context');

        return [
            [
                new RenderZone($zone, $context, 1),
                <<<EOT
                // line 1
                \$nglZone = {$zoneNodeGetter};
                \$nglZoneIdentifier = \$nglZone instanceof {$zoneClass} ? \$nglZone->identifier : \$nglZone;
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
                \$nglZoneIdentifier = \$nglZone instanceof {$zoneClass} ? \$nglZone->identifier : \$nglZone;
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
                \$nglZoneIdentifier = \$nglZone instanceof {$zoneClass} ? \$nglZone->identifier : \$nglZone;
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
                \$nglZoneIdentifier = \$nglZone instanceof {$zoneClass} ? \$nglZone->identifier : \$nglZone;
                \$nglContext = {$viewInterface}::CONTEXT_DEFAULT;
                \$nglTemplate = new {$templateClass}(\$this, \$context, \$blocks);
                yield \$this->env->getRuntime("{$runtimeClass}")->renderZone(\$context["nglayouts"]->getLayout(), \$nglZoneIdentifier, \$nglContext, \$nglTemplate);
                EOT,
                $environment,
            ],
        ];
    }
}
