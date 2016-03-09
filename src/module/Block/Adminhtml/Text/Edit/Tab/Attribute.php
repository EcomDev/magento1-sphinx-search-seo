<?php

class EcomDev_SphinxSeo_Block_Adminhtml_Text_Edit_Tab_Attribute
    extends EcomDev_SphinxSeo_Block_Adminhtml_Text_Edit_AbstractTab
{
    protected $_fieldNameSuffix = 'text[category]';

    protected function _getName()
    {
        return $this->__('Category Attributes');
    }

    protected function _createFields()
    {
        $attributeCodes = $this->getDataObject()->getApplicableCategoryAttributeCodes();

        $eavConfig = Mage::getSingleton('eav/config');
        $eavConfig->preloadAttributes(Mage_Catalog_Model_Category::ENTITY, $attributeCodes);

        $attributes = [];
        foreach ($attributeCodes as $attributeCode) {
            $attribute = $eavConfig->getAttribute(Mage_Catalog_Model_Category::ENTITY, $attributeCode);
            if ($attribute) {
                $attribute->setIsRequired(false);
                $attributes[$attributeCode] = $attribute;
            }
        }

        $this->_setFieldset($attributes, $this->_currentFieldset);
        return $this;
    }

    /**
     * Sets values for fields from data object
     *
     * @return $this
     */
    protected function _setFieldValues()
    {
        $this->getForm()->setValues($this->getDataObject()->getValues());
        return $this;
    }


    /**
     * Retrieve Additional Element Types
     *
     * @return array
     */
    protected function _getAdditionalElementTypes()
    {
        return parent::_getAdditionalElementTypes() + [
            'textarea' => Mage::getConfig()->getBlockClassName('adminhtml/catalog_helper_form_wysiwyg')
        ];
    }
}
