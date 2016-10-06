<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\Templating\Twig\Node;

use Netgen\BlockManager\API\Values\Page\Zone;
use Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension;
use Netgen\Bundle\BlockManagerBundle\Templating\Twig\Node\RenderZone;
use Twig_Node_Expression_Name;

class RenderZoneTest extends \Twig_Test_NodeTestCase
{
    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Node\RenderZone::__construct
     */
    public function testConstructor()
    {
        $zone = new Twig_Node_Expression_Name('zone', 1);
        $context = new Twig_Node_Expression_Name('context', 1);
        $node = new RenderZone($zone, $context, 1);

        $this->assertEquals($zone, $node->getNode('zone'));
        $this->assertEquals($context, $node->getNode('context'));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Node\RenderZone::__construct
     */
    public function testConstructorWithNoContext()
    {
        $zone = new Twig_Node_Expression_Name('zone', 1);
        $node = new RenderZone($zone, null, 1);

        $this->assertEquals($zone, $node->getNode('zone'));
        $this->assertNull($node->getNode('context'));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Node\RenderZone::compile
     */
    public function getTests()
    {
        $environment = $this->getEnvironment();
        $environment->enableStrictVariables();

        $zoneClass = Zone::class;
        $extensionClass = RenderingExtension::class;

        $zone = new Twig_Node_Expression_Name('zone', 1);
        $context = new Twig_Node_Expression_Name('context', 1);

        return array(
            array(
                new RenderZone($zone, $context, 1),
                <<<EOT
// line 1
\$ngbmZone = (isset(\$context["zone"]) ? \$context["zone"] : \$this->getContext(\$context, "zone"));
\$ngbmContext = (isset(\$context["context"]) ? \$context["context"] : \$this->getContext(\$context, "context"));
if (\$ngbmZone instanceof {$zoneClass}) {
    \$this->env->getExtension("{$extensionClass}")->displayZone(\$ngbmZone, \$ngbmContext, \$this, \$context, \$blocks);
}
EOT
                ,
                $environment,
            ),
            array(
                new RenderZone($zone, null, 1),
                <<<EOT
// line 1
\$ngbmZone = (isset(\$context["zone"]) ? \$context["zone"] : \$this->getContext(\$context, "zone"));
\$ngbmContext = Netgen\BlockManager\View\ViewInterface::CONTEXT_DEFAULT;
if (\$ngbmZone instanceof {$zoneClass}) {
    \$this->env->getExtension("{$extensionClass}")->displayZone(\$ngbmZone, \$ngbmContext, \$this, \$context, \$blocks);
}
EOT
                ,
                $environment,
            ),
        );
    }
}
