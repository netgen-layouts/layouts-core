<?php

namespace Netgen\BlockManager\BlockDefinition\Form;

class ParagraphInlineType extends BlockInlineEditType
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
        return 'paragraph_inline';
    }
}
