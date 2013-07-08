<?php
/**
 * Unicode Systems
 * @category   Uni
 * @package    Uni_Fileuploader
 * @copyright  Copyright (c) 2010-2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
class Uni_Fileuploader_Model_Fileuploader extends Mage_Core_Model_Abstract {

    
    protected $_labels;
    
    public function _construct() {
        parent::_construct();
        $this->_init('fileuploader/fileuploader');
    }

    public function getAllAvailProductIds(){
        $collection = Mage::getResourceModel('catalog/product_collection')
                        ->getAllIds();
        return $collection;
    }

    public function getFilesByProductId($productId) {
        $data = array();
        $collection = Mage::getResourceModel('fileuploader/fileuploader_collection');
        $collection->addFieldToFilter('product_ids', array('finset' => $productId))        
                ->addFieldToFilter('file_status', 1);
        $collection->getSelect()->order('sort_order');
        return $collection->toArray();
    }
    
    
    protected function _afterSave() {
        if ($this->hasStoreLabels()) {
            $this->_getResource()->saveStoreLabels($this->getId(), $this->getStoreLabels());
        }
        parent::_afterSave();
    }




    public function getStoreLabels()
    {
        if (!$this->hasStoreLabels()) {
            $labels = $this->_getResource()->getStoreLabels($this->getId());
            $this->setStoreLabels($labels);
        }
        return $this->_getData('store_labels');
    }
    
    public function getStoreLabel($returnDescription=false) {
        $storeId = Mage::app()->getStore()->getId();
        if ($this->hasStoreLabels()) {
            $labels = $this->_getData('store_labels');
            if (isset($labels[$storeId])) {
                return $labels[$storeId];
            } elseif ($labels[0]) {
                return $labels[0];
            } elseif ($returnDescription) {
                return $this->getData('title');
            } else {
                return false;
            }
        } elseif (!isset($this->_labels[$storeId])) {
            $this->_labels[$storeId] = $this->_getResource()->getStoreLabel($this->getId(), $storeId);
        }
        return $this->_labels[$storeId];        
    }

}