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
class Ricambi_Catalog_Block_Adminhtml_Product_Edit_Tab_Options_Spareparts_Grid extends Ricambi_Catalog_Block_Adminhtml_Product_Edit_Tab_Super_Group
{
    
    public function __construct() {
        parent::__construct();
        $this->setDefaultLimit(10);
        $this->setDefaultSort('position');
        $this->setGridUrl(Mage::helper("adminhtml")->getUrl('radmincatalog/adminhtml_product/supergroupgrid/', array('_current'=>true)));
        
    }
    
    public function getAssociatedProductJsObj() {
    
        return $this->getId().'AssociatedProductJsObj';
    }
    
    protected function _prepareLayout() {
        
        $this->setChild('reset_selection_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('rcatalog')->__('Reset Selection'),
                    'onclick'   => $this->getAssociatedProductJsObj().'.Reset("'+$this->_getResetUrl+'")',
                    'class'   => 'delete'
                ))
        );
        
        return parent::_prepareLayout();
    }
    
    /**
     * Override della funzione base per aggiungere il pulsante di reset
     * @return type
     */
    public function getMainButtonsHtml()
    {
        return $this->getChildHtml('reset_selection_button') . parent::getMainButtonsHtml();;
    }


    /**
     * Override della funzione base per selezionare i prodotti configurabili
     * 
     * Prepare collection
     *
     * @return Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_Group
     */
    protected function _prepareCollection()
    {
        $allowProductTypes = array();
        foreach (Mage::getConfig()->getNode('global/catalog/product/type/grouped/allow_product_types')->children() as $type) {
                $allowProductTypes[] = $type->getName();
        }
        
        $collection = Mage::getModel('catalog/product_link')->useGroupedLinks()
        ->getProductCollection()
        ->setProduct($this->_getProduct())
        ->addAttributeToSelect('*')
        //->addFilterByRequiredOptions()
        ->addAttributeToFilter('type_id', $allowProductTypes)
        ->addFieldToFilter('entity_id', array('in'=>array_keys($this->getSelectedGroupedProducts())));
        $this->setCollection($collection);
        return Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();       
        
    }
    
    
    /**
     * Override della funzione base per visualizzare le colonne che servono
     * @return type
     */
    protected function _prepareColumns()
    {
        
        $this->addColumn('entity_id', array(
            'header'    => Mage::helper('catalog')->__('ID'),
            'sortable'  => true,
            'width'     => '60px',
            'index'     => 'entity_id'
        ));
        $this->addColumn('name', array(
            'header'    => Mage::helper('catalog')->__('Name'),
            'index'     => 'name'
        ));
        
        $this->addColumn('sku', array(
            'header'    => Mage::helper('catalog')->__('SKU'),
            'width'     => '80px',
            'index'     => 'sku'
        ));        
        $this->addColumn('price', array(
            'header'    => Mage::helper('catalog')->__('Price'),
            'type'      => 'currency',
            'currency_code' => (string) Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE),
            'index'     => 'price'
        ));

        $this->addColumn('position', array(
            'header'    => Mage::helper('catalog')->__('Position'),
            'name'      => 'position',
            'type'      => 'number',
            'validate_class' => 'validate-number',
            'index'     => 'position',
            'width'     => '1',
        ));
        
        $this->addColumn('action',
            array(
                'header'    => Mage::helper('rcatalog')->__('Associa'),
                'renderer'  => $this->getLayout()->createBlock('rcatalog/adminhtml_widget_grid_column_renderer_options_link'),
                'filter'    => false,
                'sortable'  => false
        ));

        return Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
    }
    
    private function _getResetUrl() {
         return Mage::helper("adminhtml")
                ->getUrl('radmincatalog/adminhtml_product/selectoptiongrid/', 
                          array('grp_id' => $groupedProduct->getId()));
    }
    
}

?>