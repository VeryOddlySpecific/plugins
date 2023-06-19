<?php

/**
 * Template Name: Single City
 * 
 * The template for displaying location landing pages.
 */


get_header();
$contentLoader = new SDEV_Loader('content', get_the_ID());
$cityLoader = new SDEV_Loader('city', get_the_ID());
$controller = new SDEV_Controller($contentLoader, $cityLoader);

$postData = $controller->theData();
$cityData = $postData['city'];
$cityContent = get_post_field('post_content');
$contentData = $postData['content'];

$universalContent = get_option('amf_universal_content');
?>
<main id="site-content" role="main">
<?php
    if ($cityContent) {
        echo $cityContent;
    }
    if ($universalContent) {
        $renderedContent = apply_filters('the_content', $universalContent);
        echo $renderedContent;
    }
    if ($contentData) {
        foreach ($contentData as $section => $content) {
            $renderedContent = apply_filters('the_content', $content);
            echo $renderedContent;
        }
    }
    
?>    
</main>
<?php
get_footer();