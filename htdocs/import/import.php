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
                    $sku = $product->getSku();
                    try
                    {
                        $product->delete();
                        Mage::log("Cancellato Prodotto $sku");
                        break;
                    } 
                    catch (Exception $e) 
                    {
                        Mage::log("Errore in fase di cancellazione di $sku");
                        Mage::logException($e);
                        break;
                    }
                }
            }            
        }
        
        $conn = getConn();
        if (!is_null($conn)) {

            try {
                
        
                $ricambi = mysqli_query($conn,"SELECT * FROM ricambi");
                
                while($row = mysqli_fetch_array($ricambi)) {
                    
                    $id = Mage::getModel('catalog/product')->getIdBySku($row['codiceRicambio']);
                    
                    if (($id+0) > 0) {
                        Mage::log("prodotto " . $row['codiceRicambio'] . " già eistente! VERIFICARE!!!!!");
                    } else {
                        $prod = Mage::getModel('catalog/product')->setStoreId(0);
                        $prod->setSku($row['codiceRicambio']);
                        $prod->setPrice((real)$row["prezzoRicambio"]);
                        $prod->setName($row['descrizioneRicambioITA']);
                        $prod->setDescription("[VUOTO]");
                        $prod->setShortDescription("[VUOTO]");
                        $prod->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH);
                        $prod->setStatus(Mage_Catalog_Model_Product_Status::STATUS_ENABLED);
                        $prod->setTypeId(Mage_Catalog_Model_Product_Type::TYPE_SIMPLE);
                        $prod->setWebsiteIds(Array("1"));
                        $prod->setCategoryIds(array($_POST['cat_ric']));
                        $prod->setAttributeSetId(4);
                        $prod->setTaxClassId((int)2);
                        $prod->setWeight((real)1);
                        $prod->setStockData(array('is_in_stock' => 1,'manage_stock' => 0));
                        
                        try {

                            $prod->save();

                            //Eng 
                            $prodEN = Mage::getModel('catalog/product')->Load($prod->getId())->setStoreId(EN_STORE);
                            $prodEN->setName($row['descrizioneRicambioENG']);
                            $prodEN->save();
                            
                            //fra
                            $prodFR = Mage::getModel('catalog/product')->Load($prod->getId())->setStoreId(FR_STORE);
                            $prodFR->setName($row['descrizioneRicambioFRA']);
                            $prodFR->save();
                            
                            //Ted
                            $prodDH = Mage::getModel('catalog/product')->Load($prod->getId())->setStoreId(DH_STORE);
                            $prodDH->setName($row['descrizioneRicambioDEU']);
                            $prodDH->save();
                            
                            //ESp
                            $prodES = Mage::getModel('catalog/product')->Load($prod->getId())->setStoreId(ES_STORE);
                            $prodES->setName($row['descrizioneRicambioESP']);
                            $prodES->save();
                            
                            
                        } catch (Exception $ex) {
                            Mage::log("Errore in fase di salvataggio del prodotto " .$row['codiceRicambio']);
                            Mage::logException($ex);
                        }
                        
                    }
                    
                    break;
                }
                
            } catch (Exception $ex) {
                MAge::log(" Errore in fase di importazione dei ricambi");
                Mage::logException($ex);
            }
            
            mysqli_close($conn);
        }

        
        
    }
?>
        
<?php
}
?>
        
    </body>
    
</html>

<?php 

function getConn() {
    $conn =  mysqli_connect("localhost","root","","conversioni_cosmetal");
    if (mysqli_connect_errno())
    {
        Mage::log("Failed to connect to MySQL: " . mysqli_connect_error());
        return null;
    }
    return $conn;

}


?>