<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

include 'require.php';

?>
<html>
    <head>
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    </head>
    <body>
        <form method="POST" action="<?php echo preg_replace("/index.php\//", "", Mage::getUrl('import/import.php'));?>">
            <input type="text" name="secure" value="" />
            <br>
            <br>
            <input type="checkbox" name="ricambi" id="ricambi" value="0" 
                   onclick="$(this).val(!$(this).val());" />
            <label for="ricambi">Importazione ricambi</label>
            <br>
             <label for="cat_ric">Categoria Ricambi</label>
            <input type="text" name="cat_ric" id="cat_ric" value="48" />
            <br><br>
            <input type="checkbox" name="macchine" id="macchine" value="0" 
                   onclick="$(this).val(!$(this).val());" />
            <label for="macchine">Importazione Macchine</label>
            <br>
            <input type="checkbox" name="disegni" id="disegni" value="0" 
                   onclick="$(this).val(!$(this).val());" />
            <label for="disegni">Importazione Disegni</label>
            <br>
            <input type="checkbox" name="opzioni" id="opzioni" value="0" 
                   onclick="$(this).val(!$(this).val());" />
            <label for="opzioni">Importazione Opzioni</label>
            <br>
            <br>
            <input type="input" name="refrigeratori" id="refrigeratori" value="54" />
            <label for="refrigeratori">Categoria refrigeratori</label>
            
            
            
            <br><br><br><br>
            <input type="submit" value="import"/>
        </form>
    </body>
</html>
