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
class Ricambi_Catalog_Model_Product_Observer {

    
    /**
     * In fase di cancellazione dell'articolo se è raggruppato cancello anche 
     * i posizionemanti
     * @param type $observer
     */
    public function on_delete($observer) {
    
        $product = $observer->getProduct();
        
        foreach (Mage::getModel('rcatalog/position')->getCollection()->setFilterByProduct($product) as $link) {
            $this->_deleteLink($link->getId());
        }
        
        return $observer;
    }
    
    /**
     * Contorllo nel caso di raggruppato se è stato cancellato un link
     * @param type $observer
     */
    public function after_save($observer) {
        $product = $observer->getProduct();
        foreach (Mage::getModel('rcatalog/position')->getCollection()->setFilterByProduct($product) as $link) {
            $groupedLink = MAge::getModel('catalog/product_link')->Load($link->getLinkId());

            if (!$groupedLink->hasLinkedId()) {
                $this->_deleteLink($link->getId());
            }
        }
        
        return $observer;
        
    }
    
    private function _deleteLink($id){
        $link = Mage::getModel('rcatalog/position')->Load($id);
MAge::Log($link);
        $link->delete();
    }
            
}

?>
