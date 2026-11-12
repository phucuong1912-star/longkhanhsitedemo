<?php 
$woo = false;
?>
    
        <section id="sidebar">
            <?php
            if(!dynamic_sidebar('default-sidebar')) {
                print 'There is no widget. You should add your widgets into <strong>';
                print 'Default';
                print '</strong> sidebar area on <strong>Appearance => Widgets</strong> of your dashboard. <br/><br/>';
            ?>
                
            <?php } ?>
        </section>
   
