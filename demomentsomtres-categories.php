<?php

/*
  Plugin Name: DeMomentSomTres Categories
  Plugin URI: http://demomentsomtres.com/
  Description: Displays all categories based on shortcode.
  Version: 1.0
  Author: marcqueralt
  Author URI: http://demomentsomtres.com
  License: GPLv2 or later
 */

add_shortcode('DeMomentSomTres-Categories', 'demomentsomtres_categories_shortcode');

/**
 * Generates all the content of the shortcode
 * @param mixed $atts
 * @return string
 * @since 1.0
 */
function demomentsomtres_categories_shortcode($atts) {
    extract(shortcode_atts(array(
                'exclude' => '',
                    ), $atts));
    $args = array(
        'type' => 'post',
        'child_of' => '',
        'parent' => 0,
        'orderby' => 'slug',
        'order' => 'ASC',
        'hide_empty' => 1,
        'hierarchical' => 1,
        'exclude' => $exclude,
        'include' => '',
        'number' => '',
        'taxonomy' => 'category',
        'pad_counts' => false);
    $categories = get_categories($args);
    $output = '';
//    $output = '<pre>' . print_r($categories, true) . '</pre>'; //debug purposes
    $output .= '<ul class="dmst-categories">';
    foreach ($categories as $category) {
        $output .= '<li>';
        $output .= '<a href="' . get_category_link($category->term_id) . '" title="' . $category->name . '" ' . '>' . $category->name . '</a>: ';
        $output .= $category->description;
        $output .= '</li>';
    }
    $output .= '</ul>';
    return $output;
}

?>