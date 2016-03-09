<?php

class EcomDev_SphinxSeo_Model_Source_Text_Store
    extends EcomDev_Sphinx_Model_Source_AbstractOption
{
    protected function _initOptions()
    {
        $this->_options = [
            0 => $this->__('All Store Views')
        ];

        foreach (Mage::app()->getStores() as $store) {
            $this->_options[$store->getId()] = $store->getName();
        }

        return $this;
    }

}
