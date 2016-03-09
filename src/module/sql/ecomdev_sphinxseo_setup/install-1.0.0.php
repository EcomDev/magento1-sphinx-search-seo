<?php

use Varien_Db_Ddl_Table as Table;
use Varien_Db_Adapter_Interface as DbInterface;

/** @var $this Mage_Core_Model_Resource_Setup */
$this->startSetup();

$table = $this->getConnection()->newTable($this->getTable('ecomdev_sphinxseo/option_url'));

$table
    ->addColumn(
        'option_id',
        Table::TYPE_INTEGER,
        null,
        ['primary' => true, 'nullable' => false, 'unsigned' => true]
    )
    ->addColumn(
        'store_id',
        Table::TYPE_SMALLINT,
        null,
        ['primary' => true, 'nullable' => false, 'unsigned' => true]
    )
    ->addColumn(
        'slug',
        Table::TYPE_TEXT,
        255,
        ['nullable' => false]
    )
    ->addIndex(
        $this->getIdxName('ecomdev_sphinxseo/option_url', ['store_id', 'slug'], DbInterface::INDEX_TYPE_UNIQUE),
        ['store_id', 'slug'],
        ['type' => DbInterface::INDEX_TYPE_UNIQUE]
    )
    ->addForeignKey(
        $this->getFkName('ecomdev_sphinxseo/option_url', 'store_id', 'core/store', 'store_id'),
        'store_id',
        $this->getTable('core/store'),
        'store_id'
    )
    ->addForeignKey(
        $this->getFkName('ecomdev_sphinxseo/option_url', 'option_id', 'eav/attribute_option', 'option_id'),
        'store_id',
        $this->getTable('core/store'),
        'store_id'
    );

$this->getConnection()->createTable($table);

$table = $this->getConnection()->newTable($this->getTable('ecomdev_sphinxseo/index_url'));

$table
    ->addColumn(
        'store_id',
        Table::TYPE_SMALLINT,
        null,
        ['primary' => true, 'nullable' => false, 'unsigned' => true]
    )
    ->addColumn(
        'slug',
        Table::TYPE_TEXT,
        255,
        ['primary' => true, 'nullable' => false]
    )
    ->addColumn(
        'type',
        Table::TYPE_VARCHAR,
        255,
        ['nullable' => false]
    )
    ->addColumn(
        'id',
        Table::TYPE_INTEGER,
        null,
        ['nullable' => false]
    )
    ->addColumn(
        'filter',
        Table::TYPE_TEXT,
        255,
        ['nullable' => false]
    )
    ->addColumn(
        'value',
        Table::TYPE_TEXT,
        255,
        ['nullable' => false]
    )
    ->addColumn(
        'position',
        Table::TYPE_INTEGER,
        null,
        ['nullable' => false, 'default' => 100]
    )
    ->addIndex(
        $this->getIdxName('ecomdev_sphinxseo/index_url', ['type', 'id', 'store_id']),
        ['type', 'id', 'store_id']
    )
    ->addIndex(
        $this->getIdxName('ecomdev_sphinxseo/index_url', ['store_id', 'filter']),
        ['store_id', 'filter']
    )
    ->addIndex(
        $this->getIdxName('ecomdev_sphinxseo/index_url', ['position']),
        ['position']
    )
;

$this->getConnection()->createTable($table);

$table = $this->getConnection()->newTable($this->getTable('ecomdev_sphinxseo/text'));

$table->addColumn('text_id', Table::TYPE_INTEGER, null, ['primary' => true, 'unsigned' => true, 'nullable' => false])
    ->addColumn('store_id', Table::TYPE_SMALLINT, null)
    ->addColumn('priority', Table::TYPE_INTEGER, null, ['nullable' => false, 'default' => '100'])
    ->addColumn('values', Table::TYPE_TEXT, '256k', ['nullable' => false])
    ->addIndex(
        $this->getIdxName('ecomdev_sphinxseo/text', 'priority'),
        'priority'
    )
;

$this->getConnection()->createTable($table);

$table = $this->getConnection()->newTable($this->getTable('ecomdev_sphinxseo/text_category'));

$table->addColumn('text_id', Table::TYPE_INTEGER, null, ['primary' => true, 'unsigned' => true, 'nullable' => false])
    ->addColumn('category_id', Table::TYPE_INTEGER, null, ['primary' => true, 'unsigned' => true, 'nullable' => false])
    ->addForeignKey(
        $this->getFkName('ecomdev_sphinxseo/text_category', 'category_id', 'catalog/category', 'entity_id'),
        'category_id',
        $this->getTable('catalog/category'),
        'entity_id'
    )
    ->addForeignKey(
        $this->getFkName('ecomdev_sphinxseo/text_category', 'text_id', 'ecomdev_sphinxseo/text', 'text_id'),
        'text_id',
        $this->getTable('ecomdev_sphinxseo/text'),
        'text_id'
    )
;


$this->getConnection()->createTable($table);

$table = $this->getConnection()->newTable($this->getTable('ecomdev_sphinxseo/text_filter'));

$table->addColumn('text_id', Table::TYPE_INTEGER, null, ['primary' => true, 'unsigned' => true, 'nullable' => false])
    ->addColumn('filter', Table::TYPE_TEXT, 255, ['primary' => true, 'unsigned' => true, 'nullable' => false])
    ->addColumn('value', Table::TYPE_TEXT, 255, ['primary' => true, 'unsigned' => true, 'nullable' => false])
    ->addForeignKey(
        $this->getFkName('ecomdev_sphinxseo/text_filter', 'text_id', 'ecomdev_sphinxseo/text', 'text_id'),
        'text_id',
        $this->getTable('ecomdev_sphinxseo/text'),
        'text_id'
    )
;

$this->getConnection()->createTable($table);

$table = $this->getConnection()->newTable($this->getTable('ecomdev_sphinxseo/index_text'));

$table->addColumn('text_id', Table::TYPE_INTEGER, null, ['primary' => true, 'unsigned' => true, 'nullable' => false])
    ->addColumn('store_id', Table::TYPE_SMALLINT, 5, ['primary' => true, 'unsigned' => true, 'nullable' => false])
    ->addColumn('category_id', Table::TYPE_INTEGER, null, ['primary' => true, 'unsigned' => true, 'nullable' => false])
    ->addColumn('condition', Table::TYPE_TEXT, 255, ['nullable' => false])
    ->addForeignKey(
        $this->getFkName('ecomdev_sphinxseo/index_text', 'text_id', 'ecomdev_sphinxseo/text', 'text_id'),
        'text_id',
        $this->getTable('ecomdev_sphinxseo/text'),
        'text_id'
    )
;

$this->getConnection()->createTable($table);

$this->endSetup();
