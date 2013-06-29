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
 * Gestione del collegamento immagine / prodotti
 *
 */
class Ricambi_Catalog_Block_Adminhtml_Product_Edit_Tab_Options_Tab 
        extends Mage_Adminhtml_Block_Widget_Form //Mage_Adminhtml_Block_Template
        implements Mage_Adminhtml_Block_Widget_Tab_Interface 
{
    private $_product = null;
    
    public function __construct() {
        parent::__construct();        
        $this->setTemplate('/ricambi/catalog/edit/tab/options/tab.phtml'); 
        $this->_product = Mage::Helper('rcatalog')->getCurrentProduct();
        
    }
    
    protected function _prepareLayout()
    {
        $this->setChild('spareparts.grid',
            $this->getLayout()->createBlock('rcatalog/adminhtml_product_edit_tab_options_spareparts_grid',
                'adminhtml.product.edit.tab.options.spareparts.grid')
        );
        
        return parent::_prepareLayout();
    }
    
    protected function getGroupedGridHtml() {
        return $this->getChildHtml('spareparts.grid');
    }


    // <editor-fold defaultstate="collapsed" desc="Implementazione dell'interfaccia">
    
    /**
     * Return Tab label
     *
     * @return string
     */
    public function getTabLabel() {
        return Mage::Helper('rcatalog')->__('Opzioni Ricambi');
    }

    /**
     * Return Tab title
     *
     * @return string
     */
    public function getTabTitle() {
        return Mage::Helper('rcatalog')->__('Opzioni dei ricambi collegati');
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab() {
        return ($this->_product->getTypeId() == 'grouped' && !is_null(Mage::Helper('rcatalog')->getSchemaAttribute()) && !Mage::Helper('rcatalog')->isNotImage($this->_product->getData(Mage::Helper('rcatalog')->getSchemaAttribute())));
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     */
    public function isHidden() {
        return false;
    }

 
    // </editor-fold>
    
    
}

?>
