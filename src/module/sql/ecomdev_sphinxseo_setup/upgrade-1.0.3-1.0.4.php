<?php

/** @var $this Mage_Core_Model_Resource_Setup */

$this->startSetup();

$table = $this->getConnection()->newTable($this->getTable('ecomdev_sphinxseo/text_url'));

$table
    ->addColumn('text_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, ['unsigned' => true, 'nullable' => false])
    ->addColumn('url_slug', Varien_Db_Ddl_Table::TYPE_TEXT, 255, ['nullable' => false])
    ->addColumn('checksum', Varien_Db_Ddl_Table::TYPE_INTEGER, null, ['unsigned' => false, 'nullable' => false])
    ->addColumn('category_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, ['unsigned' => false, 'nullable' => false])
    ->addIndex(
        $this->getIdxName(
            'ecomdev_sphinxseo/text_url',
            ['url_slug'],
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        ['url_slug'],
        ['type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE]
    )
    ->addIndex(
        $this->getIdxName('ecomdev_sphinxseo/text_url', ['checksum']),
        ['checksum']
    )
    ->addIndex(
        $this->getIdxName('ecomdev_sphinxseo/text_url', ['category_id']),
        ['category_id']
    )
    ->addForeignKey(
        $this->getFkName('ecomdev_sphinxseo/text_url', 'text_id', 'ecomdev_sphinxseo/text', 'text_id'),
        'text_id',
        $this->getTable('ecomdev_sphinxseo/text'),
        'text_id',
        Varien_Db_Adapter_Interface::FK_ACTION_CASCADE,
        Varien_Db_Adapter_Interface::FK_ACTION_CASCADE
    )
;

$this->getConnection()->createTable($table);

$this->endSetup();
