<?php

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$table_faq = $installer->getConnection()
    ->newTable($installer->getTable('rcatalog/options'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'nullable'  => false,
        'auto_increment' => true,
        'primary'   => true,
        ), 'Entity ID')
    ->addColumn('link_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,        
        ), 'Title')
    ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,        
        ), 'Id del prodotto opzione')
    ->addColumn('sort_order', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => true,        
        ), 'Collegamento tabella link')
    ->addIndex($installer->getIdxName('rcatalog/options', array('link_id')),
        array('link_id'))
    ->addIndex($installer->getIdxName('rcatalog/options', array('entity_id'), Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX),
        array('entity_id'), array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX));
$installer->getConnection()->createTable($table_faq);

/*
 * Le relazioni le gestisco in fase di cancellazione 
 */

?>
