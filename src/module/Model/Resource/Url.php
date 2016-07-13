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

    public function findSingleText($path, $storeId)
    {
        $path = trim($path, '/');
        $select = $this->_getReadAdapter()->select();
        $select
            ->from(
                ['slug' => $this->getTable('ecomdev_sphinxseo/text_url')],
                ['category_id', 'text_id', 'url_slug']
            )
            ->join(
                ['text' => $this->getTable('ecomdev_sphinxseo/text')],
                'text.text_id = slug.text_id',
                []
            )
            ->where('slug.checksum = ?', crc32($path))
            ->where('text.store_id IN(0, ?)', $storeId)
            ->where('text.is_active = ?', 1)
            ->having('url_slug = ?', $path)
            ->limit(1)
        ;

        $row = $this->_getReadAdapter()->fetchRow($select);
        if ($row) {
            $row['query'] = [];
            $select = $this->_getReadAdapter()->select();
            $select->from($this->getTable('ecomdev_sphinxseo/text_filter'), ['filter', 'value'])
                ->where('text_id = ?', $row['text_id']);


            foreach ($this->_getReadAdapter()->query($select) as $item) {
                if (isset($row['query'][$item['filter']])) {
                    $row['query'][$item['filter']] .= ',' . $item['value'];
                    continue;
                }

                $row['query'][$item['filter']] = $item['value'];
            }

            $select->reset()->from($this->getTable('core/url_rewrite'), ['request_path'])
                ->where('id_path = ?', sprintf('category/%s', $row['category_id']))
                ->where('store_id = ?', $storeId)
                ->where('is_system = ?', 1)
                ->limit(1);

            $row['path_info'] = '/' . $this->_getReadAdapter()->fetchOne($select);
            return $row;
        }

        return false;
    }
}
