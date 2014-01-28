<pre>
<?php

require_once 'require.php';
require_once 'function.php';

if (is_array($_POST)) {
    foreach ($_POST as $index => $data) {
        if (isset($data['cod']) && $data['cod'] == 'on') {
            
            $sku = explode("-", $index);
            
            $sku = $sku[0];
            
            $id = Mage::getModel('catalog/product')->getIdBySku($sku);
            
            if ($id > 0) {
                $p = Mage::GetModel('catalog/product')->setStoreId($data['store'])->Load($id);
                
                $p->setName(utf8_encode ($data['value']));
                
                $p->Save();
                
                echo "Aggiornato Sku $sku - " . utf8_encode ($data['value']) ."<br>";
                
                flush();
                ob_flush();
            }
            
        }
    }
}
