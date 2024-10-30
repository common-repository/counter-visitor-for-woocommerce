<?php
if(!defined('ABSPATH')) { exit; }
global $WCVISITOR_MAIN;
/**Acciones */
if(isset($_POST['action'])) {
    if ( isset($_POST['save_option_nonce']) && wp_verify_nonce(  $_POST['save_option_nonce'], 'wcv_nonce' ) ) {
        if($_POST['action'] == 'save_options') {
           update_option('_wcv_timeout_limit',sanitize_text_field( $_POST['_wcv_timeout_limit'] ));
           update_option('_wcv_position',sanitize_text_field( $_POST['_wcv_position'] ));
           update_option('_wcv_icon',sanitize_text_field( $_POST['_wcv_icon'] ));
           if( $_POST['_wcv_weight_block'] == '') {
                //Prevent
                $_POST['_wcv_weight_block'] = 0;
           }
           update_option('_wcv_weight_block',sanitize_text_field( $_POST['_wcv_weight_block'] ));
           

           update_option('_wcv_message',sanitize_textarea_field( $_POST['_wcv_message'] ));
           update_option('_wcv_message_one',sanitize_textarea_field( $_POST['_wcv_message_one'] ));
            if(isset($_POST['_wcv_use_js'])) {
                update_option('_wcv_use_js','1');
            }else{
                update_option('_wcv_use_js','0');
            }
            if(isset($_POST['_wcvisitor_after_price'])) {
                update_option('_wcvisitor_after_price','1');
            }else{
                update_option('_wcvisitor_after_price','0');
            }

            if(isset($_POST['_wcvisitor_only_one_hide'])) {
                update_option('_wcvisitor_only_one_hide','1');
            }else{
                update_option('_wcvisitor_only_one_hide','0');
            }
            
            
            if(isset($_POST['_wcv_fake_mode'])) {
                update_option('_wcv_fake_mode','1');
            }else{
                update_option('_wcv_fake_mode','0');
            }

            update_option('_wcv_fake_mode_from', sanitize_text_field($_POST['_wcv_fake_mode_from']));
            update_option('_wcv_fake_mode_to', sanitize_text_field($_POST['_wcv_fake_mode_to']));

            /**
             * @since 1.1.4
             * Live mode added
             */
            if(isset($_POST['_wcv_live_mode'])) {
                update_option('_wcv_live_mode','1');
            }else{
                update_option('_wcv_live_mode','0');
            }

            if(isset($_POST['_wcv_fontawesome'])) {
                update_option('_wcv_fontawesome','1');
            }else{
                update_option('_wcv_fontawesome','0');
            }
            
            $seconds = intval(sanitize_text_field($_POST['_wcv_live_seconds']));

            if($seconds < 5) {
                $seconds = 5;
            }
            update_option('_wcv_live_seconds', $seconds);
            
        }
    }


    if ( isset($_POST['action']) && isset($_POST['add_sub_nonce']) && $_POST['action'] == 'adsub' && wp_verify_nonce(  $_POST['add_sub_nonce'], 'wcv_nonce' ) ) {
        $sub = wp_remote_post( 'https://mailing.danielriera.net', [
            'method'      => 'POST',
            'timeout'     => 2000,
            'redirection' => 5,
            'httpversion' => '1.0',
            'blocking'    => true,
            'headers'     => array(),
            'body'        => array(
                'm' => $_POST['action'],
                'd' => base64_encode(json_encode($_POST))
            ),
            'cookies'     => array()
        ]);
        $result = json_decode($sub['body'],true);

        if($result['error']) {
            $class = 'notice notice-error';
            $message = __( 'An error has occurred, try again.', 'counter-visitor-for-woocommerce' );
            printf( '<div class="%s"><p>%s</p></div>', $class, $message );
        }else{
            $class = 'notice notice-success';
            $message = __( 'Welcome newsletter :)', 'counter-visitor-for-woocommerce' );
            
            printf( '<div class="%s"><p>%s</p></div>', $class, $message );

            update_option('counter-visitor-newsletter' , '1');
        }
    }

    if ( isset($_POST['action']) && isset($_POST['add_sub_nonce']) && $_POST['action'] == 'delete_old_files' && wp_verify_nonce(  $_POST['add_sub_nonce'], 'wcv_nonce' ) ) {
        if(current_user_can('administrator')) {
            $files_deleted = $WCVISITOR_MAIN->wcvisitor_delete_old_files(WCVisitor_TEMP_FILES, true);
            $class = 'notice notice-success';
            $message = $files_deleted . ' ' . __( 'files deleted success', 'counter-visitor-for-woocommerce' );
            printf( '<div class="%s"><p>%s</p></div>', $class, $message );
        }else{
            $class = 'notice notice-error';
            $message = __( 'Permission Failed, need administrator rol for delete old files', 'counter-visitor-for-woocommerce' );
            printf( '<div class="%s"><p>%s</p></div>', $class, $message );
        }
    }
}
$newsletterCounterLive = get_option('counter-visitor-newsletter', '0');
$user = wp_get_current_user();
?>
<style>
form#new_subscriber {
    background: #FFF;
    padding: 10px;
    margin-bottom: 50px;
    border-radius: 12px;
    border: 1px solid #CCC;
    width: 23%;
    text-align: center;
}

