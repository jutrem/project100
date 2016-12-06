<?php
/*
  Plugin Name: Simplistics Reviews for WordPress
  Plugin URI: http://www.simplistics.com/
  Description: WordPress plugin that pulls business reviews from Google Places api. Simplistics place id for the reviews:.
  Version: 1.0
  Author: RC
  Author URI: http://www.simplistics.com/
  License: GPLv2+
  Text Domain: wp-simplistics-reviews
*/


register_activation_hook( __FILE__, 'my_plugin_create_db' );
function my_plugin_create_db() {   
	global $wpdb;
	$charset_collate = $wpdb->get_charset_collate();
	$table_name = $wpdb->prefix . 'gplace_reviews';

	$sql = "CREATE TABLE $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
        author_name varchar(60),
        author_url varchar(120),
        language varchar(10),
        rating int,
        relative_time_description varchar(60),
        text varchar(500),
        time int,
		time2 datetime DEFAULT '0000-00-00 00:00:00',
		UNIQUE KEY id (id)
	) $charset_collate;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );
    }

register_activation_hook( __FILE__, 'insert_data_db' );
function insert_data_db(){ 
    	global $wpdb;
        $wpdb->show_errors(); 
	    $charset_collate = $wpdb->get_charset_collate();
	    $table_name = $wpdb->prefix . 'gplace_reviews';   
               $wpdb->insert($table_name, array(
                   'author_name' => 'Ted Tester',
                   'text' => 'Working with Simplistics.ca is a pleasure.',
                   'rating' => 3
                ));
}

add_action( 'wp_ajax_my_action', 'my_action_callback' );

/*
function my_action_callback() {
	global $wpdb; // this is how you get access to the database

	$whatever = intval( $_POST['whatever'] );

	$whatever += 10;

        echo $whatever;

        echo "** Action Callback **" ;
        debug_to_console( "** Callback action **" );

	wp_die(); // this is required to terminate immediately and return a proper response
}
*/

function debug_to_console( $data ) {

    if ( is_array( $data ) )
        $output = "<script>console.log( 'Debug Objects: " . implode( ',', $data) . "' );</script>";
    else
        $output = "<script>console.log( 'Debug Objects: " . $data . "' );</script>";

    echo $output;
}


class WP_Simplistics_Simple{

    // Constructor
    function __construct() {

    //var datainserted = insert_data_db();

    if ( !class_exists( 'Google_Client' ) ) {

        require_once dirname(__FILE__) . '/lib/google-api-php-client-master/src/Google/Client.php';
        /* require_once dirname(__FILE__) . '/lib/google-api-php-client-master/src/Google/Service/Analytics.php';*/
    }
    /*
    $this->client = new Google_Client();
    $this->client->setApprovalPrompt( 'force' );
    $this->client->setAccessType( 'offline' );
    $this->client->setClientId( '543710582064-0ctbbdc6kba24su19t6o7p25fcju593s.apps.googleusercontent.com' );
    $this->client->setClientSecret( 'PC1A_3GGoH8j9Ook-OsuMIhC' );
    $this->client->setRedirectUri( 'urn:ietf:wg:oauth:2.0:oob' );
    $this->client->setScopes( 'https://www.googleapis.com/auth/analytics' );    
    try{

        $this->service = new Google_Service_Analytics( $this->client );
        $this->wpa_connect();
    }
    catch ( Google_Service_Exception $e ) {

    }*/

        add_action( 'admin_menu', array( $this, 'wpa_add_menu' ));
        register_activation_hook( __FILE__, array( $this, 'wpa_install' ) );
        register_deactivation_hook( __FILE__, array( $this, 'wpa_uninstall' ) );
    }

      /*
      * Actions perform at loading of admin menu
      */
    function wpa_add_menu() {

        add_menu_page( 'Simplistics simple', 'Simplistics Rev', 'manage_options', 'simplistics-dashboard', array(
                          __CLASS__,
                         'wpa_page_file_path'
                        ), plugins_url('images/wp-simplistics-reviews-logo.png', __FILE__),'2.2.9');

        add_submenu_page( 'simplistics-dashboard', 'Simplistics simple' . ' Dashboard', ' Dashboard', 'manage_options', 'simplistics-dashboard', array(
                              __CLASS__,
                             'wpa_page_file_path'
                            ));

        add_submenu_page( 'simplistics-dashboard', 'Simplistics simple' . ' Settings', '<b style="color:#f9845b">Settings</b>', 'manage_options', 'simplistics-settings', array(
                              __CLASS__,
                             'wpa_page_file_path'
                            ));
    }

    public function wpa_connect() {

    $access_token = get_option('access_token');

    if (! empty( $access_token )) {

        $this->client->setAccessToken( $access_token );

    } 
    else{

        $authCode = get_option( 'access_code' );

        if ( empty( $authCode ) ) return false;

        try {

            $accessToken = $this->client->authenticate( $authCode );
        }
        catch ( Exception $e ) {
            return false;
        }

        if ( $accessToken ) {

            $this->client->setAccessToken( $accessToken );
            update_option( 'access_token', $accessToken );

            return true;
        }
        else {

            return false;
        }
    }

    $this->token = json_decode($this->client->getAccessToken());
    return true;

    }

    /*
     * Actions perform on loading of menu pages
     */
    function wpa_page_file_path() {

    }

    /*
     * Actions perform on activation of plugin
     */
    function wpa_install() {

    }

    /*
     * Actions perform on de-activation of plugin
     */
    function wpa_uninstall() {

    }

}

if ( ! empty( $_POST ) ) {
    // Sanitize the POST field    
        //echo "** POST recieved **" ;
        debug_to_console( "** POST recieved **" );
    insert_data_db();
}


new WP_Simplistics_Simple();

$plugindir = get_settings('home').'/wp-content/plugins/'.dirname(plugin_basename(__FILE__));
//wp_enqueue_script("jquery");
wp_enqueue_script('loadjs', $plugindir . '/simplisticreviews.js');
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <!--<meta charset="utf-8" />-->
        <style type="text/css">
            <?php include 'css/simplisticsreviews.css'; ?>
        </style>
        <title>Simplistics Reviews</title>
    </head>
    <body onload="initialize()">
         <div id="map" class="mapdisplay"></div>
         <div id="result" class="reviewsdisplay">
             <span id="resultspan" class="resulttitlespan"></span>
             <div id="result1"></div>
             <div id="result2">
             <?php include 'php/showreviewslist.php';?>          
             </div>
         </div>
    </body>
</html>
<script type="text/javascript" src="http://ajax.microsoft.com/ajax/jquery/jquery-1.6.2.min.js"></script>
<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?libraries=places&key=AIzaSyAvDIKeBpmz_nkXFSaWCNJwK6poIXPU5Qo"></script>
