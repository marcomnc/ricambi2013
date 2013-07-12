<?php

/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *  
 *
 * @category    
 * @package        
 * @copyright   Copyright (c) 2013 Mps Sistemi (http://www.mps-sistemi.it)
 * @author      MPS Sistemi S.a.s - Marco Mancinelli <marco.mancinelli@mps-sistemi.it>
 *
 */
class Ricambi_Catalog_Model_Mysql4_Options_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract 
{    
    
    protected $_filterByProduct = false;
    protected function _construct()
    {
        $this->_init('rcatalog/options');  
     
    }
    
    public function setFilterByProduct($productGrouped, $productAssociated = null) {

        $productGrouped = $this->_getProduct($productGrouped);
        
        $this->getSelect()->join(array('_link' => Mage::getSingleton('core/resource')->getTableName('catalog_product_link')),
                                 '_link.link_id = main_table.link_id',
                                 null)
                          ->where('_link.product_id = ?', $productGrouped->getId());
        if (!is_null($productAssociated)) {
            
            $productAssociated = $this->_getProduct($productAssociated);
            
            $this->getSelect()->where('_link.linked_product_id = ?', $productAssociated->getId());
        }
                          
        
        $this->_filterByProduct = true;
                
        return $this;
    }
    
    /**
     * Genera una strutta del tipo 
     * [grouperProductId] =>
     *      [SparePartsProductId] => 
     *          [OptionsProductId] 
     */
    public function toStruct() {
        
        $struct = array();
        
        foreach ($this as $record) {
            
            $struct["Product"][$record->getData('grouped_product_id')]["Spare"][$record->getData('associates_product_id')][] = $record->getData('product_id');
        }
                
        return $struct;
        
    }
    
    /**
     * Arrichisco la collezione con lo sku del prodotto collegato e l'id e la posizione nel grouped
     */
    protected function _beforeLoad() {
        
        if (!$this->_filterByProduct) {
            $this->getSelect()->join(array('_link' => Mage::getSingleton('core/resource')->getTableName('catalog_product_link')),
                                     '_link.link_id = main_table.link_id',
                                     null);
        }
        $this->getSelect()->join(array('_prod_opt' => Mage::getSingleton('core/resource')->getTableName('catalog_product_entity')),
                                 'main_table.product_id = _prod_opt.entity_id',
                                 array('proudct_sku' => '_prod_opt.sku'))
                          ->join(array('_link_prod_ass' => Mage::getSingleton('core/resource')->getTableName('catalog_product_entity')),
                                 '_link.linked_product_id = _link_prod_ass.entity_id',
                                 array('associates_proudct_sku' => '_link_prod_ass.sku', 'associates_product_id' => '_link_prod_ass.entity_id'))
                          ->join(array('_link_prod_grouped' => Mage::getSingleton('core/resource')->getTableName('catalog_product_entity')),
                                 '_link.product_id = _link_prod_grouped.entity_id',
                                 array('grouped_proudct_sku' => '_link_prod_grouped.sku', 'grouped_product_id' => '_link_prod_grouped.entity_id'));
        return $this;
    }
    
    private function _getProduct($product) {
        
        if (!$product instanceof Mage_Catalog_Model_Product) {
            if (is_numeric($product)) {
                $product = Mage::getModel('catalog/product')->Load($product);
            }
            else {
                $id = Mage::getModel('catalog/product')->getIdBySku($product);
                $product = Mage::getModel('catalog/product')->Load($id);
            }
        }
        
        return $product;
        
    }
    
}

?>