<?php

require_once 'require.php';
require_once 'function.php';

$_elab = $_POST['macchina'];

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
        </select>
            <input type="submit" value="go"/>
        </form>
        
    </body>
</html>

