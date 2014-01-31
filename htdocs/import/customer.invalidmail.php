<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


require_once 'require.php';
require_once 'function.php';

$customer = Mage::getModel('customer/customer')->getCollection();
$file = Mage::getBaseDir('var') . 'invalidmail.csv';

$f = fopen($file, "w");
fwrite($f, '"E-Mail";"Nome";"Ragione Sociale";"Attivo"'."\n") ;
foreach ($customer as $c) {    
    if (filter_var($c->getEmail(), FILTER_VALIDATE_EMAIL) === false) {
        // invalid emailaddress
        $cust = Mage::getModel('customer/customer')->Load($c->getId());
        $address = $cust->getDefaultBillingAddress();
        fwrite($f, '"' . $cust->getEmail() . '";"' . $cust->getName() . '";"' 
                . $address->getCompany(). '";"' 
                . $cust->getCustomerActivated() . "\"\n");
    }
    
}

fclose($f);
header('Pragma: public');
header('status:200 OK');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Content-type:application/octet-stream');
header('Content-Disposition: attachment; filename="invalid_address.csv"');
readfile($file);