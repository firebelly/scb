<?php
/**
 * Extra fields for Taxonomies
 */

/**
 * Hook in and add a metabox to add fields to taxonomy terms (needs Wordpress 4.4)
 */
// function register_taxonomy_metabox() {
//     $prefix = 'cmb2_term_';

//     /**
//     * Metabox to add fields to categories and tags
//     */
//     $cmb_term = new_cmb2_box( array(
//        'id'               => $prefix . 'edit',
//        'title'            => __( 'Category Options', 'cmb2' ),
//        'object_types'     => array( 'term' ), // Tells CMB2 to use term_meta vs post_meta
//        'taxonomies'       => array( 'project_category' ), // Tells CMB2 which taxonomies should have these fields
//        // 'new_term_section' => true, // Will display in the "Add New Category" section
//     ) );

//     $cmb_term->add_field( array(
//        'name' => __( 'Color', 'cmb2' ),
//        'desc' => __( 'Pick color for category', 'cmb2' ),
//        'id'   => $prefix . 'color',
//        'type' => 'colorpicker',
//     ) );
// }
// add_action( 'cmb2_admin_init', __NAMESPACE__ . '\register_taxonomy_metabox' );
