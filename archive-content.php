<?php
/**
 * The template for displaying content archive
 */

$posts = get_posts(array(
    'numberposts' => -1,
    'post_type' => 'content'
));

if($posts) {
    ?><h3>List of Content</h3><?php
    foreach ($posts as $p) {
        echo '<h2>' . get_the_title($p) . '</h2>';
        echo get_post_field('post_content', $p);
    }
}