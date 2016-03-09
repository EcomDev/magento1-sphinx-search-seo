<?php

class EcomDev_SphinxSeo_Model_Resource_Text_Collection
    extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('ecomdev_sphinxseo/text');
    }
}
