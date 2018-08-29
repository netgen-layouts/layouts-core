<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Tests\Templating\Twig\Node;

use Netgen\BlockManager\API\Values\Layout\Zone;
use Netgen\BlockManager\View\Twig\ContextualizedTwigTemplate;
use Netgen\BlockManager\View\ViewInterface;
use Netgen\Bundle\BlockManagerBundle\Templating\Twig\Node\RenderZone;
use Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime;
use Twig\Node\Expression\ConstantExpression;
use Twig\Node\Expression\NameExpression;

/**
 * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Node\RenderZone::compile
 * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Node\RenderZone::compileContextNode
 */
final class RenderZoneTest extends NodeTest
{
    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Node\RenderZone::__construct
     */
    public function testConstructor(): void
    {
        $zone = new NameExpression('zone', 1);
        $context = new NameExpression('context', 1);
        $node = new RenderZone($zone, $context, 1);

        self::assertSame($zone, $node->getNode('zone'));
        self::assertSame($context, $node->getNode('context'));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Node\RenderZone::__construct
     */
    public function testConstructorWithNoContext(): void
    {
        $zone = new NameExpression('zone', 1);
        $node = new RenderZone($zone, null, 1);

        self::assertSame($zone, $node->getNode('zone'));
        self::assertFalse($node->hasNode('context'));
    }

    public function getTests(): array
    {
        $environment = $this->getEnvironment();
        $environment->enableStrictVariables();

        $zoneClass = Zone::class;
        $runtimeClass = RenderingRuntime::class;
        $templateClass = ContextualizedTwigTemplate::class;
        $viewInterface = ViewInterface::class;

        $zone = new NameExpression('zone', 1);
        $zoneName = new ConstantExpression('zone', 1);
        $context = new NameExpression('context', 1);

        return [
            [
                new RenderZone($zone, $context, 1),
                <<<EOT
// line 1
\$ngbmZone = {$this->getNodeGetter('zone')};
\$ngbmZoneIdentifier = \$ngbmZone instanceof {$zoneClass} ? \$ngbmZone->getIdentifier() : \$ngbmZone;
\$ngbmContext = {$this->getNodeGetter('context')};
\$ngbmTemplate = new {$templateClass}(\$this, \$context, \$blocks);
\$this->env->getRuntime("{$runtimeClass}")->displayZone(\$context["ngbm"]->getLayout(), \$ngbmZoneIdentifier, \$ngbmContext, \$ngbmTemplate);
EOT
                ,
                $environment,
            ],
            [
                new RenderZone($zoneName, $context, 1),
                <<<EOT
// line 1
\$ngbmZone = "zone";
\$ngbmZoneIdentifier = \$ngbmZone instanceof {$zoneClass} ? \$ngbmZone->getIdentifier() : \$ngbmZone;
\$ngbmContext = {$this->getNodeGetter('context')};
\$ngbmTemplate = new {$templateClass}(\$this, \$context, \$blocks);
\$this->env->getRuntime("{$runtimeClass}")->displayZone(\$context["ngbm"]->getLayout(), \$ngbmZoneIdentifier, \$ngbmContext, \$ngbmTemplate);
EOT
                ,
                $environment,
            ],
            [
                new RenderZone($zone, null, 1),
                <<<EOT
// line 1
\$ngbmZone = {$this->getNodeGetter('zone')};
\$ngbmZoneIdentifier = \$ngbmZone instanceof {$zoneClass} ? \$ngbmZone->getIdentifier() : \$ngbmZone;
\$ngbmContext = {$viewInterface}::CONTEXT_DEFAULT;
\$ngbmTemplate = new {$templateClass}(\$this, \$context, \$blocks);
\$this->env->getRuntime("{$runtimeClass}")->displayZone(\$context["ngbm"]->getLayout(), \$ngbmZoneIdentifier, \$ngbmContext, \$ngbmTemplate);
EOT
                ,
                $environment,
            ],
            [
                new RenderZone($zoneName, null, 1),
                <<<EOT
// line 1
\$ngbmZone = "zone";
\$ngbmZoneIdentifier = \$ngbmZone instanceof {$zoneClass} ? \$ngbmZone->getIdentifier() : \$ngbmZone;
\$ngbmContext = {$viewInterface}::CONTEXT_DEFAULT;
\$ngbmTemplate = new {$templateClass}(\$this, \$context, \$blocks);
\$this->env->getRuntime("{$runtimeClass}")->displayZone(\$context["ngbm"]->getLayout(), \$ngbmZoneIdentifier, \$ngbmContext, \$ngbmTemplate);
EOT
                ,
                $environment,
            ],
        ];
    }
}
