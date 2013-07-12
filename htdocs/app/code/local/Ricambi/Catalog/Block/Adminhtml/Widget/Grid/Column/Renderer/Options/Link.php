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
class Ricambi_Catalog_Block_Adminhtml_Widget_Grid_Column_Renderer_Options_Link extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {
    
    
    protected $_values;

    
    public function render(Varien_Object $row) {
        
       $groupedProduct = Mage::registry('current_product');
       
       $html = '<a ';
       $html .= 'rel="' . base64_encode(Mage::helper('core')->jsonEncode(array('Associated' => $row->getEntityId(), 'SpareParts' => $groupedProduct->getId()))) . '" ';
       $html .= 'class="associated-product" href="';
       $html .= Mage::helper("adminhtml")
                        ->getUrl('radmincatalog/adminhtml_product/selectoptiongrid/', 
                                  array('ass_id' => $row->getEntityId(),
                                        'grp_id' => $groupedProduct->getId(),
                                        'link_id' => $row->getPosition())); 
       $html .= '">';
       $html .= Mage::helper('rcatalog')->__('Associa');
       $html .= "</a>";
               
        return $html;
    }
}

?>