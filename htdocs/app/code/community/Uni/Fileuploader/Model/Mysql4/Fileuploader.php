<?php
/**
 * Unicode Systems
 * @category   Uni
 * @package    Uni_Fileuploader
 * @copyright  Copyright (c) 2010-2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
class Uni_Fileuploader_Model_Mysql4_Fileuploader extends Mage_Core_Model_Mysql4_Abstract {

    public function _construct() {
        $this->_init('fileuploader/fileuploader', 'fileuploader_id');
    }
    
    public function getStoreLabels($fileUploaderId)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getTable('fileuploader/label'), array('store_id', 'value'))
            ->where('fileuploader_id=?', $fileUploaderId);
        return $this->_getReadAdapter()->fetchPairs($select);
    }
    
    public function getStoreLabel($fileUploaderId, $storeId)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getTable('mpspricezone/pricezonelabel'), array('value'))
            ->where('fileuploader_id=?', $fileUploaderId)
            ->where('store_id in (?)', array($storeId, 0))
            ->order('store_id desc');
        return $this->_getReadAdapter()->fetchOne($select);
    }
    
    public function saveStoreLabels($fileUploaderId, $labels){
        $delete = array();
        $save = array();
        $table = $this->getTable('fileuploader/label');
        $adapter = $this->_getWriteAdapter();

        foreach ($labels as $storeId => $label) {
            if (Mage::helper('core/string')->strlen($label)) {
                $data = array('fileuploader_id' => $fileUploaderId, 'store_id' => $storeId, 'value' => $label);
                $adapter->insertOnDuplicate($table, $data, array('value'));
            } else {
                $delete[] = $storeId;
            }
        }      
        if (!empty($delete)) {
            $adapter->delete($table,
                $adapter->quoteInto('fileuploader_id=? AND ', $fileUploaderId) . $adapter->quoteInto('store_id IN (?)', $delete)
            );
        }
        return $this;
        
    }    
}