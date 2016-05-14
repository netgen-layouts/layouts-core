<?php

namespace Netgen\BlockManager\Block\Form\BlockInlineEdit;

use Netgen\BlockManager\Block\Form\BlockInlineEditType;

class ParagraphType extends BlockInlineEditType
{
    /**
     * Returns the list of block definition parameters that will be editable inline.
     *
     * @return array
     */
    public function getParameterNames()
    {
        return array('content');
    }

    /**
     * Returns the prefix of the template block name for this type.
     *
     * The block prefixes default to the underscored short class name with
     * the "Type" suffix removed (e.g. "UserProfileType" => "user_profile").
     *
     * @return string The prefix of the template block name
     */
    public function getBlockPrefix()
    {
        return 'block_inline_edit_paragraph';
    }
}
