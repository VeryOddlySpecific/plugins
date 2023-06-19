<?php

class SDEV_Loader {
    
    private $id;
    private $data;
    private $data_type;
    
    public function __construct( $type, $id ) {
        $this->id = $id;
        $this->data_type = $type;
        $this->setData($type);
    }
    
    private function getData($a_type){
        if ($a_type === 'city') {
            $meta_keys = array_keys(get_registered_meta_keys('city'));
            $meta_keys = array_diff($meta_keys, ['_content_ids']);
            $data = [];
            foreach ($meta_keys as $key) {
                $meta_value = get_post_meta(get_the_ID(), $key, true);
                $data[$key] = $meta_value;
            }
            return $data;
        } elseif ($a_type === 'content') {
            $meta_data = get_post_meta($this->id, '_content_ids', true);
            if (!$meta_data) {
                $sections = get_terms([
                    'taxonomy' => 'content_sections',
                    'hide_empty' => false,
                    'fields' => 'slugs'
                ]);
                $post_ids = [];
                foreach ($sections as $section) {
                    $ids = $this->getContent($section);
                    if ($ids) {
                        $index = array_rand($ids);
                        $post_ids[$section] = $ids[$index];
                    }
                }
                $post_ids['faqs'] = $this->getContent('faqs');
                $keys = array_keys($post_ids);
                $shuffled_keys = ['city-intro'];
                
                $key_index = array_search('city-intro', $keys);
                if ($key_index !== false) {
                    unset($keys[$key_index]);
                }
                
                shuffle($keys);
                $combined_keys = array_merge($shuffled_keys, $keys);
                $shuffled = [];
                foreach ($combined_keys as $key) {
                    $shuffled[$key] = $post_ids[$key];
                }
                update_post_meta($this->id, '_content_ids', $shuffled);
                return $shuffled;
            }
            return $meta_data;
        }
    }
    
    private function getContent($term){

        if ($term === 'faqs') {
            $args = [
                'post_type' => 'faq',
                'fields' => 'ids',
                'numberposts' => -1
            ];

            $all_faqs = get_posts($args);
            $faq_keys = array_rand($all_faqs, 3);

            $faq_data = array();

            if (is_array($faq_keys)) {
                foreach ($faq_keys as $key) {
                    $faq_data[] = $all_faqs[$key];
                }
            } else {
                $faq_data[] = $all_faqs[$faq_keys];
            }
            return $faq_data;
        }
        
        $args = [
            'post_type'     => 'content',
            'fields'        => 'ids',
            'numberposts'   => -1,
            'tax_query'     => [
                [
                    'taxonomy'  => 'content_sections',
                    'terms'     => $term,
                    'field'     => 'slug'
                ],
            ],
        ];
        
        $temp = get_posts($args);
        return $temp;
    }
    
    private function setData($type){ 
        $this->data = $this->getData($type);
    }
    
    public function theData(){ 
        return $this->data;
    }
}