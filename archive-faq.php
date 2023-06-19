<?php
/**
 * The template for displaying the faq archive
 */

get_header();

$posts = get_posts(array(
    'numberposts' => -1,
    'post_type' => 'faq'
));

if($posts) {
    ?><main id="site-content" role="main"><?php
    ?><h1>Frequently Asked Questions</h1><?php
    foreach ($posts as $p) {
        echo '<div class="faq">';
        echo '<h2>' . get_the_title($p) . '</h2>';
        echo '<p>' . get_post_field('post_content', $p) . '</p>';
        echo '</div>';
    }
    ?></main><?php
}
get_footer();