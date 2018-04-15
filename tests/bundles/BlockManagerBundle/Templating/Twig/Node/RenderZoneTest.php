<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\Templating\Twig\Node;

use Netgen\BlockManager\API\Values\Layout\Zone;
use Netgen\BlockManager\View\Twig\ContextualizedTwigTemplate;
use Netgen\Bundle\BlockManagerBundle\Templating\Twig\Node\RenderZone;
use Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime;
use Twig\Node\Expression\NameExpression;

final class RenderZoneTest extends NodeTest
{
    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Node\RenderZone::__construct
     */
    public function testConstructor()
    {
        $zone = new NameExpression('zone', 1);
        $context = new NameExpression('context', 1);
        $node = new RenderZone($zone, $context, 1);

        $this->assertEquals($zone, $node->getNode('zone'));
        $this->assertEquals($context, $node->getNode('context'));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Node\RenderZone::__construct
     */
    public function testConstructorWithNoContext()
    {
        $zone = new NameExpression('zone', 1);
        $node = new RenderZone($zone, null, 1);

        $this->assertEquals($zone, $node->getNode('zone'));
        $this->assertFalse($node->hasNode('context'));
    }

    public function getTests()
    {
        $environment = $this->getEnvironment();
        $environment->enableStrictVariables();

        $zoneClass = Zone::class;
        $runtimeClass = RenderingRuntime::class;
        $templateClass = ContextualizedTwigTemplate::class;

        $zone = new NameExpression('zone', 1);
        $context = new NameExpression('context', 1);

        return array(
            array(
                new RenderZone($zone, $context, 1),
                <<<EOT
// line 1
\$ngbmZone = {$this->getNodeGetter('zone')};
\$ngbmContext = {$this->getNodeGetter('context')};
\$ngbmTemplate = new {$templateClass}(\$this, \$context, \$blocks);
if (\$ngbmZone instanceof {$zoneClass}) {
    \$this->env->getRuntime("{$runtimeClass}")->displayZone(\$ngbmZone, \$ngbmContext, \$ngbmTemplate);
}
EOT
                ,
                $environment,
            ),
            array(
                new RenderZone($zone, null, 1),
                <<<EOT
// line 1
\$ngbmZone = {$this->getNodeGetter('zone')};
\$ngbmContext = Netgen\\BlockManager\\View\\ViewInterface::CONTEXT_DEFAULT;
\$ngbmTemplate = new {$templateClass}(\$this, \$context, \$blocks);
if (\$ngbmZone instanceof {$zoneClass}) {
    \$this->env->getRuntime("{$runtimeClass}")->displayZone(\$ngbmZone, \$ngbmContext, \$ngbmTemplate);
}
EOT
                ,
                $environment,
            ),
        );
    }

    /**
     * Overriden to enable 'covers' annotation.
     *
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Node\RenderZone::compile
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Node\RenderZone::compileContextNode
     *
     * @param \Twig\Node\Node $node
     * @param string $source
     * @param \Twig\Environment $environment
     * @param bool $isPattern
     *
     * @dataProvider getTests
     */
    public function testCompile($node, $source, $environment = null, $isPattern = false)
    {
        parent::testCompile($node, $source, $environment, $isPattern);
    }
}
