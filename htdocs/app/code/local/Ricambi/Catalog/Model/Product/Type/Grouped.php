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

class Ricambi_Catalog_Model_Product_Type_Grouped extends Mage_Catalog_Model_Product_Type_Grouped
{

    /**
     * Override della funzione base per poter selezionare anche i prodotti configurabili
     * 
     * Retrieve array of associated products
     *
     * @param Mage_Catalog_Model_Product $product
     * @return array
     */
    public function getAssociatedProducts($product = null)
    {
        if (!$this->getProduct($product)->hasData($this->_keyAssociatedProducts)) {
            $associatedProducts = array();

            if (!Mage::app()->getStore()->isAdmin()) {
                $this->setSaleableStatus($product);
            }

            $collection = $this->getAssociatedProductCollection($product)
                ->addAttributeToSelect('*')
                // Visualizzao anche i configurabili
                //->addFilterByRequiredOptions()
                ->setPositionOrder()
                ->addStoreFilter($this->getStoreFilter($product))
                ->addAttributeToFilter('status', array('in' => $this->getStatusFilters($product)));

            foreach ($collection as $item) {
                $item->setData('linked_code', $item->getSku());                
                $associatedProducts[] = $item;
                // Verifico se ci sono dei prodotti collegati
                $options = Mage::getModel('rcatalog/options')->getCollection()
                            ->setFilterByProduct($this->getProduct($product), $item);
                foreach ($options as $option) {
                    $optionProd = Mage::getModel('catalog/product')->Load($option->getProductId());
                    $optionProd->setData('linked_code', $item->getSku());
                    $optionProd->setData('link_id', $item->getData('link_id'));
                    $optionProd->setData('position', $item->getData('position'));
                    $optionProd->setData('is_options', true);
                    $associatedProducts[] = $optionProd;
                }
            }

            $this->getProduct($product)->setData($this->_keyAssociatedProducts, $associatedProducts);
        }
        return $this->getProduct($product)->getData($this->_keyAssociatedProducts);
    }
    
    
    
    /**
     * Override della funzione base per permettere di gestire l'inserimento di un prodotto configurabile
     * 
     * Prepare product and its configuration to be added to some products list.
     * Perform standard preparation process and add logic specific to Grouped product type.
     *
     * @param Varien_Object $buyRequest
     * @param Mage_Catalog_Model_Product $product
     * @param string $processMode
     * @return array|string
     */
    protected function _prepareProduct(Varien_Object $buyRequest, $product, $processMode)
    {
        $product = $this->getProduct($product);
        $productsInfo = $buyRequest->getSuperGroup();
        $positionInfo = $buyRequest->getPosition();
        $linkIdInfo = $buyRequest->getLinkId();
        $optionsInfo = $buyRequest->getIsOptions();
        $isStrictProcessMode = $this->_isStrictProcessMode($processMode);

        if (!$isStrictProcessMode || (!empty($productsInfo) && is_array($productsInfo))) {
            $products = array();
            $associatedProductsInfo = array();
            $associatedProducts = $this->getAssociatedProducts($product);
            if ($associatedProducts || !$isStrictProcessMode) {
                foreach ($associatedProducts as $subProduct) {
                    $subProductId = $subProduct->getId();
                    if(isset($productsInfo[$subProductId])) {
                        $qty = $productsInfo[$subProductId];
                        if (!empty($qty) && is_numeric($qty)) {

                            $_result = $subProduct->getTypeInstance(true)
                                ->_prepareProduct($buyRequest, $subProduct, $processMode, true);
                            if (is_string($_result) && !is_array($_result)) {
                                return $_result;
                            }

                            if (!isset($_result[0])) {
                                return Mage::helper('checkout')->__('Cannot process the item.');
                            }

                            if ($isStrictProcessMode) {
                                foreach ($_result as $res) {

                                    $res->setCartQty($qty);
                                    $res->addCustomOption('product_type', self::TYPE_CODE, $product);

                                    if ($res->getTypeId() == 'configurable') {
                                        $infoBuyRequest = unserialize($res->getCustomOption('info_buyRequest')->getValue());
                                        $infoBuyRequest['super_product_config'] = array(
                                                                                        'product_type'  => self::TYPE_CODE,
                                                                                        'product_id'    => $product->getId()
                                                                                    );
                                        $res->addCustomOption('info_buyRequest', serialize($infoBuyRequest));
                                    } else {
                                        $res->addCustomOption('info_buyRequest',
                                            serialize(array(
                                                'super_product_config' => array(
                                                    'product_type'  => self::TYPE_CODE,
                                                    'product_id'    => $product->getId()
                                                )
                                            ))
                                        );
                                        
                                        //Aggiungo i miei dati custom
                                        $res->addCustomOption('r_info',
                                            serialize(array('position'   => ((isset($positionInfo[$subProductId]))) ? $positionInfo[$subProductId] : '',
                                                            'link_id'    => ((isset($linkIdInfo[$subProductId]))) ? $linkIdInfo[$subProductId] : '',
                                                            'is_options' => ((isset($optionsInfo[$subProductId]))) ? $optionsInfo[$subProductId] : 0,
                                                            )
                                                     )
                                        );

                                    }
                                    
                                    $products[] = $res;

                                }
                            } else {
                                $associatedProductsInfo[] = array($subProductId => $qty);
                                $product->addCustomOption('associated_product_' . $subProductId, $qty);
                            }
                        }
                    }
                }
            }

            if (!$isStrictProcessMode || count($associatedProductsInfo)) {
                $product->addCustomOption('product_type', self::TYPE_CODE, $product);
                $product->addCustomOption('info_buyRequest', serialize($buyRequest->getData()));

                $products[] = $product;
            }

            if (count($products)) {
                return $products;
            }
        }

        return Mage::helper('catalog')->__('Please specify the quantity of product(s).');
    }

}
