<?php
$this->startSetup();

$this->addAttribute('customer', 'date_rejected', array(
    'type' => 'datetime',
    'input' => 'date',
    'label' => 'Data Rifiuto',
    'global' => 1,
    'visible' => 1,
    'required' => 0,
    'user_defined' => 1,
    'visible_on_front' => 0,
));


$this->updateAttribute('customer', 'date_rejected', 'frontend_input', 'date');
$this->updateAttribute('customer', 'date_rejected', 'backend_model', 'eav/entity_attribute_backend_datetime');

$customer = Mage::getModel('customer/customer');
$attrSetId = $customer->getResource()->getEntityType()->getDefaultAttributeSetId();

$this->addAttributeToSet('customer', $attrSetId, 'General', 'date_rejected');

if (version_compare(Mage::getVersion(), '1.4.2', '>=')) {
    Mage::getSingleton('eav/config')
            ->getAttribute('customer', 'date_rejected')
            ->setData('used_in_forms', array('adminhtml_customer'))
            ->save();
}



$this->endSetup();

