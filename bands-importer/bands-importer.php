<?php
/*
Plugin Name: Bands Importer
Description: Plugin to import bands and genres from a JSON file.
Version: 1.0
Author: Cup O Code
License: GPL2
*/

// Register the import bands function to run on plugin activation
register_activation_hook( __FILE__, 'bands_importer_activate' );
function bands_importer_activate() {
    // Perform any activation tasks if needed
}

// Register the import bands function to run on plugin deactivation
register_deactivation_hook( __FILE__, 'bands_importer_deactivate' );
function bands_importer_deactivate() {
    // Perform any deactivation tasks if needed
}

// Import bands and genres from JSON file
function bands_importer_import_bands() {
    $file_path = plugin_dir_path( __FILE__ ) . 'bands.json';
    
    // Check if the file exists
    if ( file_exists( $file_path ) ) {
        $bands_data = file_get_contents( $file_path );

        // Decode JSON data
        $bands = json_decode( $bands_data, true );

        // Loop through bands and create posts
        foreach ( $bands as $band ) {
            $post_data = array(
                'post_title'    => $band['Band Name'],
                'post_type'     => 'our-bands',
                'post_status'   => 'publish',
            );

            // Insert the post into the database
            $post_id = wp_insert_post( $post_data );

            // Update band genre and website as post meta
            if ( $post_id ) {
                update_post_meta( $post_id, 'band_genre', $band['Genre'] );
                update_post_meta( $post_id, 'band_website', $band['Website'] );
            }
        }
    } else {
        // File not found
        echo 'Bands JSON file not found.';
    }
}

// Add a custom admin menu page to trigger the bands import
add_action( 'admin_menu', 'bands_importer_add_menu_page' );
function bands_importer_add_menu_page() {
    add_menu_page(
        'Bands Importer',
        'Bands Importer',
        'manage_options',
        'bands-importer',
        'bands_importer_menu_page'
    );
}

// Callback function for the custom admin menu page
function bands_importer_menu_page() {
    if ( isset( $_POST['import_bands'] ) ) {
        // Call the import bands function
        bands_importer_import_bands();
        echo 'Bands imported successfully.';
    }
    ?>
    <div class="wrap">
        <h1>Bands Importer</h1>
        <form method="post" action="">
            <input type="submit" name="import_bands" value="Import Bands" class="button button-primary">
        </form>
    </div>
    <?php
}
