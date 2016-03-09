<?php

class EcomDev_SphinxSeo_Block_Adminhtml_Text_Edit_Tab_Categories
    extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Categories
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Return array with category IDs which the product is assigned to
     *
     * @return array
     */
    protected function getCategoryIds()
    {
        return $this->getDataObject()->getCategoryIds();
    }

    /**
     * Checks when this block is readonly
     *
     * @return boolean
     */
    public function isReadonly()
    {
        return false;
    }

    /**
     * Seo text model
     *
     * @return EcomDev_SphinxSeo_Model_Text
     */
    public function getDataObject()
    {
        return Mage::registry('current_object');
    }

    /**
     * Return Tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return $this->__('Applicable Categories');
    }

    /**
     * Return Tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return !$this->isHidden();
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }


}
