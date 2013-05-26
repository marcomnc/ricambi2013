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
class Ricambi_Catalog_Block_Adminhtml_Widget_Grid_Column_Renderer_Objposition extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {
    
    
    protected $_values;

    /**
     * Renders grid column
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row)
    {
        //$pos = Mage::getModel('rcatalog/link')->getCollection($row->getData($this->getColumn()->getIndex()));
        
        $html  = '<span style="float:right" rel="0" class="counter" id="count_' . $row->getData($this->getColumn()->getIndex()) . '">';
        //$html .= (sizeof($pos) > 0) ? sizeof($pos) : $this->__('No');
        $html .= "</span>";
//        $html .= '<input type="hidden" ';
//        $html .= 'name="' . $this->getColumn()->getIndex() . '" ';
//        $html .= 'name="' . $this->getColumn()->getId() . '" ';
//        $html .= 'id="link_id_' . $row->getData($this->getColumn()->getIndex()) . '" '; 
//        $html .= 'value="' . base64_encode(Mage::Helper('core')->jsonEncode($pos)) . '"';
//        $html .= 'rel="' . $row->getData($this->getColumn()->getIndex()) . '" ';
//        $html .= 'class="input-text obj-position ' . $this->getColumn()->getInlineCss() . '"/>';
        $html .= '<div class="position-add-to" id="link_id_' . $row->getData($this->getColumn()->getIndex()) . '"></div>';
        return $html;
    }
}

?>
