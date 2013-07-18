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

class Ricambi_Catalog_Adminhtml_ProductController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Recupero la griglia dei grouped
     */
    public function supergroupgridAction()
    {

        $_product = Mage::getModel('catalog/product')->Load($this->getRequest()->getParam('id'));
        
        Mage::unregister('current_product');
        Mage::register('current_product', $_product);
        
        $this->loadLayout();
        $this->getResponse()->setBody($this->getLayout()->createBlock('rcatalog/adminhtml_product_edit_tab_options_spareparts_grid')->toHtml());

    }
    
    public function selectoptiongridAction() {
      

        $associateProduct = Mage::getModel('catalog/product')->Load($this->getRequest()->getParam('ass_id'));
        $groupedProduct = Mage::getModel('catalog/product')->Load($this->getRequest()->getParam('grp_id'));
        
        $actStruct = Mage::helper('core')->JsonDecode(base64_decode($this->getRequest()->getParam('struct')));
        
        $link = Mage::getModel('catalog/product')->Load($this->getRequest()->getParam('link_id'));

        if (!isset($actStruct['Product'][$groupedProduct->getId()])) {
            
            $ret['error'] = true;
            $ret['message'] = $this->__("Si e' verificato un errore nella validazione dell'articolo. Ricaricare la pagina! ");
            
            $this->getResponse()->setBody(MAge::helper('core')->JsonEncode($ret));
            
            return;
        }
        
        $this->loadLayout();
        
        $blockGrid = $this->getLayout()->createBlock("rcatalog/adminhtml_product_edit_tab_options_grid");
        $blockGrid->setAssociateProduct($associateProduct);
        $blockGrid->setGroupedProduct($groupedProduct);
        $blockGrid->setActualStructure((isset($actStruct['Product'][$groupedProduct->getId()]['Spare'][$associateProduct->getId()]) ? $actStruct['Product'][$groupedProduct->getId()]['Spare'][$associateProduct->getId()] : null));
        $blockGrid->setLinkId($link);
        
        $this->getResponse()->setBody($blockGrid->toHtml());
                
        
    }
}

?>