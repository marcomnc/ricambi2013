<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


require_once 'require.php';
require_once 'function.php';

define("C_G", "IMPORTED_CUSTOMERS");

$file = './data/customer.csv';

$adapter = Mage::getSingleton('core/resource')->getConnection('core/read');

//$customer = Mage::getModel('customer/customer')->setWebsiteId(1)->loadByEmail('test@mcgroup.it');
//print_r($customer->getData());
//
//$addres = MAge::GetModel('customer/address');
//$addres->Load($customer->getData('default_billing'));
//print_r($addres->getData());
//
//die();

if (!file_exists($file)):
    
    echo "File clienti inesistente";
    
else:
    
    $toSendMail = array();
    $error = array();
    
    unlink("./error.txt");
    
    $index = (int)file_get_contents("./lock");
    
    $parser = new Varien_File_Csv();
    $parser->setDelimiter(";");
    $data = $parser->getData($file);
    
    if (is_array($data) && sizeof($data) > 0) {

        foreach ($data as $idx => $c) {
            
            if ($idx < $index) {
                //echo "scarto " . $c[0];
                continue;
            } else {
                //die("elaboro $idx " . $c[0]);
            }
            
            $pwd = $c[13];
            if (strlen($c[13]) < 6) {
                
                $chars = Mage_Core_Helper_Data::CHARS_PASSWORD_LOWERS
                    . Mage_Core_Helper_Data::CHARS_PASSWORD_UPPERS
                    . Mage_Core_Helper_Data::CHARS_PASSWORD_DIGITS
                    . Mage_Core_Helper_Data::CHARS_PASSWORD_SPECIALS;
                $pwd = Mage::helper('core')->getRandomString(8, $chars);
                $toSendMail[$c[12]] = $pwd;
            }

            $customerData = array(
                "email"         => $c[12],
                "firstname"     => $c[1],
                "lastname"      => $c[2],
                "password"      => $pwd,
                "website_id"    => 1,
                "store_id"      => $c[17],
                "group_id"      => 1,
                "taxvat"        => $c[5],
                "old_code"      => $c[0],
                "created_at"    => _getData($c[15]),
                // Dopo quando salvo ...
                //'customer_activated' => $c[14],
                'no_activation_email' => true
            );
            
            try {
                $customer = Mage::getModel('customer/customer')->setWebsiteId(1)->loadByEmail($c[12]);

                if ($customer->getId() > 0){
                    unset($customerData['email']);

                }
                
                foreach ($customerData as $k => $v) {
                    $customer->setData($k, $v);
                }            
                $customer->save();

                $address = Mage::getModel('customer/address');
                if ($customer->hasData('default_billing') && $customer->getData('default_billing') > 0) {
                    $address->Load($customer->getData('default_billing') );
                }
                
                $addressData = array(
                    "parent_id"         => $customer->getId(),
                    "created_at"        => _getData($c[15]),
                    "is_active"         => 1,
                    "firstname"         => $c[1],
                    "lastname"          => $c[2],
                    "company"           => $c[4],
                    "city"              => $c[7],
                    "country_id"        => $c[18],
                    "region"            => $c[8],
                    "postcode"          => $c[6],
                    "telephone"         => $c[10],
                    "fax"               => $c[11],
                    "street"            => $c[3],
                );
                    
                foreach ($addressData as $k => $v) {
                    $address->setData($k,$v);
                }
                
                $address->save();
                $id = $address->getId();
                
                $customer->setData('customer_activated', $c[14]);
                if ($c[14] == 0) {
                    $customer->setData('date_rejected', date ("Y-m-d H:i:s",strtotime("2014/01/31 00:00:00.00")));
                }
                $customer->setData('no_activation_email', true);
                $customer->setData('default_billing', $id);
                $customer->setData('default_shipping', $id);
                
                $customer->save();
                
                if (strlen($c[13]) < 6) {
                    $file = fopen("./data/toSend.csv", "a+");       
                    $str = "\"$c[12]\";\"$pwd\"\n";
                    fwrite($file, $str);
                    fclose($file);
                }
                
                $file = fopen("./lock", "w");       
                fwrite($file, $idx);
                fclose($file);
                
            } catch (Exception $ex) {
                $fileError = fopen("./error.txt", "w+");       
                $error = array();
                $error[$c[12]] = $ex->getMessage();
                fwrite($file, print_r($error, true));   
                fclose($file);
            }
                   
//            if ($ii++ > 10) 
//                break;
        }        
                
        echo "elaborazione terminata";
        
    } else {
        echo "Impossibile caricare il file";
    }
    
endif;

