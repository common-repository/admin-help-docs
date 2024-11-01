<?php
/**
 * Feedback class
 */

// Exit if accessed directly.
if ( !defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Initiate the class
 */
add_action( 'init', function() {
    (new HELPDOCS_FEEDBACK)->init();
} );


/**
 * Main plugin class.
 */
class HELPDOCS_FEEDBACK {

    /**
     * Load on init, but not every time the class is called
     *
     * @return void
     */
    public function init() {
        
        // Ajax
        add_action( 'wp_ajax_'.HELPDOCS_GO_PF.'send_feedback', [ $this, 'send' ] );
        add_action( 'wp_ajax_nopriv_'.HELPDOCS_GO_PF.'send_feedback', [ $this, 'send' ] );

        // On failure
        add_action( 'wp_mail_failed', [ $this, 'mail_error' ], 10, 1 );

    } // End init()


    /**
     * Send the message
     *
     * @return void
     */
    public function send() {
        // First verify the nonce
        if ( !wp_verify_nonce( sanitize_text_field( wp_unslash ( $_REQUEST[ 'nonce' ] ) ), HELPDOCS_GO_PF.'feedback' ) ) {
            exit( 'No naughty business please.' );
        }

        // Get the stuff
        $name = isset( $_REQUEST[ 'name' ] ) ? sanitize_text_field( $_REQUEST[ 'name' ] ) : false;
        $email = isset( $_REQUEST[ 'email' ] ) ? sanitize_email( $_REQUEST[ 'email' ] ) : false;
        $msg = isset( $_REQUEST[ 'msg' ] ) ? sanitize_text_field( $_REQUEST[ 'msg' ] ) : false;
       
        // Check for a message
        if ( $name && $email && $msg ) {
           
            // To email
            $to = HELPDOCS_AUTHOR_EMAIL;

            // Subject
            $subject = HELPDOCS_NAME.' Feedback | '.helpdocs_get_domain();
                    
            // The message
            $message = $msg;

            // Headers
            $headers[] = 'From: '.$name.' <'.$email.'>';
            $headers[] = 'Content-Type: text/html; charset=UTF-8';

            // Mail it
            $mail = wp_mail( $to, $subject, $message, $headers );
           
            // If mail was sent, return success
            if ( $mail ) {
                 $result[ 'type' ] = 'success';

            // Otherwise return error
            } else {
                $result[ 'type' ] = 'error';
                $error = get_option( HELPDOCS_GO_PF.'feedback_error' );
                $result[ 'err' ] = $error->errors;
                $result[ 'what' ] = 'Error: Mail Failure ['.base64_encode( $error->errors[ 'wp_mail_failed' ][0] ).']. ';
            }
                        
        // Otherwise return error
        } else {
            $result[ 'type' ] = 'error';

            // Catch the errors
            $errors = [];
            if ( !$name ) {
                $errors[] = base64_encode( 'name' );
            } elseif ( !$email ) {
                $errors[] = base64_encode( 'email' );
            } elseif ( !$msg ) {
                $errors[] = base64_encode( 'message' );
            }
            $result[ 'what' ] = 'Errors: '.implode( ', ', $errors );
        }
       
        // Pass to ajax
        if ( !empty( $_SERVER[ 'HTTP_X_REQUESTED_WITH' ] ) && strtolower( sanitize_key( $_SERVER[ 'HTTP_X_REQUESTED_WITH' ] ) ) == 'xmlhttprequest' ) {
            echo wp_json_encode( $result );
        } else {
            header( 'Location: '.filter_var( $_SERVER[ 'HTTP_REFERER' ], FILTER_SANITIZE_URL ) );
        }

        // Stop
        die();
    } // End send()


    /**
     * Do something if there is an error sending feedback
     *
     * @param [type] $wp_error
     * @return void
     */
    public function mail_error( $wp_error ) {
        update_option( HELPDOCS_GO_PF.'feedback_error', $wp_error );
    } // End mail_error()
}