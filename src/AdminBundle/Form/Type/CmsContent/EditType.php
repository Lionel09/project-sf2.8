<?php

namespace AdminBundle\Form\Type\CmsContent;

use Admingenerated\AdminBundle\Form\BaseCmsContentType\EditType as BaseEditType;

/**
 * EditType
 */
class EditType extends BaseEditType
{
    /**
     * Get options for cms_types field.
     *
     * @param  array $builderOptions The builder options.
     * @return array Field options.
     */
//     protected function getOptionsCmsTypes(array $builderOptions = array())
//     {
//         $optionsClass = '\AdminBundle\Form\Type\CmsContent\Options';
//         $options = class_exists($optionsClass) ? new $optionsClass() : null;
// 
//         return $this->resolveOptions('cms_types', array(  'label' => 'Cms types',  'translation_domain' => 'Admin',  '// required' => false,  'multiple' => true,  'class' => 'Model\\CmsType',  'query' => \Model\CmsTypeQuery::create()// ->orderById(),), $builderOptions, $options);
//     }

}
