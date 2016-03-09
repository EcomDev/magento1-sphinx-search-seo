<?php

class EcomDev_SphinxSeo_Block_Adminhtml_Text
    extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    protected function _prepareLayout()
    {
        $this->_headerText = $this->__('Manage SEO Text');
        return Mage_Adminhtml_Block_Widget_Container::_prepareLayout();
    }
}
