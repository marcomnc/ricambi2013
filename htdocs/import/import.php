<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


include 'require.php'

?>
<html>
    <head>
    </head>
    
    <body>
<?php 
Mage::log($_POST);
if ($_POST['secure'] != "aquiloni gagliardi"){
    echo "<strong>Secure code errato!!!</strong>";   
} else {
    
    if (isset($_POST["ricambi"]) && isset($_POST['cat_ric'])){
        // Azzero i ricambi
        
        $products = Mage::getModel('catalog/product')->getCollection();
    
        foreach ($products as $prod){
            
            $product = Mage::getModel('catalog/product')->Load($prod->getId());
            
            foreach ($product->getCategoryIds() as $catId) {
                if ($catId == $_POST['cat_ric']) {
                    echo "Cancello " . $product->getSku();
                }
            }
            
        }
        
        
    }
?>
        
<?php
}
?>
        
    </body>
    
</html>
