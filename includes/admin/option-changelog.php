<?php
/**
 * The changelog page
 */

// Include the header
include 'header-page.php';

// Get the dev timezone
$timezone = get_option( HELPDOCS_GO_PF.'dev_timezone', wp_timezone_string() );

// Plugin installed date
if ( $installed_date = get_option( 'helpdocs_plugin_installed' ) ) {
    $installed_date = helpdocs_convert_timezone( $installed_date, 'F j, Y g:i A', $timezone );
    echo '<br>Plugin was installed on '.esc_html( $installed_date );
}

// Plugin activated date
if ( $activated_date = get_option( 'helpdocs_plugin_activated' ) ) {
    $activated_date = helpdocs_convert_timezone( $activated_date, 'F j, Y g:i A', $timezone );
    echo '<br>Plugin was last activated on '.esc_html( $activated_date );
}

// Add CSS for just this page
echo '<style>h3 { margin-bottom: 0; }</style>';

// Initialize the filesystem
global $wp_filesystem;
if ( empty( $wp_filesystem ) ) {
    require_once ABSPATH . 'wp-admin/includes/file.php';
    WP_Filesystem();
}
if ( ! $wp_filesystem ) {
    echo '<p>' . esc_html__( 'Unable to initialize the filesystem.', 'admin-help-docs' ) . '</p>';
    return;
}

// Get the file content
$file_path = HELPDOCS_PLUGIN_ROOT.'readme.txt';
$file = $wp_filesystem->get_contents( $file_path );

if ( false === $file ) {
    echo '<p>' . esc_html__( 'Unable to fetch the changelog at this time.', 'admin-help-docs' ) . '</p>';
    return;
}
// Extract the changelog
$changelog = strstr( $file, '= '. HELPDOCS_VERSION .' =' );

// Replace the versions and bullets
$changelog = str_replace( '= ', '<h3>', $changelog );
$changelog = str_replace( ' =', '</h3>', $changelog );

// Add the content
echo '<br><br><br>' . wp_kses_post( nl2br( $changelog ) );