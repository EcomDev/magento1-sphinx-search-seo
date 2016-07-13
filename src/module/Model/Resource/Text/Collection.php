<?php

class EcomDev_SphinxSeo_Model_Resource_Text_Collection
    extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('ecomdev_sphinxseo/text');
    }

    /**
     * Init collection select
     *
     * @return Mage_Core_Model_Resource_Db_Collection_Abstract
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->getSelect()->joinLeft(
            ['url_slug' => $this->getTable('ecomdev_sphinxseo/text_url')],
            'url_slug.text_id = main_table.text_id',
            ['url_slug']
        );
        $this->addFilterToMap('url_slug', 'url_slug.url_slug');
        $this->addFilterToMap('text_id', 'main_table.text_id');
        $this->addFilterToMap('checksum', 'url_slug.checksum');
    }


}
