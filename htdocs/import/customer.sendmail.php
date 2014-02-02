<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once 'require.php';
require_once 'function.php';

$file = './data/toSend.csv';

if (isset($_POST['send']) && $_POST['send'] == 1) {

    try {
        $data = unserialize(base64_decode($_POST['data']));

        $customers = Mage::getModel('customer/customer')->getCollection();
        
        $customers->getSelect()
                  ->where('email = ?', $data[0]);
        $customer = null;
        foreach ($customers as $c ) {
            $customer = Mage::getModel('customer/customer')->Load($c->getId());
            break;
        }
                    

        if ($customer) {
            $customer->setPassword($data[1]);
            $customer->sendPasswordReminderEmail();
            die("inviato");
        }
        
        die('non inviato');
        
    } catch (Exception $ex) {
        die("Errore - " . $ex->getMessage());
    }
    
    
    
}


?>
<html>
    <head>
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
    </head>
    <body>
        <form id="submit" action="" method="POST">
            <?php 
                $parser = new Varien_File_Csv();
                $parser->setDelimiter(";");
                $data = $parser->getData($file);

                if (is_array($data) && sizeof($data) > 0) : ?>
            <h1> Elenco utenti a cui verra' inviata una mail di new PWD </h1>
            <?php   $i = 0;             
                    foreach ($data as $sender): 
                        $i++;                    
            ?>
            <div class="senders" id="<?php echo 'user-'. $i;?>">
                <!--<pre><?php print_r($sender); ?> </pre>-->
            <input name="user[<?php echo $i;?>]" value="<?php echo base64_encode(serialize($sender));?>" type="hidden"/>
            <span> <?php echo $sender[0];?></span>
            </div>
            <?php        
                    endforeach;
                    
                endif;
            ?>
            <br>
            <input type="submit" value="send"/>
        </form>
    </body>
    
    <script>
        
        var queue = [];
        
        $('#submit').submit(function(evt) {
           var $form = $(this); 
           evt.preventDefault();
           evt.stopPropagation();
           queue = [];
           $('.senders').each(function() {
               var idx = $(this).attr('id')
               queue.push({id: idx, data: $('#' + idx + ' input').attr("value") });
           });
           
           iterate();
       });
       
       function iterate() {
           
           obj = queue.pop();
           if (obj.id) {
               
              var id = obj.id;
              var $form = $('#submit');
              $.ajax($form.attr ('action'), {
                type: "POST",
                data : {
                    send: 1,
                    data: obj.data
                },
                error: function() {
                    $('#' + id + ' span').text('Erorr + ' + ' - ' + $('#' + id + ' span').text());
                },
                success: function(data) {
                    $('#' + id + ' span').text(data.toString() + ' - ' +  $('#' + id + ' span').text());
                },
                complete: function() {
                    iterate();
                }
              });
               
           }           
       }
           
           
    </script>
</html>