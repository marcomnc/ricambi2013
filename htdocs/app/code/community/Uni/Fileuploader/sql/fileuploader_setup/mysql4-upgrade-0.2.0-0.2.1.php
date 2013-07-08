<?php

/**
 * Unicode Systems
 * @category   Uni
 * @package    Uni_Fileuploader
 * @copyright  Copyright (c) 2010-2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

$installer = $this;
$installer->startSetup();

$table_label = $installer->getConnection()
    ->newTable($installer->getTable('fileuploader/label'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'nullable'  => false,
        'auto_increment' => true,
        'primary'   => true,
        ), 'Entity ID')
        
    ->addColumn('fileuploader_id', Varien_Db_Ddl_Table::TYPE_BIGINT, 20, array(
        'nullable'      => false,        
        ), 'Attachment ID')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, 5, array(
        'nullable'      => false,        
        ), 'Store ID')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable'  => true, 
        'default'   => '',
        ), 'Label store Value')
    ->addIndex(
        $installer->getIdxName(
            'fileuploader/label',
            array('fileuploader_id'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX
        ),
        array('fileuploader_id'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX))
    ->addIndex(
        $installer->getIdxName(
            'fileuploader/label',
            array('store_id'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX
        ),
        array('store_id',),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX))
    ->addIndex(
        $installer->getIdxName(
            'fileuploader/label',
            array('fileuploader_id', 'store_id'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX
        ),
        array('fileuploader_id', 'store_id'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX))
    ->addForeignKey($installer->getFkName('fileuploader/label', 'fileuploader_id', 'fileuploader/fileuploader', 'fileuploader_id'),
        'fileuploader_id', $installer->getTable('fileuploader/fileuploader'), 'fileuploader_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('fileuploader/label', 'store_id', 'core/store', 'store_id'),
        'store_id', $installer->getTable('core/store'), 'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE);

$installer->getConnection()->createTable($table_label);

$installer->endSetup();
