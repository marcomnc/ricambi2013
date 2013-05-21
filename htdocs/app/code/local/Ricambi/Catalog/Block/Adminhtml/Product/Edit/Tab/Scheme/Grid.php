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
        $this->setId('sku');        
        $this->setSaveParametersInSession(true);
        $this->setFilterVisibility(false);
        $this->setPagerVisibility(false);
        
        $this->_product = Mage::Helper('rcatalog')->getCurrentProduct();
    }
    
    /**
     * Evito la paginazione
     */
    protected function _preparePage() {
        
    }

    protected function _prepareCollection() {
 
        $collection = Mage::getModel('catalog/product')->getCollection();
        $collection->getSelect()
                   ->join(array('_link' => Mage::getSingleton('core/resource')->getTableName('catalog_product_link')),
                          '_link.linked_product_id = e.entity_id')
                   ->join(array('_attr' => Mage::getSingleton('core/resource')->getTableName('catalog_product_link_attribute')),
                          "_attr.link_type_id = _link.link_type_id and product_link_attribute_code ='position'",
                           null)
                   ->join(array('_attr_int' => Mage::getSingleton('core/resource')->getTableName('catalog_product_link_attribute_int')),
                              "_attr_int.product_link_attribute_id = _attr.product_link_attribute_id and _attr_int.link_id = _link.link_id",
                             "value pos")
                   ->where('_link.link_type_id = ?', Mage_Catalog_Model_Product_Link::LINK_TYPE_GROUPED)
                   ->where('_link.product_id = ?', $this->_product->getId())
                   ->order('_attr_int.value');
        
        $this->setCollection($collection);

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
            'name'      => 'pos',
            'width'     => '30px',
        ));
        
        return parent::_prepareColumns();
    }
    
    public function getGridUrl()
    {
        return $this->getUrl('*/*/bhoo', array('_current'=>true));
    }

}

?>
