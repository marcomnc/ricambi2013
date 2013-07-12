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
        
        if ($product->getType() == Mage_Catalog_Model_Product_Type::TYPE_GROUPED) {
            foreach (Mage::getModel('rcatalog/position')->getCollection()->setFilterByProduct($product) as $link) {
                $this->_deleteLink($link->getId());
            }
        } else {
            $links = Mage::getModel('catalog/product_link')->getCollection();
            $links->getSelect()
                  ->where('linked_product_id = ?', $product->getId());

            foreach ($links as $link) {
                $positionLinks = Mage::getModel('rcatalog/position')
                                        ->getCollection()
                                        ->setFilterByLink($link->getLinkId());
                foreach ($positionLinks as $positionLink) {
                    $this->_deleteLink($positionLink->getId());
                }
            }
        }
        return $observer;
    }
    
    /**
     * Contorllo nel caso di raggruppato se è stato cancellato un link
     * @param type $observer
     */
    public function after_save($observer) {
        $product = $observer->getProduct();
        
        if ($product->hasSchemeLink()) {
            //Sono in salvataggio da controller del backend
            foreach ($product->getSchemeLink() as $link) {
                if (isset($link['state'])) {
                    if ($link['state'] == 'delete') {
                        $this->_deleteLink($link['id_link']);
                    } else {
                            $positionLink = Mage::getModel('rcatalog/position');
                            if ($link["state"] == 'create') {
                                $positionLink->setData('grouped_product_id', $product->getId());
                                $positionLink->setData('link_id', $link["linkid"]);

                                $positionLink->setData('position_x', ($link["x"] + 0));
                                $positionLink->setData('position_y', ($link["y"] + 0));                        
                            } else {
                                $positionLink->Load($link["id_link"]);
                                $positionLink->setData('position_x', ($link["x"] + 0));
                                $positionLink->setData('position_y', ($link["y"] + 0));
                            }
                            $positionLink->save();

                    }
                }
            }
        } 
        foreach (Mage::getModel('rcatalog/position')->getCollection()->setFilterByProduct($product) as $link) {


            $groupedLink = Mage::getModel('catalog/product_link')->Load($link->getLinkId());
            if (!$groupedLink->hasProductId()) {
                $this->_deleteLink($link->getId());
            }
        }

        
        return $observer;
        
    }
    
    /**
     * Assengo al prodotto un attributo fittizio SchemeLink che poi gestisco in fase di salvataggio
     * @param type $observer
     * @return type
     */
    public function prepare_save($observer) {
        
        $product = $observer->getProduct();
        $request = $observer->getRequest();
        
        if (($links=$request->getParam('associated-scheme','')) != '') {
            $link = Mage::Helper('core')->jsonDecode(base64_decode($links));
            $product->setData('scheme_link', $link);
            $observer->setProduct($product);
        }                
        
        return $observer;
    }
    
    private function _deleteLink($id){
        $link = Mage::getModel('rcatalog/position')->Load($id);
        $link->delete();
    }
            
}

?>
