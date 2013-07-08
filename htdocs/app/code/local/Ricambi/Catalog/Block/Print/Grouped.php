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

class Ricambi_Catalog_Block_Print_Grouped extends Mage_Core_Block_Template 
{
    protected $_curProd = null;
    
    public function _construct() {        
        parent::_construct();
    }
    
    public function getProduct() {
        return Mage::registry("print_product");        
    }
    
    public function setProduct4Render() {
        $this->_curProd = Mage::registry('product');
        Mage::unregister('product');
        Mage::register('product', $this->getProduct());                
    }
    
    public function unsProduct4Render() {
        Mage::unregister('product');
        Mage::register('product', $this->_curProd);                
    }
}

?>
