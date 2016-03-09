<?php

/** @var $this Mage_Core_Model_Resource_Setup */

$this->startSetup();

$this->getConnection()->dropTable($this->getTable('ecomdev_sphinxseo/index_text'));

$table = $this->getConnection()->newTable($this->getTable('ecomdev_sphinxseo/index_text'));

$table
    ->addColumn('index_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, ['primary' => true, 'identity' => true, 'nullable' => false])
    ->addColumn('text_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, ['unsigned' => true, 'nullable' => false])
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, 5, ['unsigned' => true, 'nullable' => false])
    ->addColumn('category_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, ['unsigned' => true, 'nullable' => false])
    ->addColumn('checksum', Varien_Db_Ddl_Table::TYPE_INTEGER, null, ['unsigned' => true, 'nullable' => false])
    ->addColumn('field', Varien_Db_Ddl_Table::TYPE_TEXT, 255, ['nullable' => false])
    ->addColumn('condition', Varien_Db_Ddl_Table::TYPE_TEXT, 255, ['nullable' => false])
    ->addIndex(
        $this->getIdxName('ecomdev_sphinxseo/index_text', ['store_id', 'category_id', 'checksum']),
        ['store_id', 'category_id', 'checksum']
    )
    ->addIndex(
        $this->getIdxName('ecomdev_sphinxseo/index_text', ['store_id', 'category_id', 'checksum']),
        ['store_id', 'category_id', 'field']
    )
    ->addForeignKey(
        $this->getFkName('ecomdev_sphinxseo/index_text', 'text_id', 'ecomdev_sphinxseo/text', 'text_id'),
        'text_id',
        $this->getTable('ecomdev_sphinxseo/text'),
        'text_id',
        Varien_Db_Adapter_Interface::FK_ACTION_CASCADE,
        Varien_Db_Adapter_Interface::FK_ACTION_CASCADE
    )
;

$this->getConnection()->createTable($table);

$this->endSetup();
