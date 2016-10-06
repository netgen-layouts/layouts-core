<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\Templating\Twig\Node;

use Netgen\BlockManager\API\Values\Page\Block;
use Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension;
use Netgen\Bundle\BlockManagerBundle\Templating\Twig\Node\RenderBlock;
use Netgen\BlockManager\View\ViewInterface;
use Twig_Node_Expression_Name;

class RenderBlockTest extends \Twig_Test_NodeTestCase
{
    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Node\RenderBlock::__construct
     */
    public function testConstructor()
    {
        $block = new Twig_Node_Expression_Name('block', 1);
        $node = new RenderBlock($block, ViewInterface::CONTEXT_DEFAULT, 1);
        $this->assertEquals($block, $node->getNode('block'));
        $this->assertEquals(ViewInterface::CONTEXT_DEFAULT, $node->getAttribute('context'));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Node\RenderBlock::compile
     */
    public function getTests()
    {
        $environment = $this->getEnvironment();
        $environment->enableStrictVariables();

        $blockClass = Block::class;
        $extensionClass = RenderingExtension::class;
        $context = ViewInterface::CONTEXT_DEFAULT;

        $block = new Twig_Node_Expression_Name('block', 1);
        $node = new RenderBlock($block, $context, 1);

        return array(
            array(
                $node,
                <<<EOT
// line 1
\$ngbmBlock = (isset(\$context["block"]) ? \$context["block"] : \$this->getContext(\$context, "block"));
if (\$ngbmBlock instanceof {$blockClass}) {
    \$this->env->getExtension("{$extensionClass}")->displayBlock(\$ngbmBlock, "{$context}", \$this, \$context, \$blocks);
}
EOT
                ,
                $environment,
            ),
        );
    }
}
