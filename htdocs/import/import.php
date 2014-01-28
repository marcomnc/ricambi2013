<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


require_once 'require.php';
require_once 'function.php';

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
    
//        foreach ($products as $prod){
//            
//            $product = Mage::getModel('catalog/product')->Load($prod->getId());
//            
//            foreach ($product->getCategoryIds() as $catId) {
//                if ($catId == $_POST['cat_ric']) {
//                    $sku = $product->getSku();
//                    try
//                    {
//                        $product->delete();
//                        Mage::log("Cancellato Prodotto $sku");
//                        break;
//                    } 
//                    catch (Exception $e) 
//                    {
//                        Mage::log("Errore in fase di cancellazione di $sku");
//                        Mage::logException($e);
//                        break;
//                    }
//                }
//            }            
//        }
        
        $conn = getConn();
        if (!is_null($conn)) {

            try {
                
        
                $ricambi = mysqli_query($conn,"SELECT * FROM ricambi");
                echo '<form method="POST" action="http://cosmetal2013.mcgroup.it/import/setName.php">';
                while($row = mysqli_fetch_array($ricambi)) {
                    
                    $id = Mage::getModel('catalog/product')->getIdBySku($row['codiceRicambio']);
                    
                    if (($id+0) > 0) {
                        
                        $prod = Mage::getModel('catalog/product')->Load($id);
                        if (utf8_decode($prod->getName()) != ($row['descrizioneRicambioITA'])) {
                            echo '<input type="checkbox" name="' .$row['codiceRicambio']. '-IT[cod]" ' . (detectUTF8($row['descrizioneRicambioITA']) ? 'checked' : '' ). ' />';
                            echo '<input type="hidden" name="' .$row['codiceRicambio']. '-IT[value]" value="' .$row['descrizioneRicambioITA']. '"/>';
                            echo '<input type="hidden" name="' .$row['codiceRicambio']. '-IT[store]" value="0"/>';
                            echo "Site ITA " . $row['codiceRicambio'] . " ";
                            echo $prod->getName() . " <> " . $row['descrizioneRicambioITA'];
                            echo "<br>";
                        }
                        
                        //Eng 
                        $prod = Mage::getModel('catalog/product')->setStoreId(EN_STORE)->Load($id);
                        if (utf8_decode($prod->getName()) != ($row['descrizioneRicambioENG'])) {
                            echo '<input type="checkbox" name="' .$row['codiceRicambio']. '-EN[cod]" ' . (detectUTF8($row['descrizioneRicambioENG']) ? 'checked' : '') . ' />';
                            echo '<input type="hidden" name="' .$row['codiceRicambio']. '-EN[value]" value="' .$row['descrizioneRicambioENG']. '"/>';
                            echo '<input type="hidden" name="' .$row['codiceRicambio']. '-EN[store]" value="'. EN_STORE. '"/>';
                            echo "Site ENG " . $row['codiceRicambio'] . " ";
                            echo $prod->getName() . " <> " . $row['descrizioneRicambioENG'];
                            echo "<br>";
                        }

                        //Fra
                        $prod = Mage::getModel('catalog/product')->setStoreId(FR_STORE)->Load($id);
                        if (utf8_decode($prod->getName()) != ($row['descrizioneRicambioFRA'])) {
                            echo '<input type="checkbox" name="' .$row['codiceRicambio']. '-FR[cod]" ' . (detectUTF8($row['descrizioneRicambioFRA']) ? 'checked' : '' ). ' />';
                            echo '<input type="hidden" name="' .$row['codiceRicambio']. '-FR[value]" value="' .$row['descrizioneRicambioFRA']. '"/>';
                            echo '<input type="hidden" name="' .$row['codiceRicambio']. '-FR[store]" value="'. FR_STORE. '"/>';
                            echo "Site FRA " . $row['codiceRicambio'] . " ";
                            echo $prod->getName() . " <> " . $row['descrizioneRicambioFRA'];
                            echo "<br>";
                        }
                        
                        //Dh
                        $prod = Mage::getModel('catalog/product')->setStoreId(DH_STORE)->Load($id);
                        if (utf8_decode($prod->getName()) != ($row['descrizioneRicambioDEU'])) {
                            echo '<input type="checkbox" name="' .$row['codiceRicambio']. '-DH[cod]" ' . (detectUTF8($row['descrizioneRicambioDEU']) ? 'checked' : '') . ' />';
                            echo '<input type="hidden" name="' .$row['codiceRicambio']. '-DH[value]" value="' .$row['descrizioneRicambioDEU']. '"/>';
                            echo '<input type="hidden" name="' .$row['codiceRicambio']. '-DH[store]" value="'. DH_STORE. '"/>';
                            echo "Site DH  " . $row['codiceRicambio'] . " ";
                            echo $prod->getName() . " <> " . $row['descrizioneRicambioDEU'];
                            echo "<br>";
                        }
                        
                        //ES
                        $prod = Mage::getModel('catalog/product')->setStoreId(ES_STORE)->Load($id);
                        if (utf8_decode($prod->getName()) != ($row['descrizioneRicambioESP'])) {
                            echo '<input type="checkbox" name="' .$row['codiceRicambio']. '-ES[cod]" ' . (detectUTF8($row['descrizioneRicambioESP']) ? 'checked' : '') . ' />';
                            echo '<input type="hidden" name="' .$row['codiceRicambio']. '-ES[value]" value="' .$row['descrizioneRicambioESP']. '"/>';
                            echo '<input type="hidden" name="' .$row['codiceRicambio']. '-ES[store]" value="'. ES_STORE. '"/>';
                            echo "Site ESP " . $row['codiceRicambio'] . " ";
                            echo $prod->getName() . " <> " . $row['descrizioneRicambioESP'];
                            echo "<br>";                            
                        }
                        
                    } else {
                        
                        continue;
                        
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
                    
                    MAge::Log("CReato Ricambio " . $row['codiceRicambio']);
                }
                echo "<br><input type=\"submit\" value=\"Aggiorna\"/>";
                echo "</form>";
            } catch (Exception $ex) {
                MAge::log(" Errore in fase di importazione dei ricambi");
                Mage::logException($ex);
                echo "Importazione ricambi terminata con errore \n";
            }
            
            mysqli_close($conn);
        }
        echo "Importazione ricambi terminata correttamente \n";
    }
    if (isset($_POST["macchine"]) && isset($_POST['refrigeratori'])) {
        Mage::Log("------------------ IMPORTAZIONE MACCHINE -----------------------");


        $conn = getConn();
        if (!is_null($conn)) {

            try {
                $sql = "SELECT macchine.idMacchina, macchine.macchina, macchine.idFamiglia, macchine.posizioneMacchina, ";
                $sql .= " versioni.idVersione ";
                $sql .= " FROM macchine ";
                $sql .= " JOIN versioni ON  `macchine`.`idMacchina` = versioni.`idMacchina` ";
                $sql .= " where versioni.visualizzaVersione = 1 "; //and macchine.idMacchina = '12983'";
                
                //$sql .= " and macchine.macchina = 'UV NIAGARA 180 SL WG'";

                $macchine = mysqli_query($conn, $sql);
                
                while($row = mysqli_fetch_array($macchine)) {
                    creaMacchina( $row, 1);
                    creaMacchina( $row, 2);
                    
                }
                
                
            } catch (Exception $ex) {
                MAge::log(" Errore in fase di importazione delle macchine");
                Mage::logException($ex);
                echo "Importazione Macchine terminata con errore \n";
            }
            
            mysqli_close($conn);
        }
        
        echo "Importazione Macchine terminata con successo\n";
    }

    if (isset($_POST["foto"]) || isset($_POST["disegni"])) {
        
        if (isset($_POST["disegni"])) {
            $dirName = __DIR__ . "/data/schemi";            
            if ($handle = opendir($dirName)) {
            
                 while (false !== ($entry = readdir($handle))) {
                    if ($entry != '.' && $entry != '..') {
                        
                        rename($dirName.'/'.$entry, strtolower($dirName.'/'.$entry));
                    }
                }
                
            }
            
        }
        if (isset($_POST["foto"])) {
            $dirName = __DIR__ . "/data/fotoProdotti";            
            if ($handle = opendir($dirName)) {
            
                 while (false !== ($entry = readdir($handle))) {
                    if ($entry != '.' && $entry != '..') {
                        
                        rename($dirName.'/'.$entry, strtolower($dirName.'/'.$entry));
                    }
                }
                
            }
            
        }
        $products = Mage::getModel('catalog/product')->getCollection()
                         ->addFieldToFilter('type_id', Mage_Catalog_Model_Product_Type::TYPE_GROUPED);
                         //->addFieldToFilter('entity_id', array('in' => array('1970')));
        
        Mage::Log('-------------IMPORTAZIONE IMMAGINI -----------------');
        foreach ($products as $product) {
       
            if (isset($_POST["foto"]))
                importImmagine($product->getId(), "foto");            
            
            if (isset($_POST["disegni"]))
                importImmagine($product->getId(), "disegni");  
            
            //break;

        }
                
    }
    
    if (isset($_POST['riasso'])) {
        
?>
<h1>Riassociazione prodotti<h1>    
        <h2>Farla a mano con queste query</h2>
<p>insert into catalog_product_link</p></p>
<p>SELECT null, a.entity_id, b.entity_id, 1  FROM `catalog_product_entity` a</p>
<p>join `catalog_product_entity` b on a.sku = replace(b.sku, 'hc','sp' )</p>
<p>and a.entity_id <> b.entity_id</p>
<p>WHERE a.`sku` LIKE '%sp'</p>
<br>
<br>
<p>insert into catalog_product_link</p></p>
<p>SELECT null, b.entity_id, a.entity_id, 1  FROM `catalog_product_entity` a</p>
<p>join `catalog_product_entity` b on a.sku = replace(b.sku, 'hc','sp' )</p>
<p>and a.entity_id <> b.entity_id</p>
<p>WHERE a.`sku` LIKE '%sp'</p>
<?php         
    }
}
?>
        
    </body>
    
</html>

<?php 



?>