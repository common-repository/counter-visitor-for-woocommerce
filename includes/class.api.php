<?php
if(!defined('ABSPATH')) { exit; }
if( !class_exists( 'WCVisitor_API' ) ) {
    class WCVisitor_API {
        function __construct() {
            add_action( 'wp_ajax_nopriv_wcvisitor_get_counter', array($this, 'WCVisitor_get_counter') );
            add_action( 'wp_ajax_wcvisitor_get_counter', array($this, 'WCVisitor_get_counter') );
        }
        function WCVisitor_get_counter() {
            global $WCVISITOR_MAIN;
            $product = sanitize_text_field( $_POST['product'] );
            $string = $WCVISITOR_MAIN->wcvisitor_show_api($product);
            if(!$string) {
                $res = array();
            }
            $res = array(
                'html' => $string,
                'counter' => $WCVISITOR_MAIN->wcvisitor_get_counter()
            );
            wp_send_json($res);
            wp_die();
        }

        
    }
    $WCVisitor_API = new WCVisitor_API();
}
?>