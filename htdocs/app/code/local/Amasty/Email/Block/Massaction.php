<?php
/**
 * @author Amasty
 */ 
class Amasty_Email_Block_Massaction extends Mage_Adminhtml_Block_Widget_Grid_Massaction
{
    protected function _beforeToHtml()
    {
        $this->addItem('amemail', array(
            'label' => Mage::helper('amemail')->__('Email to Customers'),
            'url'   => $this->getUrl('amemail/adminhtml_index/index', array())
        ));

        return parent::_beforeToHtml();
    }
}