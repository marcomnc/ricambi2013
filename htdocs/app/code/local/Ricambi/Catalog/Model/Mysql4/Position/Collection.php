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
class Ricambi_Catalog_Model_Mysql4_Position_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract 
{    
    protected function _construct()
    {
        $this->_init('rcatalog/position');  
     
    }
    
    public function setFilterByProduct($product) {

        if (!$product instanceof Mage_Catalog_Model_Product) {
            if (is_numeric($product)) {
                $product = Mage::getModel('catalog/product')->Load($product);
            }
            else {
                $id = Mage::getModel('catalog/product')->getIdBySku($product);
                $product = Mage::getModel('catalog/product')->Load($id);
            }
        }
            
        $this->getSelect()->where('grouped_product_id = ?', $product->getId());
                
        return $this;
    }
    
    public function setFilterByLink($linkId) {
        $this->getSelect()->where('main_table.link_id = ?', $linkId);
        return $this;
    }


    /**
     * Arrichisco la collezione con lo sku del prodotto collegato e l'id e la posizione nel grouped
     */
    protected function _beforeLoad() {
        
        $this->getSelect()->joinLeft(array('_link' => Mage::getSingleton('core/resource')->getTableName('catalog_product_link')),
                                 '_link.link_id = main_table.link_id',
                                 null)
                          ->joinLeft(array('_attr' => Mage::getSingleton('core/resource')->getTableName('catalog_product_link_attribute')),
                                 "_attr.link_type_id = _link.link_type_id and product_link_attribute_code ='position'",
                                 null)
                          ->joinLeft(array('_attr_int' => Mage::getSingleton('core/resource')->getTableName('catalog_product_link_attribute_int')),
                                 "_attr_int.product_link_attribute_id = _attr.product_link_attribute_id and _attr_int.link_id = _link.link_id",
                                 array('pos' => '_attr_int.value'))
                          ->joinLeft(array('_link_prod' => Mage::getSingleton('core/resource')->getTableName('catalog_product_entity')),
                                 '_link.linked_product_id = _link_prod.entity_id',
                                 array('linked_proudct_sku' => '_link_prod.sku', 'linked_product_id' => '_link_prod.entity_id'));        
        return $this;
    }
    
    
}

?>
