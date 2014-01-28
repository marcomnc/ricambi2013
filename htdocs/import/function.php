<?php

$tipoSchema = array(1 => 'sp', 2 => 'hc');

$conn = null;

function getConn() {
    
    global $conn;
    
    $conn =  mysqli_connect("localhost","cosmetal","6vsJj$33","conversioni_cosmetal");
    //$conn =  mysqli_connect("localhost","root","","conversioni_cosmetal");
    if (mysqli_connect_errno())
    {
        Mage::log("Failed to connect to MySQL: " . mysqli_connect_error());
        return null;
    }
    return $conn;

}


/**
 * Azzero tutte le associazioni di un articolo
 * @param type $product
 */
function resetLink ($product) {
    
    $assColl = Mage::getModel('catalog/product_link')->getCollection();
    $assColl->getSelect()->Where('product_id = ?', $product->getId())
                         ->Where('link_type_id = ?', Mage_Catalog_Model_Product_Link::LINK_TYPE_GROUPED);

    $assOptions = Mage::getModel('rcatalog/options')->getCollection()->setFilterByProduct($product);
    foreach ($assOptions as $opt) {
        $options = Mage::getModel('rcatalog/options')->Load($opt->getId());
        $options->delete();
    }
    

    foreach ($assColl as $ass) {
        $associate = Mage::getModel('catalog/product_link')->load($ass->getId());
        $associate->delete();
    }
    
    $assPosition = Mage::getModel('rcatalog/position')->getCollection()->setFilterByProduct($product);
    foreach ($assPosition as $pos) {
        $position = Mage::getModel('rcatalog/position')->Load($pos->getId());
        $position->delete();
    }    
    
}

function getLinkFoto($idVersione) {
    
    global $conn;
    
    $sql = "SELECT versioni . * , foto.filefoto FROM versioni";
    $sql .= " LEFT JOIN versionifoto ON versionifoto.idVersione = versioni.idVersione";
    $sql .= " LEFT JOIN foto ON foto.IdFoto = versionifoto.idFoto";
    $sql .= " WHERE versioni.IdVersione = $idVersione";
    
    $foto = mysqli_query($conn, $sql);
    $linkFoto = "";
    while ($row = mysqli_fetch_array($foto)) {

        $linkFoto .= (($linkFoto == "") ? "" : ",") . $row['filefoto'];
        
    }
    
    return $linkFoto;
}

function _getGalleryAttribute($product) {
    $attributes = $product->getTypeInstance(true)
        ->getSetAttributes($product);

    if (!isset($attributes[Mage_Catalog_Model_Product_Attribute_Media_Api::ATTRIBUTE_CODE])) {
        throw new Exception;
    }

    return $attributes[Mage_Catalog_Model_Product_Attribute_Media_Api::ATTRIBUTE_CODE];
}



function getLinkSchema($idVersione, $type) {
    
    global $conn;
    
    $sql = "SELECT * FROM versionidisegno";
    $sql .= " WHERE IdVersione = $idVersione AND idTipoDisegno = $type";
    
    $schema = mysqli_query($conn, $sql);

    $linkSchema = "";
    while ($row = mysqli_fetch_array($schema)) {

        $linkSchema .= (($linkSchema == "") ? "" : ",") . $row['fileDisegno'];
        
    }
    
    return $linkSchema;
}


