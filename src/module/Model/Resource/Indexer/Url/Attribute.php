<?php

class EcomDev_SphinxSeo_Model_Resource_Indexer_Url_Attribute
    extends EcomDev_Sphinx_Model_Resource_Indexer_Catalog_AbstractIndexer
{
    /**
     * Resource initialization
     */
    protected function _construct()
    {
        $this->_init('ecomdev_sphinxseo/index_url', 'slug');
    }

    public function reindexAll()
    {
        $this->_transactional(function () { $this->_reindex(); });
    }

    /**
     * Process attribute save
     *
     * @param Mage_Index_Model_Event $event
     * @return $this
     */
    public function sphinxAttributeSave(Mage_Index_Model_Event $event)
    {
        $this->processAttributeEvent($event);
    }

    /**
     * Process attribute delete
     *
     * @param Mage_Index_Model_Event $event
     * @return $this
     */
    public function sphinxAttributeDelete(Mage_Index_Model_Event $event)
    {
        $this->processAttributeEvent($event);
    }

    private function processAttributeEvent(Mage_Index_Model_Event $event)
    {
        $data = $event->getNewData();
        if (isset($data[EcomDev_SphinxSeo_Model_Indexer_Url_Attribute::EVENT_ATTRIBUTE_IDS])) {
            $attributeIds = $data[EcomDev_SphinxSeo_Model_Indexer_Url_Attribute::EVENT_ATTRIBUTE_IDS];
        } else {
            return $this;
        }

        $this->_transactional(function ($attributeIds) {
            $this->_reindex($attributeIds);
        }, true, $attributeIds);
    }

    protected function _reindex($attributeIds = null)
    {
        $deleteConditions = [
            'type = ?' => 'attribute',
        ];

        if (is_array($attributeIds)) {
            $deleteConditions['id IN(?)'] = $attributeIds;
        }

        $this->_getIndexAdapter()->delete($this->getMainTable(), $deleteConditions);


        $select = $this->_getIndexAdapter()->select();
        $select
            ->from(
                ['default_url' => $this->getTable('ecomdev_sphinxseo/option_url')],
                []
            )
            ->join(
                ['option' => $this->getTable('eav/attribute_option')],
                'option.option_id = default_url.option_id',
                []
            )
            ->join(
                ['attribute' => $this->getTable('eav/attribute')],
                'attribute.attribute_id = option.attribute_id',
                []
            )
            ->joinLeft(
                ['store_url' => $this->getTable('ecomdev_sphinxseo/option_url')],
                $this->_createCondition(
                    'store_url.option_id = default_url.option_id',
                    'store_url.store_id = :store_id'
                ),
                []
            )
            ->columns([
                'store_id' => new Zend_Db_Expr(':store_id'),
                'slug' => 'IFNULL(store_url.slug, default_url.slug)',
                'checksum' => 'CRC32(IFNULL(store_url.slug, default_url.slug))',
                'id' => 'attribute.attribute_id',
                'type' => new Zend_Db_Expr(':type'),
                'filter' => 'attribute.attribute_code',
                'value' => 'default_url.option_id',
                'position' => 'option.sort_order'
            ])
            ->where('default_url.store_id = ?', 0);
        ;


        if (is_array($attributeIds)) {
            $select->where('option.attribute_id IN(?)', $attributeIds);
        }

        $insertFromSelect = $this->_getIndexAdapter()->insertFromSelect(
            $select,
            $this->getMainTable(),
            ['store_id', 'slug', 'checksum', 'id', 'type', 'filter', 'value', 'position']
        );

        foreach (Mage::app()->getStores() as $store) {
            $this->_getIndexAdapter()->query($insertFromSelect, [
                'store_id' => $store->getId(),
                'type' => 'attribute'
            ]);
        }
    }
}
