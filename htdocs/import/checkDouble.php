<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once 'require.php';

$coll = Mage::getModel('catalog/product')->getCollection()
            ->addFieldToFilter('type_id', Mage_Catalog_Model_Product_Type::TYPE_GROUPED);
            //->addFieldToFilter('sku', 'UVCONNECT23Hsp');

$doublePos = array();

foreach ($coll as $_gp) {
    $ass = $_gp->getTypeInstance(true)->getAssociatedProducts(Mage::getModel('catalog/product')->Load($_gp->getId()));

    $positions = Mage::getModel('rcatalog/position')->getCollection()->setFilterByProduct($_gp);
    
    $_db = array();
    foreach ($positions as $pos) {
        
//        if (!isset($_db[$pos->getLinkId()])) {
//            $_db[$pos->getLinkId()] = 1;
//        } else {
            
            $linkedProductId = $pos->getData('linked_product_id');
            
            $options = Mage::getModel('rcatalog/options')->getCollection()
                    ->setFilterByProduct($_gp, $pos->getData('linked_product'));
            
            $options->getSelect()->Where('main_table.link_id = ? ',$pos->getLinkId());
            
            $_dp = array();
            foreach ($options as $opt) {
                //print_r($opt->getData());
                $key = $opt->getData('link_id') . "#" . $opt->getData('product_id'); 
                
                if (!isset($_dp[$key])) {
                    $_dp[$key] = 1;
                } else {
                    echo "delete from ricambi_catalog_product_gruoped_associate_options where entity_id =  " . $opt->getData('entity_id') . "\n"; 
                }
                
//                print_r(Mage::getModel('rcatalog/link')
//                    ->setProduct($_gp)
//                    ->getCollection($pos->getLinkId()));
//                die('asdas');
                
//                print_r(array(
//                    "prod" => $_gp->getSku(), 
//                    "prod_pos" => $pos->getDAta(),
//                    "prod_coll" => $pos->getData('linked_proudct_sku'),
//                    "pos" => $pos->getData('pos'),
//                    "prod_opt" => $opt->getData('proudct_sku'),
//                    "link" =>$pos->getLinkId()));
                
            }   
            
            //die();
//        }
    }    
    
        
//    $_dp = array();
//    foreach ($ass as $as) {
//        $options = Mage::getModel('rcatalog/options')->getCollection()
//                                ->setFilterByProduct($_gp, $as);
//        
//        if (!isset($_db[$options->getLinkId()])) {
//            $_db[$options->getLinkId()] = 1;
//        } else {
//            print_r(array(
//                "prod" => $_gp->getSku(), 
//                "prod_coll" => $pos->getData('linked_product')->getSku(),
//                "position" => $pos->getData('linked_product')->getPosition(),
//                "link" =>$pos->getLinkId()));
//        }
//    }
}

print_r($doublePos);
