<?php

class SDEV_Controller {
    
    private $contentLoader;
    private $cityLoader;
    
    public function __construct(SDEV_Loader $contentLoader, SDEV_Loader $cityLoader) {
        $this->contentLoader = $contentLoader;
        $this->cityLoader = $cityLoader;
    }
    
    private function processContent($contentIds) { 
        
        $contentMarkup = array_map(function ($sec, $id) use ($contentIds) {
            $tempPost = get_post($id);
            
            $tempName = '';
            
            if (strpos($sec, '-split-left') !== false) {
                $rightSec = str_replace('-split-left', '-split-right', $sec);
                $rightId = $contentIds[$rightSec];
                
                $tempContentL = get_post_field('post_content', $id);
                $tempContentR = get_post_field('post_content', $rightId);
                $sectionId = str_replace('-split-left', '', $sec);
                $processedMarkup = sprintf('<section id="sdev-%s" class="sdev-split"><div class="container"><h2>%s</h2><div class="%s sdev-split-col">%s</div><div class="%s sdev-split-col">%s</div></div></section>', $sectionId, $tempName, $sec, $tempContentL, $rightSec, $tempContentR);
                
                return [$sec, $processedMarkup];
            } elseif (strpos($sec, '-split-right') === false) {
                if ($sec === 'faqs') {
                    $processedMarkup = sprintf('<section id="sdev-%s"><div class="container"><h2>FAQs</h2><div class="faqs-container">', $sec);
                    foreach ($id as $val) {
                        $tempTitle = get_post_field('post_title', $val);
                        $tempContent = get_post_field('post_content', $val);
                        $processedMarkup .= sprintf('<div class="faq"><h2>%s</h2><p>%s</p></div>', $tempTitle, $tempContent);
                    }
                    $processedMarkup .= sprintf('</div><a href="/faq"><h5>View All FAQs</h5></a></div></section>');
                    return [$sec, $processedMarkup];
                }
                $tempContent = get_post_field('post_content', $id);
                $processedMarkup = sprintf('<section id="sdev-%s"><div class="container"><h2>%s</h2>%s</div></section>', $sec, $tempName, $tempContent);
                return [$sec, $processedMarkup];
            } else {
                return '';
            }
        }, array_keys($contentIds), $contentIds);

        $contentMarkup = array_combine(array_column($contentMarkup, 0), array_column($contentMarkup, 1));
        
        foreach ($contentMarkup as $sec => $content) {
            foreach ($this->cityLoader->theData() as $key => $val) {
                if ($key === '_phone') {
                    $val = '531.329.4400';
                    $link_val = '5313294400';
                    $key = '{' . $key . '}'; 
                    $link_key = '%7b_phone%7d';
                    $content = str_ireplace($key, $val, $content);
                    $content = str_ireplace($link_key, $link_val, $content);
                } 
                else { 
                    $key = '{' . $key . '}';
                    $content = str_ireplace($key, $val, $content); 
                }
            }
            $updatedContent[$sec] = $content;
        }
        
        if ($updatedContent) { return $updatedContent; }
    }
    
    private function get_term_name_by_slug($slug) {
        $term = get_term_by('slug', $slug, 'content');
        if ($term) {
            return $term->name;
        }
        return '';
    }
    public function theData(){
        $cityData = $this->cityLoader->theData();
        $contentData = $this->contentLoader->theData();
        $contentData = $this->processContent($contentData);
        return ['city' => $cityData, 'content' => $contentData];
    }
}