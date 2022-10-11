<?php
/*---------------------------------------------------------
Plugin Name: WP Professional Profile
Author: carlosramosweb
Author URI: https://criacaocriativa.com
Donate link: https://donate.criacaocriativa.com
Description: Esse plugin é uma versão BETA. Shortcode [professional_profile]
Text Domain: wp-professional-profile
Domain Path: /languages/
Version: 1.0.0
Requires at least: 3.5.0
Tested up to: 5.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html 
------------------------------------------------------------*/

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

if ( ! class_exists( 'WP_Professional_Profile' ) ) {   

    class WP_Professional_Profile {

        public function __construct() {
            add_action( 'plugins_loaded', array( $this, 'init_functions' ) );
            register_activation_hook( __FILE__, array( $this, 'activate_plugin' ) );
            //register_deactivation_hook( __FILE__, array( $this, 'deactivate_plugin' ) );
        }
        //=>

        public function init_functions() {
            add_action( 'init', array( $this, 'load_plugin_textdomain' ) );
            add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'plugin_action_links_settings' ) );
            add_action( 'init', array( $this, 'register_posttype' ) );
            add_action( 'save_post', array( $this, 'save_meta_box' ) );
            add_action( 'add_meta_boxes', array( $this, 'register_meta_boxes' ) ); 
            add_shortcode( 'professional_profile', array( $this, 'get_professional_profile' ) );  
        }
        //=>

        public static function plugin_action_links_settings( $links ) { 
            $settings_url   = esc_url( admin_url( 'edit.php?post_type=professional-profile' ) );
            $donate_url     = esc_url( 'https://donate.criacaocriativa.com' );
            $settings_text  = __( 'Settings Plugin', 'wp-professional-profile' );
            $donate_text    = __( 'Donation Plugin', 'wp-professional-profile' );

            $action_links = array(
                'settings'  => '<a href="' . $settings_url . '" title="'. $settings_text .'" class="error">'. $settings_text .'</a>',
                'donate'    => '<a href="' . $donate_url . '" title="'. $donate_text .'" class="error">'. $donate_text .'</a>',
            );  
            return array_merge( $action_links, $links );
        }
        //=>

        public static function load_plugin_textdomain() {
            load_plugin_textdomain( 'wp-professional-profile', false, dirname( plugin_basename(__FILE__) ) . '/languages' );
        }
        //=>

        public function register_posttype() {
            $args = array(
                'public'                => true,
                'label'                 => 'Locutores',
                'public_queryable'      => true,
                'exclude_from_search'   => true,
                'show_ui'               => true,
                'show_in_menu'          => true,
                'show_in_nav_menus'     => true,
                'show_in_admin_bar'     => true,
                'capability_type'       => 'post',
                'query_var'             => true,
                'menu_icon'             => 'dashicons-microphone',
                'supports'              => array( 'title', 'thumbnail' ), 
                'rewrite'               => array(
                    'slug'          => 'professional-profile',
                    'with_front'    => false
                ),
                // 'title', 'editor', 'comments', 'revisions', 'trackbacks', 'author', 'excerpt', 'page-attributes', 'thumbnail', 'custom-fields', and 'post-formats'
            );
            register_post_type( 'professional-profile', $args );
        }
        //=>

        public function register_meta_boxes() {
            add_meta_box( 
                'meta-box-id', 
                __( 'Configuração', 'wp-professional-profile' ), 
                array( $this, 'professional_profile_display_callback' ),
                'professional-profile',
                'advanced',
                'high'
            );
        }
        //=>

        public function professional_profile_display_callback( $post ) { 
            $software = get_post_meta( get_the_ID(), '_software', true );
            if ( empty( $software ) ) {
                $software = "N/A";
            }
            $facebook = get_post_meta( get_the_ID(), '_facebook', true );
            if ( empty( $facebook ) ) {
                $facebook = "#";
            }
            $whatsapp = get_post_meta( get_the_ID(), '_whatsapp', true );
            if ( empty( $whatsapp ) ) {
                $whatsapp = "#";
            }
            $instagram = get_post_meta( get_the_ID(), '_instagram', true );
            if ( empty( $instagram ) ) {
                $instagram = "#";
            }
            ?>
            <p class="form-field _software_field ">
                <label for="_software">Programas</label>
                <input type="text" class="input_text" name="software" value="<?php echo $software; ?>" placeholder="Artist">
            </p>
            <p class="form-field _facebook_field ">
                <label for="_facebook">Facebook</label>
                <input type="text" class="input_link" name="facebook" value="<?php echo $facebook; ?>" placeholder="https://">
            </p>
            <p class="form-field _whatsapp_field ">
                <label for="_whatsapp">WhatsApp</label>
                <input type="text" class="input_link" name="whatsapp" value="<?php echo $whatsapp; ?>" placeholder="https://">
            </p>
            <p class="form-field _instagram_field ">
                <label for="_instagram">Instagram</label>
                <input type="text" class="input_link" name="instagram" value="<?php echo $instagram; ?>" placeholder="https://">
            </p>
            <br/>
            <?php
        }
        //=>

        public function save_meta_box( $post_id ) {
            if ( isset( $_POST['post_type'] ) && $_POST['post_type'] == "professional-profile" ) {
                update_post_meta( $post_id, '_software', $_POST['software'] );
                update_post_meta( $post_id, '_facebook', $_POST['facebook'] );
                update_post_meta( $post_id, '_whatsapp', $_POST['whatsapp'] );
                update_post_meta( $post_id, '_instagram', $_POST['instagram'] );
            }
        }
        //=>

        public function get_professional_profile( $atts ) {
            global $post;

            $args = array(
                'numberposts' => 10,
                'post_type'   => 'professional-profile',
                'orderby'    => 'menu_order',
                'sort_order' => 'asc'
            );

            $professional = get_posts( $args );

            if ( $professional ) {
                echo '<ul class="professional-profile">';
                foreach ( $professional as $post ) { 
                    setup_postdata( $post ); 
                    $the_title = get_the_title();

                    $software = get_post_meta( get_the_ID(), '_software', true );
                    if ( empty( $software ) ) {
                        $software = "N/A";
                    }
                    $facebook = get_post_meta( get_the_ID(), '_facebook', true );
                    if ( empty( $facebook ) ) {
                        $facebook = "#";
                    }
                    $whatsapp = get_post_meta( get_the_ID(), '_whatsapp', true );
                    if ( empty( $whatsapp ) ) {
                        $whatsapp = "#";
                    }
                    $instagram = get_post_meta( get_the_ID(), '_instagram', true );
                    if ( empty( $instagram ) ) {
                        $instagram = "#";
                    }

                    $thumbnail_url = get_the_post_thumbnail_url( get_the_ID(), 'thumbnail' ); 
                    if ( empty( $thumbnail_url ) ) {
                        $thumbnail_url = plugin_dir_url( __FILE__ ) . 'images/placeholder.jpg';
                    }

                    echo '<li class="item">';
                    echo '<div class="profile">';
                    echo '<img src="' . $thumbnail_url . '" alt="' . $the_title . '" class="thumbnail">';
                    echo '<strong class="title">' . $the_title . '</strong>';
                    echo '<strong class="software">' . $software. '</strong>';
                    echo '</div>';
                    echo '<div class="links">';
                    echo '<a href="' . $facebook . '" target="_blank" class="link" title="Facebook"><i class="bi bi-facebook"></i></a>';
                    echo '<a href="' . $whatsapp . '" target="_blank" class="link" title="WhatsApp"><i class="bi bi-whatsapp"></i></a>';
                    echo '<a href="' . $instagram . '" target="_blank" class="link" title="Instagram"><i class="bi bi-instagram"></i></a>';
                    echo '</div>';
                    echo '</li>';
                }
                echo '</ul>';
            }
            ?>
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
            <style type="text/css">
                ul.professional-profile { display: block; width: 100%; height: auto; margin: 0; padding: 0; }
                ul.professional-profile li.item { display: inline-block; width: calc(100% / 4); height: auto; margin: 0 10px 20px; padding: 0; vertical-align: top; text-align: center; }
                ul.professional-profile li.item .profile { text-align: center; }
                ul.professional-profile li.item .thumbnail { width: 100%; }
                ul.professional-profile li.item .title { display: block; font-size: 18px; font-weight: bold; }
                ul.professional-profile li.item .software { display: block; font-size: 14px; }
                ul.professional-profile li.item .links { display: block; border-top: 1px solid #CCC; margin: 10px 0; padding: 10px 0; }
                ul.professional-profile li.item .links a.link { display: inline-block; width: calc(100% / 3); margin: 0; padding: 0; }
                ul.professional-profile li.item .links a.link:hover { opacity: 0.5; }
                ul.professional-profile li.item .links a.link i { display: block; }
            </style>
            <?php
        }
        //=>

    }
    //=>
    new WP_Professional_Profile();
}