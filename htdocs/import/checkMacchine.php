<?php

require_once 'require.php';
require_once 'function.php';

$_elab = isset($_POST['macchina']) ? $_POST['macchina'] : false ;

?>
<html>
    <head>
        
    </head>
    <body>
        <form action="" method="POST">
        <select name="macchina">
            <?php 
            $macchine = Mage::getModel('catalog/product')->getCollection();
            foreach ($macchine as $m):
              if ($m->getTypeId() == "grouped"):?>
            <option value="<?php echo $m->getId();?>" <?php if ($_elab == $m->getId()):?>selected="select"<?php endif; ?>>
                <?php echo $m->getSku() . " - " . $m->getName();?>
            </option>
            <?php
              endif;  
            endforeach;
            ?>
            <option value="_ALL" <?php if ($_elab == "_ALL"):?>selected="select"<?php endif; ?>>
                _ALL
            </option>
        </select>
            <input type="submit" value="go"/>
        </form>
        
        <br/>
        <br/>
        
<?php 
    if ($_elab):
        
        $pColl = Mage::getModel('catalog/product')->getCollection();
    
        if ($_elab != "_ALL") {
            $pColl->getSelect()->Where("entity_id = " . $_elab);
        }
    
        
        foreach ($pColl as $pr) {
            
            if ($pr->getTypeId() != "grouped") {
                continue;
            
            }
            
            $p = Mage::getModel('catalog/product')->Load($pr->getId());                
            
            $associatedProducts = $p->getTypeInstance(true)
                    ->getAssociatedProducts($p);
            $del = "";
            foreach ($associatedProducts as $a):

                if (!$a->getIsOptions()):

                    foreach ($associatedProducts as $d):

                        if ($a->getId() == $d->getId() && $d->getIsOptions()):

                            $del .= "delete from catalog_product_link where link_id = " . $a->getLinkId() . ";\n";

                        endif;

                    endforeach;


                endif;

            endforeach;
            if ($del != "") {
                $del = "-- per " . $p->getSku() . "\n" . $del;
                echo "<pre>" . $del . "</pre>";
            }
        }
        
    endif;
?>        
    </body>
</html>

