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
 * Model per la gestione dell'attributo per la visualzizazione dei pallini
 * 
 */
class Ricambi_Catalog_Model_Link extends Mage_Core_Model_Abstract {
    
    protected $_imgAttribute = null;
    protected $_helper = null;
    
    public function _construct() {
        $this->_imgAttribute = Mage::getStoreConfig('catalog/ricambi/image_schema');        
        $this->_helper = Mage::Helper('rcatalog');
    }
    
    /**
     * Imposto il prodotto raggruppato
     * @param type $product
     */
    public function setProduct($product) {
        
        $this->_getProduct($product);
        
    }
    
    /**
     * Recupero la collection
     * Se non ci sono dati impostati ritorno FALSE
     * 
     * @return array Collection così strutturata
     * [id_link] => (
     *      [sku] => (
     *          [x] => "x position",
     *          [y] => "y position",
     *      )
     * )
     */
    public function getCollection() {
        
        
        
    }
    
    
    /**
     * leggo / setto il prodoto
     * @param type $product
     * @return type
     */
    private function _getProduct($product = null) {
        
        if (!is_null($product)) {
            $this->setData('product', $product);
        } else {
            if (is_null($this->getData('product'))) {
                $this->setData('product', $this->_helper->getCurrentProduct());
            }
        }
        
        return $this->getData('product');
    }
    
}

?>
