<?php

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

//Attributo in cui memorizzo le posizioni dei prodotti.
//Non è necessario che sia visibile in frontend/backend
$this->addAttribute('product', 'grouped_link', array(
    'type' => 'text',
    'input' => 'text',
    'label' => 'Grouped Product Link',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible' => 0,
    'required' => 0,
    'user_defined' => 1,
    'default' => '0',
    'visible_on_front' => 0,
));

$installer->endSetup();

?>
