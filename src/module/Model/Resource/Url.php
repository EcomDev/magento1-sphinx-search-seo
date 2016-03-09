<?php

class EcomDev_SphinxSeo_Model_Resource_Url
    extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('ecomdev_sphinxseo/index_url', 'slug');
    }

    public function findFilters($slugs, $storeId)
    {
        $select = $this->_getReadAdapter()->select();
        $select
            ->from(
                $this->getMainTable(),
                ['slug', 'filter', 'value']
            )
            ->where('store_id = ?', $storeId)
            ->where('checksum IN(?)', array_map('crc32', $slugs))
            ->having('slug IN(?)', $slugs)
        ;

        return $this->_getReadAdapter()->fetchAssoc($select);
    }

    public function findSlugs($filters, $storeId)
    {
        $select = $this->_getReadAdapter()->select();
        $select
            ->from(
                $this->getMainTable(),
                ['filter', 'value', 'slug']
            )
            ->where('store_id = ?', $storeId)
            ->where('filter IN(?)', $filters)
            ->order('position asc')
        ;

        return $this->_getReadAdapter()->query($select);
    }
}
