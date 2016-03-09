<?php

/** @var Mage_Core_Model_Resource_Setup $this */

$this->startSetup();

$this->getConnection()->addColumn(
    $this->getTable('ecomdev_sphinxseo/text'),
    'name',
    [
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => 255,
        'nullable' => false,
        'comment' => 'Internal name of the text'
    ]
);

$this->getConnection()->addColumn(
    $this->getTable('ecomdev_sphinxseo/text'),
    'is_active',
    [
        'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'length' => 1,
        'unsigned' => true,
        'nullable' => false,
        'comment' => 'Flag for activity of the model'
    ]
);

$this->getConnection()->addIndex(
    $this->getTable('ecomdev_sphinxseo/text'),
    $this->getIdxName('ecomdev_sphinxseo/text', 'is_active'),
    'is_active'
);

$this->getConnection()->addColumn(
    $this->getTable('ecomdev_sphinxseo/index_text'),
    'checksum',
    [
        'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'nullable' => false,
        'comment' => 'Checksum for text'
    ]
);

$this->getConnection()->addIndex(
    $this->getTable('ecomdev_sphinxseo/index_text'),
    $this->getIdxName('ecomdev_sphinxseo/index_text', ['store_id', 'category_id', 'checksum']),
    ['store_id', 'category_id', 'checksum']
);

$this->getConnection()->addColumn(
    $this->getTable('ecomdev_sphinxseo/index_url'),
    'checksum',
    [
        'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'nullable' => false,
        'unsigned' => true,
        'comment' => 'Checksum for url'
    ]
);

$this->getConnection()->addIndex(
    $this->getTable('ecomdev_sphinxseo/index_url'),
    $this->getIdxName('ecomdev_sphinxseo/index_text', ['store_id', 'checksum']),
    ['store_id', 'checksum']
);

$this->endSetup();
