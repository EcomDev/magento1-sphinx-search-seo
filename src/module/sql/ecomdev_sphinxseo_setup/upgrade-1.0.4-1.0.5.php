<?php

/** @var $this Mage_Core_Model_Resource_Setup */

$this->startSetup();

$this->getConnection()->modifyColumn(
    $this->getTable('ecomdev_sphinxseo/text_url'),
    'checksum',
    [
        'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'unsigned' => true,
        'nullable' => false
    ]
);

$this->getConnection()->modifyColumn(
    $this->getTable('ecomdev_sphinxseo/text_url'),
    'category_id',
    [
        'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'unsigned' => true,
        'nullable' => false
    ]
);

$this->endSetup();
