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
class Ricambi_Catalog_Block_Adminhtml_Product_Edit_Tab_Scheme_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    private $_product = null;
    
    public function __construct()
    {
        parent::__construct();
        $this->setUseAjax(true);
        $this->setId('position_list');        
        $this->setSaveParametersInSession(true);
        $this->setFilterVisibility(false);
        $this->setPagerVisibility(false);
        $this->setSortable(false);
        
        $this->_product = Mage::Helper('rcatalog')->getCurrentProduct();
    }
    
    /**
     * Evito la paginazione
     */
    protected function _preparePage() {
        
    }

    protected function _prepareCollection() {
 
        $this->setCollection(Mage::getModel('rcatalog/link')->setProduct($this->_product)->getProductCollection(array('name')));
        return parent::_prepareCollection();
        
    }
    
    protected function _prepareColumns() {

        $this->addColumn('name', array(
            'header'    => Mage::helper('catalog')->__('Name'),
            'index'     => 'name'
        ));
        
        $this->addColumn('sku', array(
            'header'    => Mage::helper('catalog')->__('SKU'),
            'width'     => '80px',
            'index'     => 'sku',
        ));        

        $this->addColumn('pos', array(
            'header'    => Mage::helper('rcatalog')->__('Pos.'),
            'index'      => 'pos',
            'width'     => '30px',
        ));
        
        $this->addColumn('objposition', array(
            'header'    => Mage::helper('rcatalog')->__('In Sc.'),
            'index'     => 'link',
            'renderer'  => $this->getLayout()->createBlock('rcatalog/adminhtml_widget_grid_column_renderer_objposition'),
            'width'     => '50px',
            'column_css_class'      => 'relative-draggable',
        ));
        
        return parent::_prepareColumns();
    }
    
    public function getGridUrl()
    {
        return $this->getUrl('*/*/bhoo', array('_current'=>true));
    }
    
    public function getRowClickCallback()
    {
        return false;
    }
    
    public function getRowClass(Varien_Object $row) {
        return "position_list_table link_id_" . $row->getLink();
    }

}

?>
