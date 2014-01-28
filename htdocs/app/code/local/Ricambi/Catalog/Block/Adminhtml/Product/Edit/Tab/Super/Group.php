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
class Ricambi_Catalog_Block_Adminhtml_Product_Edit_Tab_Super_Group extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_Group {
    
    
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
        ->addAttributeToFilter('type_id', $allowProductTypes);

        $this->setCollection($collection);
        return Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();       
        
    }
    
    /**
     * Override della funzoine base per visualizzare il tipo del prodotto
     * @return type
     */
    protected function _prepareColumns()
    {
        $this->addColumn('in_products', array(
            'header_css_class' => 'a-center',
            'type'      => 'checkbox',
            'name'      => 'in_products',
            'values'    => $this->_getSelectedProducts(),
            'is_options'=> $this->getSelectedGroupedProducts(),
            'align'     => 'center',
            'index'     => 'entity_id',
            'renderer'  => $this->getLayout()->createBlock('rcatalog/adminhtml_widget_grid_column_renderer_groupedcheck'),
        ));

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
        $this->addColumn('type', array(
            'header'=> Mage::helper('catalog')->__('Type'),
            'width' => '60px',
            'index' => 'type_id',
            'type'  => 'options',
            'options' => Mage::getSingleton('catalog/product_type')->getOptionArray(),
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

        $this->addColumn('qty', array(
            'header'    => Mage::helper('catalog')->__('Default Qty'),
            'name'      => 'qty',
            'type'      => 'number',
            'validate_class' => 'validate-number',
            'index'     => 'qty',
            'width'     => '1',
            'editable'  => true
        ));

        $this->addColumn('position', array(
            'header'    => Mage::helper('catalog')->__('Position'),
            'name'      => 'position',
            'type'      => 'number',
            'validate_class' => 'validate-number',
            'index'     => 'position',
            'width'     => '1',
            'editable'  => true,
            'edit_only' => !$this->_getProduct()->getId()
        ));

        return Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
    }
    
    /**
     * Retrieve grouped products
     * Only if not options......
     * @return array
     */
    public function getSelectedGroupedProducts()
    {
        $associatedProducts = Mage::registry('current_product')->getTypeInstance(true)
            ->getAssociatedProducts(Mage::registry('current_product'));
        $products = array();
        foreach ($associatedProducts as $product) {            
            if (!$product->getIsOptions())
                $products[$product->getId()] = array(
                    'qty'       => $product->getQty(),
                    'position'  => $product->getPosition(),
                    'is_options'=> $product->getIsOptions(),
                );
        }
        return $products;
    }
    
}
