<?php
/**
 * Discord class
 */

// Exit if accessed directly.
if ( !defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Main plugin class.
 */
class HELPDOCS_DISCORD {

    /**
     * Discord webhook
     * Please do not abuse this webhook or use it for anything else; it helps me manage feedback on Discord rather than through email
     * 
     * @var string
     */
    private $webhook = '2uF_aUoerJIYF7l9VPNcIG13z2zONdVGd_7kpmdHmSUaBIrycyk7gpgnvG8DEfjCiPWy';
    

    // $args = [
    //     'msg'            => 'This is a test',
    //     'embed'          => true,
    //     'author_name'    => 'Apos37',
    //     'author_url'     => HELPDOCS_AUTHOR_URL,
    //     'title'          => 'My title',
    //     'title_url'      => 'https://mytitleurl.com',
    //     'desc'           => 'The description',
    //     'img_url'        => '',
    //     'thumbnail_url'  => '',
    //     'disable_footer' => true,
    //     'fields'         => [
    //          [
    //              'name'   => 'Field #2 Name',
    //              'value'  => 'Field #2 Value',
    //              'inline' => true
    //          ]
    //      ]
    // ];
    /**
     * Send a message to our Dev Debug Tools server
     * https://discord.com/developers/docs/resources/channel
     *
     * @param array $args
     * @param string $webhook
     * @return boolean
     */
    public function send( $args ) {
        // Timestamp
        $timestamp = gmdate( 'c', strtotime( 'now' ) );

        // Message data
        $data = [
            // Text-to-speech
            'tts' => false,
        ];

        // Message
        if ( isset( $args[ 'msg'] ) && sanitize_textarea_field( $args[ 'msg' ] ) != '' ) {
            $data[ 'content' ] = sanitize_textarea_field( $args[ 'msg' ] );
        }

        // Change name of bot; default is DevDebugTools
        if ( isset( $args[ 'bot_name'] ) && sanitize_text_field( $args[ 'bot_name'] ) != '' ) {
            $data[ 'username' ] = sanitize_text_field( $args[ 'bot_name'] );
        }

        // Change bot avatar url
        if ( isset( $args[ 'bot_avatar_url'] ) && filter_var( $args[ 'bot_avatar_url' ], FILTER_SANITIZE_URL ) != '' ) {
            $data[ 'avatar_url' ] = filter_var( $args[ 'bot_avatar_url' ], FILTER_SANITIZE_URL );
        }

        // Embed
        if ( isset( $args[ 'embed' ] ) && filter_var( $args[ 'embed' ], FILTER_VALIDATE_BOOLEAN ) == true ) {
            $data[ 'embeds' ] = [
                [
                    // Embed Type
                    'type' => 'rich',

                    // Embed left border color in HEX
                    'color' => hexdec( '2A70A1' ),

                    // Fields
                    'fields' => $args[ 'fields' ],
                ]
            ];

            // Are we adding the footer?
            if ( !isset( $args[ 'disable_footer' ] ) || $args[ 'disable_footer' ] !== true ) {
                // Footer
                $data[ 'embeds' ][0][ 'footer' ] = [
                    'text' => HELPDOCS_AUTHOR_URL,
                    'icon_url' => "https://avatars.githubusercontent.com/u/58490438?v=4"
                ];
                $data[ 'embeds' ][0][ 'timestamp' ] = $timestamp;
            }

            // Embed author
            if ( isset( $args[ 'author_name' ] ) && $args[ 'author_name' ] != '' && 
                    isset( $args[ 'author_url' ] ) && $args[ 'author_url' ] != '' ) {
                $data[ 'embeds' ][0][ 'author' ][ 'name' ] = esc_attr( $args[ 'author_name' ] );
                $data[ 'embeds' ][0][ 'author' ][ 'url' ] = esc_url( $args[ 'author_url' ] );
            }

            // Embed title
            if ( isset( $args[ 'title' ] ) && $args[ 'title' ] != '' ) {
                $data[ 'embeds' ][0][ 'title' ] = esc_html( $args[ 'title' ] );
            }

            // Embed title link
            if ( isset( $args[ 'title_url' ] ) && $args[ 'title_url' ] != '' ) {
                $data[ 'embeds' ][0][ 'url' ] = esc_url( $args[ 'title_url' ] );
            }

            // Embed description
            if ( isset( $args[ 'desc' ] ) && $args[ 'desc' ] != '' ) {
                $data[ 'embeds' ][0][ 'description' ] = esc_html( $args[ 'desc' ] );
            }

            // Embed attached image
            if ( isset( $args[ 'img_url' ] ) && $args[ 'img_url' ] != '' ) {
                $data[ 'embeds' ][0][ 'image' ][ 'url' ] = esc_url( $args[ 'img_url' ] );
            }

            // Embed thumbnail
            if ( isset( $args[ 'thumbnail_url' ] ) && $args[ 'thumbnail_url' ] != '' ) {
                $data[ 'embeds' ][0][ 'thumbnail' ][ 'url' ] = esc_url( $args[ 'thumbnail_url' ] );
            }
        }

        // Encode
        $json_data = wp_json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );

        // Convert webhook url
        $webhook_url = base64_decode( 'aHR0cHM6Ly9kaXNjb3JkLmNvbS9hcGkvd2ViaG9va3MvMTEyMzYzMDIzNTQ2MDU3OTM5OC8' ).$this->webhook;

        // Send it to discord
        $options = [
            'body'        => $json_data,
            'headers'     => [
                'Content-Type' => 'application/json',
            ],
            'timeout'     => 60,
            'redirection' => 5,
            'blocking'    => true,
            'httpversion' => '1.0',
            'sslverify'   => false,
            'data_format' => 'body',
        ];
        $send = wp_remote_post( esc_url( $webhook_url ), $options );
        if ( !is_wp_error( $send ) && !empty( $send ) ) {
            return true;
        } else {
            return false;
        }
    } // End send()
}