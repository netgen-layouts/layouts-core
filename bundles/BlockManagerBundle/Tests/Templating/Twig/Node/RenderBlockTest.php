<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\Templating\Twig\Node;

use Netgen\BlockManager\API\Values\Page\Block;
use Netgen\BlockManager\Block\BlockDefinition\Twig\ContextualizedTwigTemplate;
use Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension\RenderingExtension;
use Netgen\Bundle\BlockManagerBundle\Templating\Twig\Node\RenderBlock;
use Twig_Node_Expression_Name;

class RenderBlockTest extends NodeTest
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
        $this->assertFalse($node->hasNode('context'));
    }

    /**
     * Overriden to enable 'covers' annotation.
     *
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Node\ContextTrait::compileContextNode
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Node\RenderBlock::compile
     *
     * @param \Twig_Node $node
     * @param string $source
     * @param \Twig_Environment $environment
     * @param bool $isPattern
     *
     * @dataProvider getTests
     */
    public function testCompile($node, $source, $environment = null, $isPattern = false)
    {
        parent::testCompile($node, $source, $environment, $isPattern);
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
        $templateClass = ContextualizedTwigTemplate::class;

        $block = new Twig_Node_Expression_Name('block', 1);
        $context = new Twig_Node_Expression_Name('context', 1);

        return array(
            array(
                new RenderBlock($block, $context, 1),
                <<<EOT
// line 1
\$ngbmBlock = {$this->getNodeGetter('block')};
\$ngbmContext = {$this->getNodeGetter('context')};
\$ngbmTemplate = new {$templateClass}(\$this, \$context, \$blocks);
if (\$ngbmBlock instanceof {$blockClass}) {
    \$this->env->getExtension("{$extensionClass}")->displayBlock(\$ngbmBlock, \$ngbmContext, \$ngbmTemplate);
}
EOT
                ,
                $environment,
            ),
            array(
                new RenderBlock($block, null, 1),
                <<<EOT
// line 1
\$ngbmBlock = {$this->getNodeGetter('block')};
\$ngbmContext = Netgen\BlockManager\View\ViewInterface::CONTEXT_DEFAULT;
\$ngbmTemplate = new {$templateClass}(\$this, \$context, \$blocks);
if (\$ngbmBlock instanceof {$blockClass}) {
    \$this->env->getExtension("{$extensionClass}")->displayBlock(\$ngbmBlock, \$ngbmContext, \$ngbmTemplate);
}
EOT
                ,
                $environment,
            ),
        );
    }
}
