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
class Ricambi_Catalog_Model_Mysql4_Options extends Mage_Core_Model_Mysql4_Abstract
{
    
    protected function _construct()
    {
        $this->_init('rcatalog/options', 'entity_id');
    }
    
    /**
     * Azione eseguita dopo la lettura e prima della lettura a livello di modello
     * @param Mage_Core_Model_Abstract $object
     */
    protected function _afterLoad(Mage_Core_Model_Abstract $object) {

        $object->setData('fater_product', null);
        $object->setData('child_product', null);
        
        //Arrichisco l'oggetto con il prodotto Filgio
        if ($object->hasProductId() && $object->getProductId() > 0) {
            $object->setData('child_product', Mage::getModel('catalog/product')->Load($object->getProductId()));
        } 
        //Arrichisco l'oggetto con il prodotto Padre
        if ($object->hasLinkId() && $object->getLinkId() > 0) {
            $link = Mage::getModel('catalog/product_link')->Load($object->getLinkId());
            if ($link->hasLinkedProductId() && $link->getLinkedProductId() > 0) {
                $object->setData('fater_product', Mage::getModel('catalog/product')->Load($link->getLinkedProductId()));
            }
        }
        parent::_afterLoad($object);
    }
    
    /**
     * Azione eseguita prima del salvataggio
     * @param Mage_Core_Model_Abstract $object
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object) {

        parent::_beforeSave($object);
    } 
    
    
    /**
     * Azioone eseguita dopo il salvataggio
     * @param Mage_Core_Model_Abstract $object
     */
    protected function _afterSave(Mage_Core_Model_Abstract $object) {
        parent::_afterSave($object);
    }
    
}

?>
