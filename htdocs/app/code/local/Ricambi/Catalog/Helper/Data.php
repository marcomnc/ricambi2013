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
class Ricambi_Catalog_Helper_Data extends Mage_Core_Helper_Abstract {
    
    const LINK_ATTRIBUTE = 'grouped_link';
    
    protected  $_schemaAttribute = null;
    
    public function __construct() {
        $this->_schemaAttribute = Mage::getStoreConfig('catalog/ricambi/image_scheme');
    }


    /**
     * Verifica che sia una immagine
     * @param type $string
     */
    public function IsNotImage($string) {
        $string = $string . '';
        return ($string == '' || $string == 'no_image');
    }
    
    /**
     * Retrieve currently edited product object
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getCurrentProduct()
    {
        return Mage::registry('current_product');
    }
    
    public function getSchemaAttribute() {
        return $this->_schemaAttribute;
    }
    
    /**
     * 
     * @param type $from admin/frontend
     * @return type
     */
    public function getImage($product = null, $from = 'admin') {
        if (is_null($product)) {
            $product = $this->getCurrentProduct();
        }
        return Mage::helper('catalog/image')->init($product, $this->_schemaAttribute);   
    }
    
    
}

?>
