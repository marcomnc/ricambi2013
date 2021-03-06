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
class Ricambi_Catalog_PrintController extends Mage_Core_Controller_Front_Action {
    
    public function groupedAction() {
        
        $productId = $this->getRequest()->getParams('id', "");
        if ($productId != "") {
            $product = Mage::getModel('catalog/product')->Load($productId);
            Mage::register("print_product", $product);
        }
        
        $this->loadLayout();
        $this->renderLayout();
    }
    
    
    public function testAction() 
    {
        $collection = Mage::getModel('rcatalog/options')->getCollection();
        
        echo "<pre>";
        foreach ($collection as $c) {
            print_r($c);
        }
        die();
    }
}
?>
