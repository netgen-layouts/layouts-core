<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\Templating\Twig\Node;

use Netgen\Bundle\BlockManagerBundle\Templating\Twig\Node\RenderZone;
use Netgen\BlockManager\View\ViewInterface;
use Twig_Node_Expression_Name;

class RenderZoneTest extends \Twig_Test_NodeTestCase
{
    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Node\RenderZone::__construct
     */
    public function testConstructor()
    {
        $zone = new Twig_Node_Expression_Name('zone', 1);
        $node = new RenderZone($zone, ViewInterface::CONTEXT_VIEW, 1);
        $this->assertEquals($zone, $node->getNode('zone'));
        $this->assertEquals(ViewInterface::CONTEXT_VIEW, $node->getAttribute('context'));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Node\RenderZone::compile
     */
    public function getTests()
    {
        $environment = $this->getEnvironment();
        $environment->enableStrictVariables();

        $zone = new Twig_Node_Expression_Name('zone', 1);
        $node = new RenderZone($zone, ViewInterface::CONTEXT_VIEW, 1);

        return array(
            array(
                $node,
                <<<EOF
// line 1
\$ngbmZone = (isset(\$context["zone"]) ? \$context["zone"] : \$this->getContext(\$context, "zone"));
if (\$ngbmZone instanceof \Netgen\BlockManager\API\Values\Page\Zone) {
    \$this->env->getExtension("ngbm_render")->displayZone(\$ngbmZone, "view", \$this, \$context, \$blocks);
}
EOF
                ,
                $environment,
            ),
        );
    }
}
