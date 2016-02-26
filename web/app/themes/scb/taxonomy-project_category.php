<?php
/**
 * Project taxonomy page 
 */

$term = get_term_by('slug', get_query_var('term'), get_query_var('taxonomy'));
include(locate_template('templates/project-grid-top.php'));
