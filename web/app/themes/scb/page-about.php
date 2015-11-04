<?php
/**
 * Template Name: About
 */

$num_projects = \Firebelly\PostTypes\Project\get_num_projects();
$num_people = \Firebelly\PostTypes\Person\get_num_people();
$num_offices = \Firebelly\PostTypes\Office\get_num_offices();
?>

<header><?= $post->post_content ?></header>

<h2><?= $num_projects ?> Active Projects</h2>

<h2><?= $num_people ?> Design Professionals</h2>

<h2><?= $num_offices ?> Offices</h2>