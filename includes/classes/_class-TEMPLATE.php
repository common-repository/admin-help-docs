<?php
/**
 * Class template file. Copy and use for other classes.
 */

// Exit if accessed directly.
if ( !defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Initiate the class
 */
new HELPDOCS_CLASS_NAME;

add_action( 'init', function() {
    (new HELPDOCS_CLASS_NAME)->init();
} );



/**
 * Main plugin class.
 */
class HELPDOCS_CLASS_NAME {

    /**
	 * Constructor
	 */
	public function __construct() {



	} // End __construct()


    /**
     * Load on init, but not every time the class is called
     *
     * @return void
     */
    public function init() {
        

    } // End init()
}