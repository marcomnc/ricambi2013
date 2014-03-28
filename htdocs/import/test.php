<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once 'require.php';

$customer = Mage::getModel('customer/customer')->Load(7951);

echo "<pre>";
print_R($customer->getPrimaryShippingAddress()->getStreet());
