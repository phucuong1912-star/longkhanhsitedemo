<table class="table wp-list-table widefat fixed striped posts">
    <thead>
        <tr>
            <!-- <th><?php echo esc_html__('No.', 'listingpro-plugin'); ?></th> -->



            <th scope="col" id="title" class="manage-column column-title column-primary sortable desc">
                <a><span><?php echo esc_html__('Receipt/invoice#', 'listingpro-plugin'); ?></span><span class="sorting-indicator"></span></a>
            </th>

            <th class="manage-column column-tags"><?php echo esc_html__('Date', 'listingpro-plugin'); ?></th>
            <th class="manage-column column-tags"><?php echo esc_html__('Method', 'listingpro-plugin'); ?></th>
            <th class="manage-column column-tags"><?php echo esc_html__('Price', 'listingpro-plugin'); ?></th>
            <th class="manage-column column-tags"><?php echo esc_html__('Status', 'listingpro-plugin'); ?></th>
            <th class="manage-column column-tags"><?php echo esc_html__('Action', 'listingpro-plugin'); ?></th>
            <th class="manage-column column-tags"><?php echo esc_html__('Watch', 'listingpro-plugin'); ?></th>
        </tr>
    </thead>
    <tbody>

        <?php
        global $wpdb;
        $counter = 1;
        $currency_position = lp_theme_option('pricingplan_currency_position');
        $table = "listing_orders";
        $table = $dbprefix . $table;
        $results = array();
        if ($wpdb->get_var("SHOW TABLES LIKE '$table'") == $table) {
            $query = "";
            $query = "SELECT * from $table WHERE status !='in progress' ORDER BY main_id DESC";
            $results = $wpdb->get_results($query);
        }
        if (!empty($results)) {

            foreach ($results as $Index => $Value) {
                $invoiceStatus = $Value->status;
                $main_id = $Value->main_id;
                $listid = $Value->post_id;

                $currency_sign = (isset($Value->currency) && $Value->currency != '') ? $Value->currency : listingpro_currency_sign();
                $price         = $Value->price;
                if ($currency_position == 'right') {
                    $price .= $currency_sign;
                } else {
                    $price = $currency_sign . $price;
                }

                if ($Value->payment_method == 'paypal') {
                    //new code 2.6.15
                    if ($Value->payment_method == 'wire' || $Value->payment_method == 'WIRE') {
                        $Value->payment_method = esc_html__('wire', 'listingpro');
                    }
                    //end new code 2.6.15
        ?>
                    <tr>

                        <!-- <td><?php echo $counter; ?></td> -->
                        <td class="manage-column column-categories"><?php echo $Value->order_id; ?></td>
                        <td class="manage-column column-categories"><?php echo date_i18n(get_option('date_format'), strtotime($Value->date)) ?></td>
                        <td class="manage-column column-categories"><?php echo $Value->payment_method; ?></td>
                        <td class="manage-column column-categories"><?php echo $price; ?></td>


                        <td class="manage-column column-categories">
                            <form id="posts-filter" method="POST">
                                <?php
                                if (!empty($invoiceStatus)) {
                                    if ($invoiceStatus == "success") {
                                ?>
                                        <input class="alert alert-success" type="button" value="<?php echo esc_html__('Active', 'listingpro-plugin'); ?>">
                                    <?php } elseif ($invoiceStatus == "failed") {
                                    ?>
                                        <input class="alert alert-danger" type="button" value="<?php echo esc_html__('Failed', 'listingpro-plugin'); ?>">
                                        <?php
                                    } elseif ($invoiceStatus == "pending" || $invoiceStatus == "in progress") {
                                        if ($Value->payment_method == "wire") {
                                            //new code 2.6.15
                                            if ($Value->payment_method == 'wire' || $Value->payment_method == 'WIRE') {
                                                $Value->payment_method = esc_html__('wire', 'listingpro');
                                            }
                                            //end new code 2.6.15
                                        ?>
                                            <input class="alert alert-info" type="submit" value="<?php echo esc_html__('Pending', 'listingpro-plugin'); ?>">
                                            <input type="hidden" name="payment_submitt" value="proceed">
                                            <input type="hidden" name="order_id" value="<?php echo $Value->order_id; ?>">
                                            <input type="hidden" name="post_id" value="<?php echo $Value->post_id; ?>">
                                        <?php
                                        } else {
                                        ?>
                                            <input class="alert alert-info" type="button" value="<?php echo esc_html__('Pending', 'listingpro-plugin'); ?>">
                                    <?php
                                        }
                                    }
                                    ?>

                                <?php
                                }
                                ?>
                            </form>
                        </td>

                        <td>
                            <?php
                            $deletelistidfield = '';
                            $delete_invoicen = 'delete_invoice';
                            if ($invoiceStatus == "pending" || $invoiceStatus == "in progress") {
                                $delete_invoicen = 'delete_invoicee';
                                $deletelistidfield = '<input type="hidden" name="list_id" value="' . $listid . '" />';
                            }
                            ?>
                            <form class="wp-core-ui" method="post">
                                <input type="submit" name="<?php echo $delete_invoicen; ?>" class="button action" value="<?php echo esc_html__('Delete', 'listingpro-plugin'); ?>" onclick="return window.confirm('Are you sure you want to proceed action?');" />
                                <input type="hidden" name="main_id" value="<?php echo $main_id; ?>" />
                                <?php echo $deletelistidfield; ?>

                            </form>
                        </td>

                        <td>
                            <a href="#" class="lp_watchthisinvoice" data-invoiceid="<?php echo $main_id; ?>" data-type="listing"><span class="dashicons dashicons-visibility"></span></a>
                            <div class="lobackspinner"></div>

                        </td>



                    </tr>
                <?php
                    $counter++;
                } elseif ($Value->payment_method != 'paypal') {
                    //new code 2.6.15
                    if ($Value->payment_method == 'wire' || $Value->payment_method == 'WIRE') {
                        $Value->payment_method = esc_html__('wire', 'listingpro');
                    }
                    //end new code 2.6.15
                ?>
                    <tr>

                        <!-- <td><?php echo $counter; ?></td> -->
                        <td class="manage-column column-categories"><?php echo $Value->order_id; ?></td>
                        <td class="manage-column column-categories"><?php echo date_i18n(get_option('date_format'), strtotime($Value->date)) ?></td>
                        <td class="manage-column column-categories"><?php echo $Value->payment_method; ?></td>
                        <td class="manage-column column-categories"><?php echo $price; ?></td>


                        <td class="manage-column column-categories">
                            <form id="posts-filter" method="POST">
                                <?php
                                if (!empty($invoiceStatus)) {
                                    if ($invoiceStatus == "success") {
                                ?>
                                        <input class="alert alert-success" type="button" value="<?php echo esc_html__('Active', 'listingpro-plugin'); ?>">
                                    <?php } elseif ($invoiceStatus == "failed") {
                                    ?>
                                        <input class="alert alert-danger" type="button" value="<?php echo esc_html__('Failed', 'listingpro-plugin'); ?>">
                                        <?php
                                    } elseif ($invoiceStatus == "pending" || $invoiceStatus == "in progress") {
                                        if ($Value->payment_method == "wire") {
                                            //new code 2.6.15
                                            if ($Value->payment_method == 'wire' || $Value->payment_method == 'WIRE') {
                                                $Value->payment_method = esc_html__('wire', 'listingpro');
                                            }
                                            //end new code 2.6.15
                                        ?>
                                            <input class="alert alert-info" type="submit" value="<?php echo esc_html__('Pending', 'listingpro-plugin'); ?>">
                                            <input type="hidden" name="payment_submitt" value="proceed">
                                            <input type="hidden" name="order_id" value="<?php echo $Value->order_id; ?>">
                                            <input type="hidden" name="post_id" value="<?php echo $Value->post_id; ?>">
                                        <?php
                                        } else {
                                        ?>
                                            <input class="alert alert-info" type="button" value="<?php echo esc_html__('Pending', 'listingpro-plugin'); ?>">
                                    <?php
                                        }
                                    }
                                    ?>

                                <?php
                                }
                                ?>
                            </form>
                        </td>

                        <td>
                            <?php
                            $deletelistidfield = '';
                            $delete_invoicen = 'delete_invoice';
                            if ($invoiceStatus == "pending" || $invoiceStatus == "in progress") {
                                $delete_invoicen = 'delete_invoicee';
                                $deletelistidfield = '<input type="hidden" name="list_id" value="' . $listid . '" />';
                            }
                            ?>
                            <form class="wp-core-ui" method="post">
                                <input type="submit" name="<?php echo $delete_invoicen; ?>" class="button action" value="<?php echo esc_html__('Delete', 'listingpro-plugin'); ?>" onclick="return window.confirm('Are you sure you want to proceed action?');" />
                                <input type="hidden" name="main_id" value="<?php echo $main_id; ?>" />
                                <?php echo $deletelistidfield; ?>

                            </form>
                        </td>

                        <td>
                            <a href="#" class="lp_watchthisinvoice" data-invoiceid="<?php echo $main_id; ?>" data-type="listing"><span class="dashicons dashicons-visibility"></span></a>
                            <div class="lobackspinner"></div>

                        </td>



                    </tr>
            <?php
                    $counter++;
                }
            }
        } else {
            ?>
            <tr>
                <td colspan="7"><?php echo esc_html__("Sorry! there is no result", "listingpro-plugin"); ?></td>
            </tr>

        <?php } ?>

    </tbody>

    <tfoot>
        <tr>

            <th class="manage-column column-title column-primary sortable desc">
                <a><span>Receipt/invoice#</span>
                    <span class="sorting-indicator"></span></a>
            </th>
            <th class="manage-column column-tags"><?php echo esc_html__('Date', 'listingpro-plugin'); ?></th>
            <th class="manage-column column-tags"><?php echo esc_html__('Method', 'listingpro-plugin'); ?></th>
            <th class="manage-column column-tags"><?php echo esc_html__('Price', 'listingpro-plugin'); ?></th>
            <th class="manage-column column-tags"><?php echo esc_html__('Status', 'listingpro-plugin'); ?></th>
            <th class="manage-column column-tags"><?php echo esc_html__('Action', 'listingpro-plugin'); ?></th>
            <th class="manage-column column-tags"><?php echo esc_html__('Watch', 'listingpro-plugin'); ?></th>
        </tr>
    </tfoot>
</table>