<?php

/** @var Mage_Core_Model_Resource_Setup $this */

$this->startSetup();

$this->getConnection()->modifyColumn(
    $this->getTable('ecomdev_sphinxseo/text'),
    'text_id',
    [
        'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'unsigned' => true,
        'primary' => true,
        'identity' => true,
        'nullable' => false
    ]
);


$this->getConnection()->dropTable($this->getTable('ecomdev_sphinxseo/text_filter'));
$this->getConnection()->dropTable($this->getTable('ecomdev_sphinxseo/text_category'));

$table = $this->getConnection()->newTable($this->getTable('ecomdev_sphinxseo/text_filter'));

$table->addColumn('text_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, ['primary' => true, 'unsigned' => true, 'nullable' => false])
    ->addColumn('filter', Varien_Db_Ddl_Table::TYPE_TEXT, 255, ['primary' => true, 'nullable' => false])
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_TEXT, 255, ['primary' => true, 'nullable' => false])
    ->addForeignKey(
        $this->getFkName('ecomdev_sphinxseo/text_filter', 'text_id', 'ecomdev_sphinxseo/text', 'text_id'),
        'text_id',
        $this->getTable('ecomdev_sphinxseo/text'),
        'text_id',
        Varien_Db_Adapter_Interface::FK_ACTION_CASCADE,
        Varien_Db_Adapter_Interface::FK_ACTION_CASCADE
    )
;

$this->getConnection()->createTable($table);

$table = $this->getConnection()->newTable($this->getTable('ecomdev_sphinxseo/text_category'));

$table->addColumn('text_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, ['primary' => true, 'unsigned' => true, 'nullable' => false])
    ->addColumn('category_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, ['primary' => true, 'unsigned' => true, 'nullable' => false])
    ->addForeignKey(
        $this->getFkName('ecomdev_sphinxseo/text_category', 'category_id', 'catalog/category', 'entity_id'),
        'category_id',
        $this->getTable('catalog/category'),
        'entity_id',
        Varien_Db_Adapter_Interface::FK_ACTION_CASCADE,
        Varien_Db_Adapter_Interface::FK_ACTION_CASCADE
    )
    ->addForeignKey(
        $this->getFkName('ecomdev_sphinxseo/text_category', 'text_id', 'ecomdev_sphinxseo/text', 'text_id'),
        'text_id',
        $this->getTable('ecomdev_sphinxseo/text'),
        'text_id',
        Varien_Db_Adapter_Interface::FK_ACTION_CASCADE,
        Varien_Db_Adapter_Interface::FK_ACTION_CASCADE
    )
;

$this->getConnection()->createTable($table);


$this->endSetup();