form#new_subscriber input.email {
    width: 100%;
    text-align: center;
    padding: 10px;
}

form#new_subscriber input[type='submit'] {
    width: 100%;
    margin-top: 10px;
    border: 0;
    background: #3c853c;
    color: #FFF;
}

</style>

<div class="wrap wcvpanel">

    <h1><?=__('Counter Visitor for Woocommerce', 'counter-visitor-for-woocommerce')?></h1>
    <p><?=__('It is not a simple visitor counter, this counter is shown on each product with the number of users who are currently viewing that same product','counter-visitor-for-woocommerce')?></p>
    <?php if($newsletterCounterLive == '0') { ?>
            <form class="simple_form form form-vertical" id="new_subscriber" novalidate="novalidate" accept-charset="UTF-8" method="post">
                <input name="utf8" type="hidden" value="&#x2713;" />
                <input type="hidden" name="action" value="adsub" />
                <?php wp_nonce_field( 'wcv_nonce', 'add_sub_nonce' ); ?>
                <h3><?=__('Do you want to receive the latest?','counter-visitor-for-woocommerce')?></h3>
                <p><?=__('Thank you very much for using our plugin, if you want to receive the latest news, offers, promotions, discounts, etc ... Sign up for our newsletter. :)', 'counter-visitor-for-woocommerce')?></p>
                <div class="form-group email required subscriber_email">
                    <label class="control-label email required" for="subscriber_email"><abbr title="<?=__('Required', 'counter-visitor-for-woocommerce')?>"> </abbr></label>
                    <input class="form-control string email required" type="email" name="e" id="subscriber_email" value="<?=$user->user_email?>" />
                </div>
                <input type="hidden" name="n" value="<?=bloginfo('name')?>" />
                <input type="hidden" name="w" value="<?=bloginfo('url')?>" />
                <input type="hidden" name="g" value="1,6" />
                <input type="text" name="anotheremail" id="anotheremail" style="position: absolute; left: -5000px" tabindex="-1" autocomplete="off" />
            <div class="submit-wrapper">
            <input type="submit" name="commit" value="<?=__('Submit', 'counter-visitor-for-woocommerce')?>" class="button" data-disable-with="<?=__('Processing', 'counter-visitor-for-woocommerce')?>" />
            </div>
        </form>
    <?php

        } //END Newsletter
    $tab = 'general';
    if($tab == 'general') {
        $currentPosition = get_option('_wcv_position','woocommerce_after_add_to_cart_button');
        
        ?>

        <!--Donate button-->
        <div style="">
            <a href="https://www.paypal.com/donate/?hosted_button_id=EZ67DG78KMXWQ" target="_blank" style="text-decoration: none;font-size: 18px;border: 1px solid #333;padding: 10px;display: block;width: fit-content;border-radius: 10px;background: #FFF;"><?=__('Buy a Coffe? :)','counter-visitor-for-woocommerce')?></a>
        </div>
        <div class="clear_site"> </div>
            <?php
            $oldFiles = $WCVISITOR_MAIN->wcvisitor_delete_old_files(WCVisitor_TEMP_FILES);
            if($oldFiles > 0) {
                echo '<form novalidate="novalidate" method="post">
                    <h3>'.__('You can delete the old files generated more than 1 hour old','counter-visitor-for-woocommerce').'</h3>
                    <input type="hidden" name="action" value="delete_old_files" />
                    '.wp_nonce_field( 'wcv_nonce', 'add_sub_nonce' ).'
                    <input class="button" type="submit" value="'.__('Delete old files','counter-visitor-for-woocommerce').' ('.$oldFiles.')" />
                </form>';
            }
            ?>
       

        <form method="post">
            <input type="hidden" name="action" value="save_options" />
            <?php wp_nonce_field( 'wcv_nonce', 'save_option_nonce' ); ?>
            <table class="form-table">
            
                <tr valign="top">
                    <th scope="row"><?=__('Your site use cache system?', 'counter-visitor-for-woocommerce')?>
                        <p class="description"><?=__('Activate this option if your site uses some type of cache and add \'wcvisitor\' to the plugin cache exceptions','counter-visitor-for-woocommerce')?></p>
                    </th>
                    <td>
                        <label>
                        <input type="checkbox" name="_wcv_use_js" value="1" <?=checked('1', get_option('_wcv_use_js', '0'))?> /></label>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?=__('Show message after price', 'counter-visitor-for-woocommerce')?>
                        <p class="description"><?=__('Active this options for show counter after price with | separated','counter-visitor-for-woocommerce')?></p>
                    </th>
                    <td>
                        <label>
                        <input type="checkbox" name="_wcvisitor_after_price" value="1" <?=checked('1', get_option('_wcvisitor_after_price', '0'))?> /></label>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?=__('Hide counter if only one visitor', 'counter-visitor-for-woocommerce')?>
                        <p class="description"><?=__('Active this options for hide counter when only one visitor on product','counter-visitor-for-woocommerce')?></p>
                    </th>
                    <td>
                        <label>
                        <input type="checkbox" name="_wcvisitor_only_one_hide" value="1" <?=checked('1', get_option('_wcvisitor_only_one_hide', '0'))?> /></label>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?=__('Live Mode: Do you want to show users in real time?', 'counter-visitor-for-woocommerce')?>
                        <p class="description"><?=__('This option adds a call per user every X seconds, check its operation on your server, for security less than 5 seconds are not allowed. Use this option considering the resources of your server.','counter-visitor-for-woocommerce')?></p>
                    </th>
                    <td>
                        <label>
                        <input type="checkbox" name="_wcv_live_mode" value="1" <?=checked('1', get_option('_wcv_live_mode', '0'))?> /></label>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?=__('How often to update the number of users in the product?', 'counter-visitor-for-woocommerce')?>
                        <p class="description"><?=__('Seconds, min 5 seconds.. (Require Live Move)','counter-visitor-for-woocommerce')?></p>
                    </th>
                    <td>
                        <label>
                        <input type="number" name="_wcv_live_seconds" value="<?=get_option('_wcv_live_seconds','5')?>" min="5" /></label>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?=__('Duration', 'counter-visitor-for-woocommerce')?>
                        <p class="description"><?=__('Time since last activity of an users to be considered inactive','counter-visitor-for-woocommerce')?></p>
                    </th>
                    <td>
                        <label>
                        <input type="number" min="30" max="99999999" name="_wcv_timeout_limit" value="<?=get_option('_wcv_timeout_limit', '300')?>" /></label>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?=__('Position', 'counter-visitor-for-woocommerce')?></th>
                    <td>
                        <label>
                            <select name="_wcv_position">
                                <option value="woocommerce_after_add_to_cart_button" <?=selected('woocommerce_after_add_to_cart_button',$currentPosition);?>><?=__('After cart button','counter-visitor-for-woocommerce')?></option>
                                <option value="woocommerce_before_add_to_cart_button" <?=selected('woocommerce_before_add_to_cart_button',$currentPosition);?>><?=__('Before cart button','counter-visitor-for-woocommerce')?></option>
                                <option value="woocommerce_product_meta_end" <?=selected('woocommerce_product_meta_end',$currentPosition);?>><?=__('After product meta','counter-visitor-for-woocommerce')?></option>
                                <option value="woocommerce_before_single_product_summary" <?=selected('woocommerce_before_single_product_summary',$currentPosition);?>><?=__('Before product summary','counter-visitor-for-woocommerce')?></option>
                                <option value="woocommerce_after_single_product_summary" <?=selected('woocommerce_after_single_product_summary',$currentPosition);?>><?=__('After product summary','counter-visitor-for-woocommerce')?></option>
                                <option value="woocommerce_product_thumbnails" <?=selected('woocommerce_product_thumbnails',$currentPosition);?>><?=__('Product Thumbnail (may not work)','counter-visitor-for-woocommerce')?></option>
                                <option value="woocommerce_single_product_summary" <?=selected('woocommerce_single_product_summary',$currentPosition);?>><?=__('After short description','counter-visitor-for-woocommerce')?></option>
                                
                                <option value="deactivate" <?=selected('deactivate',$currentPosition);?>><?=__('Deactivate','counter-visitor-for-woocommerce')?></option>
                            </select>
                        </label>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?=__('Weight block', 'counter-visitor-for-woocommerce')?>
                        <p class="description"><?=__('The heavier the weight, the lower the block is displayed','counter-visitor-for-woocommerce')?></p>
                    </th>
                    <td>
                        <label>
                        <input type="number" min="0" max="300" name="_wcv_weight_block" value="<?=get_option('_wcv_weight_block', '0')?>" /></label>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?=__('Fake Mode', 'counter-visitor-for-woocommerce')?>
                        <p class="description"><?=__('Use Random numbers between from / to','counter-visitor-for-woocommerce')?></p>
                    </th>
                    <td>
                        <label>
                        <input type="checkbox" name="_wcv_fake_mode" value="1" <?=checked("1", get_option('_wcv_fake_mode','0'))?> /></label>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?=__('Random Numbers', 'counter-visitor-for-woocommerce')?>
                        <p class="description"><?=__('Need Fake mode, for visitors this value is saved for 25 minutes','counter-visitor-for-woocommerce')?></p>
                    </th>
                    <td>
                        <label>
                        <?=__('From:','');?> <input type="number" min="0" name="_wcv_fake_mode_from" value="<?=get_option('_wcv_fake_mode_from','0')?>" />
                        <?=__('To:','');?> <input type="number" min="0" name="_wcv_fake_mode_to" value="<?=get_option('_wcv_fake_mode_to','0')?>" />
                        </label>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?=__('Icon', 'counter-visitor-for-woocommerce')?>
                        <p class="description"><?=__('You can use always icon, fontawesome, only class name for example: fas fa-eye','counter-visitor-for-woocommerce')?></p>
                    </th>
                    <td>
                        <label>
                        <input type="text" name="_wcv_icon" value="<?=get_option('_wcv_icon','dashicons dashicons-visibility')?>" /></label>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?=__('¿Problem with Icon?', 'counter-visitor-for-woocommerce')?>
                        <p class="description"><?=__('Load FontAwesome Library.','counter-visitor-for-woocommerce')?></p>
                    </th>
                    <td>
                        <label>
                        <input type="checkbox" name="_wcv_fontawesome" value="1" <?=checked('1', get_option('_wcv_fontawesome', '0'))?> /></label>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?=__('Message more than one user', 'counter-visitor-for-woocommerce')?>
                        <p class="description"><?=__('%n is replaced by number visitors','counter-visitor-for-woocommerce')?></p>
                    </th>
                    <td>
                        <label>
                        <textarea type="text" style="width:250px;height:250px;" name="_wcv_message"><?=get_option('_wcv_message', __('%n people are viewing this product'))?></textarea></label>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?=__('Message only one user', 'counter-visitor-for-woocommerce')?>
                    </th>
                    <td>
                        <label>
                        <textarea type="text" style="width:250px;height:250px;" name="_wcv_message_one"><?=get_option('_wcv_message_one', __('1 user are viewing this product'))?></textarea></label>
                    </td>
                </tr>
                
                
            </table>
            <input type="submit" class="button" value="<?=__('Save','counter-visitor-for-woocommerce')?>" />
        </form>

        <h2><?=__('Need style?', 'counter-visitor-for-woocommerce')?></h2>
        <p><?=__('Enjoy! Paste this CSS code into your Customizer and edit as you like','counter-visitor-for-woocommerce')?></p>
<pre>
.wcv-message {
    
}
.wcv-message span.icon {

}

.wcv-message span.wcvisitor_num {
    
}
</pre>
    <?php
    }
    
    ?>

</div>