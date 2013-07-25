<?php
/**
 * @author Amasty
 */
class Amasty_Email_Model_Observer
{
    public function addMassAction($observer)
    {
         $container = $observer->getEvent()->getBlock(); 
         
         //print_r(array_keys($container->getLayout()->getAllBlocks()));
         //exit;
         
         $grid = $container->getLayout()->getBlock('customer.grid');
         if ($grid){
             $grid->setMassactionBlockName('amemail/massaction');
         }
         
         $grid = $container->getLayout()->getBlock('sales_order.grid');
         if ($grid){
             $grid->setMassactionBlockName('amemail/massaction');
         }
         
         return $this;
    }
}
