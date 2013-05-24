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
        return $this;
    }
    
    public function getProductCollection ($attributeToSelect = array()) {
        
        
        $collection = Mage::getModel('catalog/product')->getCollection();
        foreach ($attributeToSelect as $attribute) {
            $collection->addAttributeToSelect($attribute);
        }
        $collection->getSelect()
                   ->join(array('_link' => Mage::getSingleton('core/resource')->getTableName('catalog_product_link')),
                          '_link.linked_product_id = e.entity_id',
                           array('link' => 'link_id'))
                   ->join(array('_attr' => Mage::getSingleton('core/resource')->getTableName('catalog_product_link_attribute')),
                          "_attr.link_type_id = _link.link_type_id and product_link_attribute_code ='position'",
                          null)
                   ->join(array('_attr_int' => Mage::getSingleton('core/resource')->getTableName('catalog_product_link_attribute_int')),
                              "_attr_int.product_link_attribute_id = _attr.product_link_attribute_id and _attr_int.link_id = _link.link_id",
                             array('pos' => 'value'))
                   ->where('_link.link_type_id = ?', Mage_Catalog_Model_Product_Link::LINK_TYPE_GROUPED)
                   ->where('_link.product_id = ?', $this->_getProduct()->getId())
                   ->order('_attr_int.value');
        
        return $collection;
    }
    
    /**
     * Recupero la collection
     * Se non ci sono dati impostati ritorno FALSE
     * 
     * @return array Collection così strutturata
     * [id_link] => (
     *      [id]    => "Id Articolo"
     *      [sku]   => "Codice Articolo"
     *      [nome]  => "Nome Articolo"
     *      [pos]   => "Posizione del grouped"
     *      [id]    => "Link Id"
     *      [x]     => "x position",
     *      [y]     => "y position",
     *      )
     * )
     * @param int $linkId eventuale linkid per cui fare il filtro
     */
    public function getCollection($linkId = 0) {
        
        $collection = array();
        
        $links = Mage::getModel('rcatalog/position')->getCollection()->setFilterByProduct($this->_getProduct());
        if ($linkId != 0) {
            $links->getSelect()->where('link_id = ?', $linkId);
        }
        
        foreach ($links as $link) {
            $l = Mage::getModel('rcatalog/position')->Load($link->getId());

            $productLink = array(
                'id'    => $l->getLinkedProduct()->getId(),
                'sku'   => $l->getLinkedProduct()->getSku(),
                'name'  => $l->getLinkedProduct()->getName(),
                'pos'   => $link->getPos(),
                'id'    => $l->getLinkId(),
                'x'     => $l->getPositionX(),
                'y'     => $l->getPositionY(),
            );
            $collection[$link->getId()] = $productLink;
        }
        
        return $collection;
    }
    
    public function getCollectionJson($linkId = 0) {
        return Mage::Helper('core')->jsonEncode($this->getCollection($linkId));
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
    
    private function _getGroupedLink() {
        
        return unserialize($this->_getProduct()->getGroupedLink());
        
    }
    
    
    
}

?>