function creaMacchina( $row, $type) {
    
    global $tipoSchema;
    global $conn;
    
    $id = Mage::getModel('catalog/product')->getIdBySku(str_replace (" ", "", $row['macchina']).$tipoSchema[$type]);
    if (is_null($id) || $id <= 0) {
        
        $prod = Mage::getModel('catalog/product')->setStoreId(0);
        $prod->setSku(str_replace (" ", "", $row['macchina']).$tipoSchema[$type]);
        $prod->setName($row['macchina']);
        $prod->setDescription("[VUOTO]");
        $prod->setShortDescription("[VUOTO]");
        $prod->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH);
        $prod->setStatus(Mage_Catalog_Model_Product_Status::STATUS_ENABLED);
        $prod->setTypeId(Mage_Catalog_Model_Product_Type::TYPE_GROUPED);
        $prod->setWebsiteIds(Array("1"));
        $prod->setCategoryIds(array($_POST['refrigeratori']));
        $prod->setAttributeSetId(4);
        $prod->setTaxClassId((int)2);
        $prod->setWeight((real)1);
        $prod->setStockData(array('is_in_stock' => 1,'manage_stock' => 0));
        
        if($type == 1) {
            $prod->setTipoDisegno("34");
        } else {
            $prod->setTipoDisegno("33");
        }

        $prod->setData('data_link_foto', getLinkFoto($row['idVersione']));
        $prod->setDataLinkSchema(getLinkSchema($row['idVersione'], $type));

        $prod->save();
    } else {
        $prod = Mage::getModel('catalog/product')->setStoreId(0)->Load($id);
    }
    
    Mage::log('Creato prodotto ' . $row['macchina']);
    
    //Associazioni
    
    resetLink($prod);
    
    $sql = "SELECT ricambiversione . * , codiceRicambio FROM ricambiversione ";
    $sql .= "JOIN ricambi ON ricambi.idRicambio = ricambiversione.idRicambio";
    $sql .= " WHERE  `idVersione` = " . $row['idVersione'];
    $sql .= " AND  `idTipoDisegno` = " . $type;
    $sql .= " AND idRicambioMacchina_child =0 ";
    $sql .= " ORDER BY  `NUMERO` ";

    $associati = mysqli_query($conn, $sql);

    //Creo le associazioni
    $data = array();
    while($rowAss = mysqli_fetch_array($associati)) {
        
        $productLinkId = Mage::getModel('catalog/product')->getIdBySku($rowAss['codiceRicambio']);

        if ($productLinkId > 0) {
            $data[$productLinkId]['position'] =  $rowAss['NUMERO'];
            $data[$productLinkId]['pos'][] = array ( 'pos' => $rowAss['NUMERO'],
                                                     'x' => $rowAss['xPos'],
                                                     'y' => $rowAss['yPos']
                                                    );
            
            
            $sqlChild = "SELECT ricambiversione . * , codiceRicambio FROM ricambiversione ";
            $sqlChild .= "JOIN ricambi ON ricambi.idRicambio = ricambiversione.idRicambio";
            $sqlChild .= " WHERE  `idVersione` = " . $row['idVersione'];
            $sqlChild .= " AND  `idTipoDisegno` = " . $type;
            $sqlChild .= " AND idRicambioMacchina_child = '".$rowAss['idRicambio'] . "'";
            
            $childs  = mysqli_query($conn, $sqlChild);

            
            while ($rowChild =  mysqli_fetch_array($childs)) {
                $childId = Mage::getModel('catalog/product')->getIdBySku($rowChild['codiceRicambio']);
                if ($childId > 0)
                    $data[$productLinkId]['child'][] = $childId;
            }
        }

    }

    if (sizeof($data) > 0) {
    
        $prod->setGroupedLinkData($data);
        
        Mage::getModel('catalog/product_link')->saveGroupedLinks($prod);
    }

    foreach ($data as $prodId => $d) {
        $assColl = Mage::getModel('catalog/product_link')->getCollection();
        $assColl->getSelect()->Where('product_id = ?', $prod->getId())
                             ->Where('linked_product_id = ?', $prodId )
                             ->Where('link_type_id = ?', Mage_Catalog_Model_Product_Link::LINK_TYPE_GROUPED);

        foreach ($assColl as $ass) {
            
            foreach ($d['pos'] as $posData) {
                $positionLink = Mage::getModel('rcatalog/position');
                $positionLink->setData('grouped_product_id', $prod->getId());
                $positionLink->setData('link_id', $ass->getId());
                $positionLink->setData('position_x', ($posData["x"] + 15));
                $positionLink->setData('position_y', ($posData["y"] + 15));                        

                
                $positionLink->save();
            }
                        
            
            if (isset($d['child'])) {
                foreach ($d['child'] as $child) {

                    $optLinks = Mage::getModel('rcatalog/options');

                    $optLinks->setData('link_id', $ass->getId());
                    $optLinks->setData('product_id', $child);
                    $optLinks->setData('sort_order', null);

                    $optLinks->save();

                }
            }
            
        }
        
                             
    }
    
    
}

function importImmagine($productId, $type) {
        
    $product = Mage::getModel('catalog/product')->Load($productId);
    $gallery = _getGalleryAttribute($product);

    if ($type == 'foto') {
        $arrayImg = preg_split('/,/', $product->getDataLinkFoto());
    } else {
        $arrayImg =  preg_split('/,/', $product->getDataLinkSchema());
    }

    foreach ($arrayImg as $img) {

        if (($img . '') == '')
            continue;
        
        $imgname = __DIR__ . "/data/" . (($type == 'foto') ? 'fotoProdotti' : 'schemi') .  "/" . (($type == 'foto') ? strtolower($img) : strtolower(str_replace('.swf', '.png', $img)));
        
        Mage::log("importo immagine $imgname per " . $product->getSku());

        if (file_exists($imgname)) {

            if (($type != 'foto' && $product->getData('schema_1').'no_selection' != 'no_selection') ||
                ($type == 'foto' && $product->getData('image').'no_selection' != 'no_selection')) {
             
                $productGallery = $product->getMediaGallery();

                if (isset($productGallery["images"])) { //Esiste una galleria
                    foreach ($productGallery["images"] as $item) {

                        if (($type != 'foto' && $item['file'] == $product->getData('schema_1')) ||
                            ($type == 'foto' && $item['file'] == $product->getData('image'))) {
                            Mage::log("Cancello immagine già presente");
                            try {
                                $gallery->getBackEnd()->removeImage($product, $item["file"]);
                                //$product->save();   
                            } catch (Exception $e) {
                                Mage::Log("Errore remove", Zend_Log::ERR);
                                Mage::LogEception($e);
                                
                            }
                        }
                    }
                }
            }
            $file = $gallery->getBackend()->addImage(
                        $product,
                        $imgname,
                        null,
                        false, 
                        ($type=='foto') ? false : true
                    );

            $gallery->getBackEnd()->updateImage($product, $file, ($type=='foto') ? 1 : 10);
            if ($type == 'foto') {
                $product->setData('thumbnail', $file);
                $product->setData('image', $file);
                $product->setData('small_image', $file);
            }else {
                $product->setData('schema_1', $file);            
            }
        } else {
            Mage::Log('Non trovo l\'immagine!!!');
        }
    }

    $product->save();


}            

function detectUTF8($string)
{
    
    return 'UTF-8' === mb_detect_encoding($string, "auto");
    //$val =  preg_match('%(?:[\xC2-\xDF][\x80-\xBF]|\xE0[\xA0-\xBF][\x80-\xBF]|[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}|\xED[\x80-\x9F][\x80-\xBF]|\xF0[\x90-\xBF][\x80-\xBF]{2}|[\xF1-\xF3][\x80-\xBF]{3}|\xF4[\x80-\x8F][\x80-\xBF]{2})+%xs', $string);
    
     
}


