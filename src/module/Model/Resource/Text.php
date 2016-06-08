<?php

class EcomDev_SphinxSeo_Model_Resource_Text
    extends EcomDev_Sphinx_Model_Resource_AbstractModel
{
    protected $_serializableFields = [
        'values' => [[], []]
    ];

    private $categoryTable;
    private $filterTable;

    /**
     * Resource initialization
     *
     */
    protected function _construct()
    {
        $this->_init('ecomdev_sphinxseo/text', 'text_id');
        $this->categoryTable = $this->getTable('ecomdev_sphinxseo/text_category');
        $this->filterTable = $this->getTable('ecomdev_sphinxseo/text_filter');
    }

    /**
     * Perform actions after object load
     *
     * @param Varien_Object $object
     * @return Mage_Core_Model_Resource_Db_Abstract
     */
    protected function _afterLoad(Mage_Core_Model_Abstract $object)
    {
        $select = $this->_getReadAdapter()->select();
        $select->from($this->categoryTable, 'category_id')
            ->where('text_id = ?', $object->getId());

        $object->setCategoryIds($this->_getReadAdapter()->fetchCol($select));

        $select = $this->_getReadAdapter()->select();
        $select->from($this->filterTable, ['filter', 'value'])
            ->where('text_id = ?', $object->getId());

        $filter = [];
        foreach ($select->query() as $row) {
            $filter[$row['filter']][$row['value']] = true;
        }

        $object->setFilter($filter);
        return parent::_afterLoad($object);
    }

    /**
     * Perform actions after object save
     *
     * @param Varien_Object $object
     * @return Mage_Core_Model_Resource_Db_Abstract
     */
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        $categoryIds = $object->getCategoryIds();
        $filters = $object->getFilter();

        if (!is_array($categoryIds)) {
            $categoryIds = [];
        }

        if (!is_array($filters)) {
            $filters = [];
        }

        $this->_getWriteAdapter()->delete($this->categoryTable, ['text_id = ?' => $object->getId()]);
        $this->_getWriteAdapter()->delete($this->filterTable, ['text_id = ?' => $object->getId()]);

        $insertTo = [];

        foreach ($categoryIds as $categoryId) {
            $insertTo[$this->categoryTable][] = [
                'text_id' => $object->getId(),
                'category_id' => $categoryId
            ];
        }

        foreach ($filters as $code => $values) {
            foreach (array_keys($values) as $valueCode) {
                $insertTo[$this->filterTable][] = [
                    'text_id' => $object->getId(),
                    'filter' => $code,
                    'value' => $valueCode
                ];
            }
        }

        foreach ($insertTo as $table => $data) {
            $this->_getWriteAdapter()->insertOnDuplicate($table, $data);
        }

        return parent::_afterSave($object);
    }

    /**
     * Finding SEO text
     *
     * @param $filterConditions
     * @param $filterNames
     * @param $categoryId
     * @param $storeId
     * @return bool|string
     */
    public function findTextId($filterConditions, $categoryId, $storeId)
    {
        $select = $this->_getReadAdapter()->select();
        $select->from($this->getTable('ecomdev_sphinxseo/index_text'), ['text_id', 'field', 'condition'])
            ->where('checksum IN(?)', array_map('crc32', $filterConditions))
            ->where('category_id = ?', $categoryId)
            ->where('store_id = ?', $storeId)
            ->having(sprintf('%s IN(?)', $this->_getReadAdapter()->quoteIdentifier('condition')), $filterConditions);

        $textIds = [];

        foreach ($this->_getReadAdapter()->query($select) as $row) {
            $textIds[$row['text_id']][$row['field']] = true;
        }

        if (!$textIds) {
            return false;
        }

        $select->reset();
        $select->from($this->getTable('ecomdev_sphinxseo/index_text'), ['text_id', 'field'])
            ->where('text_id IN(?)', array_keys($textIds))
            ->where('category_id = ?', $categoryId)
            ->where('store_id = ?', $storeId);

        foreach ($this->_getReadAdapter()->query($select) as $row) {
            if (!isset($textIds[$row['text_id']][$row['field']])) {
                unset($textIds[$row['text_id']]);
            }
        }

        if (!$textIds) {
            return false;
        }

        $select->reset();
        $select->from($this->getTable('ecomdev_sphinxseo/text'), ['text_id'])
            ->order('priority ASC')
            ->where('text_id IN(?)', array_keys($textIds))
            ->limit(1);

        return $this->_getReadAdapter()->fetchOne($select);
    }

}
