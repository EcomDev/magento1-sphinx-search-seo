<?php

class EcomDev_SphinxSeo_Model_Resource_Indexer_Text
    extends EcomDev_Sphinx_Model_Resource_Indexer_Catalog_AbstractIndexer
{
    /**
     * Resource initialization
     */
    protected function _construct()
    {
        $this->_init('ecomdev_sphinxseo/index_text', 'text_id');
    }

    public function reindexAll()
    {
        $this->_reindex();
    }

    /**
     * Process attribute save
     *
     * @param Mage_Index_Model_Event $event
     * @return $this
     */
    public function sphinxseoTextSave(Mage_Index_Model_Event $event)
    {
        $this->processTextEvent($event);
    }

    /**
     * Process attribute delete
     *
     * @param Mage_Index_Model_Event $event
     * @return $this
     */
    public function sphinxseoTextDelete(Mage_Index_Model_Event $event)
    {
        $this->processTextEvent($event);
    }

    private function processTextEvent(Mage_Index_Model_Event $event)
    {
        $data = $event->getNewData();
        if (isset($data[EcomDev_SphinxSeo_Model_Indexer_Text::EVENT_TEXT_IDS])) {
            $textIds = $data[EcomDev_SphinxSeo_Model_Indexer_Text::EVENT_TEXT_IDS];
        } else {
            return $this;
        }

        $this->_transactional(function ($textIds) {
            $this->_reindex($textIds);
        }, true, $textIds);
    }

    protected function _reindex($textIds = null)
    {
        if (is_array($textIds)) {
            $this->_getIndexAdapter()->delete($this->getMainTable(), ['text_id IN(?)' => $textIds]);
        } else {
            $this->_getIndexAdapter()->truncateTable($this->getMainTable());
        }

        $condition = 'CONCAT(filter.filter, \'=\', filter.value)';

        $select = $this->_getIndexAdapter()->select();
        $select
            ->from(
                ['text' => $this->getTable('ecomdev_sphinxseo/text')],
                []
            )
            ->join(
                ['store' => $this->getTable('core/store')],
                'store.store_id = text.store_id or text.store_id = 0',
                []
            )
            ->join(
                ['category' => $this->getTable('ecomdev_sphinxseo/text_category')],
                'category.text_id = text.text_id',
                []
            )
            ->join(
                ['filter' => $this->getTable('ecomdev_sphinxseo/text_filter')],
                'filter.text_id = text.text_id',
                []
            )
            ->columns([
                'text_id' => 'text.text_id',
                'store_id' => 'store.store_id',
                'category_id' => 'category.category_id',
                'field' => 'filter.filter',
                'condition' => $condition,
                'checksum' => sprintf('CRC32(%s)', $condition)
            ])
            ->where('store.store_id > ?', 0);
        ;


        if (is_array($textIds)) {
            $select->where('text.text_id IN(?)', $textIds);
        }

        $insertFromSelect = $this->_getIndexAdapter()->insertFromSelect(
            $select,
            $this->getMainTable(),
            ['text_id', 'store_id', 'category_id', 'field', 'condition', 'checksum']
        );

        $this->_getIndexAdapter()->query($insertFromSelect);
    }
}
