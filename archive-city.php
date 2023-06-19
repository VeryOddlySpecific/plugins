<?php
/**
 * The template for displaying the city archive
 */
get_header();

$posts = get_posts(array(
    'numberposts' => -1,
    'post_type' => 'city'
));

?><main id="site-content"><?php
?><section id="cities-section"><?php
if($posts) {
    ?><h3>List of Cities</h3><?php
    ?><ul class="city-list"><?php
    foreach ($posts as $p) {
        $perm = get_permalink($p);
        $name = $p->post_title;
        echo "<li><a href='" . $perm . "'>" . $name . '</a></li>';
    }
    ?></ul><?php
}
    ?></section><?php
    ?></main><?php

get_footer();