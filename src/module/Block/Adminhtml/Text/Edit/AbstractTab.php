<?php

/**
 * @method EcomDev_SphinxSeo_Model_Text getDataObject()
 */
abstract class EcomDev_SphinxSeo_Block_Adminhtml_Text_Edit_AbstractTab
    extends EcomDev_Sphinx_Block_Adminhtml_Edit_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    protected $_fieldNameSuffix = 'text';

    abstract protected function _getName();

    /**
     * Prepares a form for a tab
     *
     * @return $this
     */
    protected function _beforePrepareForm()
    {
        $this->setForm(new Varien_Data_Form());
        $this->_addFieldset($this->getId() . '_fieldset', $this->_getName());
        return $this;
    }

    /**
     * Return Tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return $this->_getName();
    }

    /**
     * Return Tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->_getName();
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return true;
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
