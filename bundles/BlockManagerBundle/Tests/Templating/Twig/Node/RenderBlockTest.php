<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\Templating\Twig\Node;

use Netgen\BlockManager\API\Values\Page\Block;
use Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension;
use Netgen\Bundle\BlockManagerBundle\Templating\Twig\Node\RenderBlock;
use Twig_Node_Expression_Name;

class RenderBlockTest extends \Twig_Test_NodeTestCase
{
    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Node\RenderBlock::__construct
     */
    public function testConstructor()
    {
        $block = new Twig_Node_Expression_Name('block', 1);
        $context = new Twig_Node_Expression_Name('context', 1);
        $node = new RenderBlock($block, $context, 1);

        $this->assertEquals($block, $node->getNode('block'));
        $this->assertEquals($context, $node->getNode('context'));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Node\RenderBlock::__construct
     */
    public function testConstructorWithNoContext()
    {
        $block = new Twig_Node_Expression_Name('block', 1);
        $node = new RenderBlock($block, null, 1);

        $this->assertEquals($block, $node->getNode('block'));
        $this->assertNull($node->getNode('context'));
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

        $block = new Twig_Node_Expression_Name('block', 1);
        $context = new Twig_Node_Expression_Name('context', 1);

        return array(
            array(
                new RenderBlock($block, $context, 1),
                <<<EOT
// line 1
\$ngbmBlock = (isset(\$context["block"]) ? \$context["block"] : \$this->getContext(\$context, "block"));
\$ngbmContext = (isset(\$context["context"]) ? \$context["context"] : \$this->getContext(\$context, "context"));
if (\$ngbmBlock instanceof {$blockClass}) {
    \$this->env->getExtension("{$extensionClass}")->displayBlock(\$ngbmBlock, \$ngbmContext, \$this, \$context, \$blocks);
}
EOT
                ,
                $environment,
            ),
            array(
                new RenderBlock($block, null, 1),
                <<<EOT
// line 1
\$ngbmBlock = (isset(\$context["block"]) ? \$context["block"] : \$this->getContext(\$context, "block"));
\$ngbmContext = Netgen\BlockManager\View\ViewInterface::CONTEXT_DEFAULT;
if (\$ngbmBlock instanceof {$blockClass}) {
    \$this->env->getExtension("{$extensionClass}")->displayBlock(\$ngbmBlock, \$ngbmContext, \$this, \$context, \$blocks);
}
EOT
                ,
                $environment,
            ),
        );
    }
}
