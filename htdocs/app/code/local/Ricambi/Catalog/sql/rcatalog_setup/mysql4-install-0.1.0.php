<?php

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

//Attributo in cui memorizzo le posizioni dei prodotti.
//Non è necessario che sia visibile in frontend/backend
//@deprecate   viene utilizzata la tabella
//$this->addAttribute('product', 'grouped_link', array(
//    'type' => 'text',
//    'input' => 'text',
//    'label' => 'Grouped Product Link',
//    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
//    'visible' => 0,
//    'required' => 0,
//    'user_defined' => 1,
//    'default' => '0',
//    'visible_on_front' => 0,
//));

$table_position = $installer->getConnection()
    ->newTable($installer->getTable('rcatalog/position'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'nullable'  => false,
        'auto_increment' => true,
        'primary'   => true,
        ), 'Entity ID')
        
    ->addColumn('grouped_product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
        'nullable'      => false,        
        ), 'Product Grouped ID')
    ->addColumn('link_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
        'nullable'      => false,        
        ), 'Product linked ID')
    ->addColumn('position_x', Varien_Db_Ddl_Table::TYPE_INTEGER, 6, array(
        'nullable'  => true, 
        'default'   => 0,
        ), 'X Position')
    ->addColumn('position_y', Varien_Db_Ddl_Table::TYPE_INTEGER, 6, array(
        'nullable'  => true, 
        'default'   => 0,
        ), 'Y Position')
    ->addIndex(
        $installer->getIdxName(
            'rcatalog/position',
            array('grouped_product_id', 'link_id'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX
        ),
        array('grouped_product_id', 'link_id'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX));

$installer->getConnection()->createTable($table_position);

/* Non rifaccio la chiave, faccio i controlli in fase di salvataggio articolo
$this->getConnection()->addForeignKey($installer->getFkName('catalog/product_link', 'link_id', 'rcatalog/position', 'link_id'),
        $this->getTable('catalog/product_link'), 'link_id', $this->getTable('rcatalog/position'), 'link_id', 
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE);
 * 
 */

$installer->endSetup();

?>
