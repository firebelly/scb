<?php
/**
 * Extra fields for Taxonomies
 */

function taxonomy_metadata_cmb2_init() {

    // Including Taxonomy_MetaData_CMB2.php. Update to reflect your file structure
    if ( ! class_exists( 'Taxonomy_MetaData_CMB2' ) ) {
        require_once( __DIR__.'/Taxonomy_MetaData/Taxonomy_MetaData_CMB2.php' );
    }

    $metabox_id = 'cat_options';

    /**
     * Semi-standard CMB metabox/fields registration
     */
    $cmb = new_cmb2_box( array(
        'id'           => $metabox_id,
        'object_types' => array( 'key' => 'options-page', 'value' => array( 'unknown', ), ),
    ) );

    $cmb->add_field( array(
        'name' => __( 'Color', 'taxonomy-metadata' ),
        'desc' => __( 'Pick color for category', 'taxonomy-metadata' ),
        'id'   => 'color', // no prefix needed since the options are one option array.
        'type' => 'colorpicker',
    ) );

    /**
     * Instantiate our taxonomy meta class
     */
    $cats = new Taxonomy_MetaData_CMB2( 'project_category', $metabox_id, __( 'Category Settings', 'taxonomy-metadata' ) );
}
add_action( 'cmb2_init', 'taxonomy_metadata_cmb2_init' );