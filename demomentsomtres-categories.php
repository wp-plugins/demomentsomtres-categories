<?php
/*
  Plugin Name: DeMomentSomTres Categories
  Plugin URI: http://demomentsomtres.com/english/wordpress-plugins/demomentsomtres-categories/
  Description: Displays all categories based on shortcode.
  Version: 2.2.1
  Author: marcqueralt
  Author URI: http://demomentsomtres.com
  License: GPLv2 or later
 */

define('DMS3_CATS_TEXT_DOMAIN', 'DeMomentSomTres-Categories');

// Make sure we don't expose any info if called directly
if (!function_exists('add_action')) {
    echo "Hi there!  I'm just a plugin, not much I can do when called directly.";
    exit;
}

require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
if (is_plugin_active('demomentsomtres-tools/demomentsomtres-tools.php')):
    require_once(ABSPATH . 'wp-content/plugins/demomentsomtres-tools/demomentsomtres-tools.php');

    $demomentsomtres_categories = new DeMomentSomTresCategories();
endif;

class DeMomentSomTresCategories {

    const MENU_SLUG = 'dmst_categories';
    const TEXT_DOMAIN = DMS3_CATS_TEXT_DOMAIN;
    const OPTIONS = 'dmst_categories_options';
    const PAGE = 'dmst_categories';
    const SECTION1 = 'dmst_cats_exclusions';
    
    const OPTION_EXCLUDED_CATS = 'excludedCategories';
    const OPTION_FILTER_CATS = 'OPTION_FILTER_CATS';

    private $pluginURL;
    private $pluginPath;
    private $langDir;

    /**
     * @since 2.0
     */
    function DeMomentSomTresCategories() {
        $this->pluginURL = plugin_dir_url(__FILE__);
        $this->pluginPath = plugin_dir_path(__FILE__);
        $this->langDir = dirname(plugin_basename(__FILE__)) . '/languages';

        add_action('plugins_loaded', array(&$this, 'plugin_init'));
        add_action('admin_menu', array(&$this, 'admin_menu'));
        add_action('admin_init', array(&$this, 'admin_init'));
        add_action('widgets_init', array(&$this, 'register_widgets'));
        add_shortcode('DeMomentSomTres-Categories', array(&$this, 'demomentsomtres_categories_shortcode'));
        if ('on' == DeMomentSomTresTools::get_option(self::OPTIONS, self::OPTION_FILTER_CATS)):
            add_filter('get_the_categories', array(&$this, 'the_category_filter'), 10, 2);
        endif;
    }

    /**
     * @since 2.0
     */
    function plugin_init() {
        load_plugin_textdomain(DMS3_CATS_TEXT_DOMAIN, false, $this->langDir);
    }

    /**
     * @since 2.0
     * @return boolean
     */
    function register_widgets() {
        return register_widget("DeMomentSomTresCategoriesWidget");
    }

    /**
     * @since 2.0
     */
    function admin_menu() {
        add_options_page(__('DeMomentSomTres Categories', DeMomentSomTresCategories::TEXT_DOMAIN), __('DeMomentSomTres Categories', DeMomentSomTresCategories::TEXT_DOMAIN), 'manage_options', DeMomentSomTresCategories::MENU_SLUG, array(&$this, 'admin_page'));
    }

    /**
     * @since 2.0
     */
    function admin_page() {
        ?>
        <div class="wrap">
            <h2><?php _e('DeMomentSomTres Categories', DeMomentSomTresCategories::TEXT_DOMAIN); ?></h2>
            <form action="options.php" method="post">
                <?php settings_fields(DeMomentSomTresCategories::OPTIONS); ?>
                <?php do_settings_sections(DeMomentSomTresCategories::PAGE); ?>
                <br/>
                <input name="Submit" class="button button-primary" type="submit" value="<?php _e('Save Changes', DeMomentSomTresCategories::TEXT_DOMAIN); ?>"/>
            </form>
        </div>
        <div style="background-color:#eee;display:none;">
            <h2><?php _e('Options', DeMomentSomTresCategories::TEXT_DOMAIN); ?></h2>
            <pre style="font-size:0.8em;"><?php print_r(get_option(DeMomentSomTresCategories::OPTIONS)); ?></pre>
        </div>
        <?php
    }

    /**
     * @since 2.0
     */
    function admin_init() {
        register_setting(DeMomentSomTresCategories::OPTIONS, DeMomentSomTresCategories::OPTIONS, array(&$this, 'admin_validate_options'));

        add_settings_section(DeMomentSomTresCategories::SECTION1, __('Excluded Categories', DeMomentSomTresCategories::TEXT_DOMAIN), array(&$this, 'admin_section_exclusions'), DeMomentSomTresCategories::PAGE);

        add_settings_field('dmst_cats_excluded_categories', __('Excluded Categories', DeMomentSomTresCategories::TEXT_DOMAIN), array(&$this, 'admin_field_excluded_categories'), DeMomentSomTresCategories::PAGE, DeMomentSomTresCategories::SECTION1);
        add_settings_field('dmst_cats_filter_categories', __('Use the_category filter?', DeMomentSomTresCategories::TEXT_DOMAIN), array(&$this, 'admin_field_filter'), DeMomentSomTresCategories::PAGE, DeMomentSomTresCategories::SECTION1);
    }

    /**
     * @since 2.0
     * @param array $input
     * @return array
     */
    function admin_validate_options($input = array()) {
        return DeMomentSomTresTools::adminHelper_esc_attr($input);
    }

    /**
     * @since 2.0
     */
    function admin_section_exclusions() {
        echo '<p>' . __('Select the categories that you want to exclude from the shortcode and the widget', DeMomentSomTresCategories::TEXT_DOMAIN) . '</p>';
    }

    /**
     * @since 2.0
     */
    function admin_field_excluded_categories() {
        $name = self::OPTION_EXCLUDED_CATS;
        $value = DeMomentSomTresTools::get_option(DeMomentSomTresCategories::OPTIONS, $name);
        DeMomentSomTresTools::adminHelper_inputArray(DeMomentSomTresCategories::OPTIONS, $name, $value, array(
            'type' => 'textarea',
            'class' => 'regular-text'
        ));
        echo "<p style='font-size:0.8em;'>"
        . __('Comma separated list of categories id.', DeMomentSomTresCategories::TEXT_DOMAIN);
    }

    /**
     * @since 2.1
     */
    function admin_field_filter() {
        $name = self::OPTION_FILTER_CATS;
        $value = DeMomentSomTresTools::get_option(DeMomentSomTresCategories::OPTIONS, $name);
        DeMomentSomTresTools::adminHelper_inputArray(DeMomentSomTresCategories::OPTIONS, $name, $value, array(
            'type' => 'checkbox'
        ));
        echo "<p style='font-size:0.8em;'>"
        . __('Comma separated list of categories id.', DeMomentSomTresCategories::TEXT_DOMAIN);
    }

    public static function getCategories($excludedCats, $args = array()) {
        $globalExclude = DeMomentSomTresTools::get_option(DeMomentSomTresCategories::OPTIONS, self::OPTION_EXCLUDED_CATS, '');
        $exclude = rtrim(ltrim($excludedCats . ',' . $globalExclude, ','), ',');
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
        return $categories;
    }

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
//        $args = array(
//            'type' => 'post',
//            'child_of' => '',
//            'parent' => 0,
//            'orderby' => 'slug',
//            'order' => 'ASC',
//            'hide_empty' => 1,
//            'hierarchical' => 1,
//            'exclude' => $exclude,
//            'include' => '',
//            'number' => '',
//            'taxonomy' => 'category',
//            'pad_counts' => false);
//        $categories = get_categories($args);
        $categories = $this->getCategories($exclude);
        $output = '';
//      $output = '<pre>' . print_r($categories, true) . '</pre>'; //debug purposes
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

    /**
     * 
     * @param type $thelist
     * @param type $separator
     * @return type
     * @since 2.1
     */
    function the_category_filter($cats, $separator = ',') {

        if (!defined('WP_ADMIN')) {
            $exclude = explode($separator,DeMomentSomTresTools::get_option(self::OPTIONS, self::OPTION_EXCLUDED_CATS));

            $newlist = array();
            foreach ($cats as $cat) {
                if (!in_array($cat->term_id, $exclude))
                    $newlist[] = $cat;
            }
            return $newlist;
        } else {
            return $cats;
        }
    }

}

/**
 * @since 2.0
 */
class DeMomentSomTresCategoriesWidget extends WP_Widget {

    /**
     * @since 2.0
     */
    function DeMomentSomTresCategoriesWidget() {
        $widget_ops = array(
            'classname' => 'DMS3-Categories',
            'description' => __('Shows a Categories List', DeMomentSomTresCategories::TEXT_DOMAIN)
        );
        $this->WP_Widget('DeMomentSomTresCategories', __('DeMomentSomTres Categories', DeMomentSomTresCategories::TEXT_DOMAIN), $widget_ops);
    }

    /**
     * @since 2.0
     */
    function form($instance) {
        $title = esc_attr($instance['title']);
        $class = esc_attr($instance['class']);
        $exclude = esc_attr($instance['exclude']);
        $count = isset($instance['count']) ? (bool) $instance['count'] : false;
        $hierarchical = isset($instance['hierarchical']) ? (bool) $instance['hierarchical'] : false;
        $dropdown = isset($instance['dropdown']) ? (bool) $instance['dropdown'] : false;
        ?>
        <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', DeMomentSomTresCategories::TEXT_DOMAIN); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>
        <p><label for="<?php echo $this->get_field_id('class'); ?>"><?php _e('List class:', DeMomentSomTresCategories::TEXT_DOMAIN); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('class'); ?>" name="<?php echo $this->get_field_name('class'); ?>" type="text" value="<?php echo $class; ?>" /></p>
        <p><label for="<?php echo $this->get_field_id('exclude'); ?>"><?php _e('Excluded categories ID (comma separated):', DeMomentSomTresCategories::TEXT_DOMAIN); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('exclude'); ?>" name="<?php echo $this->get_field_name('exclude'); ?>" type="text" value="<?php echo $exclude; ?>" /></p>

        <p><input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('dropdown'); ?>" name="<?php echo $this->get_field_name('dropdown'); ?>"<?php checked($dropdown); ?> />
            <label for="<?php echo $this->get_field_id('dropdown'); ?>"><?php _e('Display as dropdown', DeMomentSomTresCategories::TEXT_DOMAIN); ?></label><br />

            <input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('count'); ?>" name="<?php echo $this->get_field_name('count'); ?>"<?php checked($count); ?> />
            <label for="<?php echo $this->get_field_id('count'); ?>"><?php _e('Show post counts', DeMomentSomTresCategories::TEXT_DOMAIN); ?></label><br />

            <input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('hierarchical'); ?>" name="<?php echo $this->get_field_name('hierarchical'); ?>"<?php checked($hierarchical); ?> />
            <label for="<?php echo $this->get_field_id('hierarchical'); ?>"><?php _e('Show hierarchy', DeMomentSomTresCategories::TEXT_DOMAIN); ?></label></p>
        <?php
    }

    /**
     * @since 1.0
     */
    function update($new_instance, $old_instance) {
        $new_instance['title'] = strip_tags($new_instance['title']);
        return $new_instance;
    }

    /**
     * @since 1.0
     */
    function widget($args, $instance) {

        /** This filter is documented in wp-includes/default-widgets.php */
        $title = apply_filters('widget_title', empty($instance['title']) ? __('Categories', DeMomentSomTresCategories::TEXT_DOMAIN) : $instance['title'], $instance, $this->id_base);
        $class = $instance['class'];
        $exclude = $instance['exclude'];

        $globalExclude = DeMomentSomTresTools::get_option(DeMomentSomTresCategories::OPTIONS,  DeMomentSomTresCategories::OPTION_EXCLUDED_CATS, '');
        $exclude = rtrim(ltrim($exclude . ',' . $globalExclude, ','), ',');


        $c = !empty($instance['count']) ? '1' : '0';
        $h = !empty($instance['hierarchical']) ? '1' : '0';
        $d = !empty($instance['dropdown']) ? '1' : '0';

        echo $args['before_widget'];
        if ($title) {
            echo $args['before_title'] . $title . $args['after_title'];
        }

        $cat_args = array('orderby' => 'name', 'show_count' => $c, 'hierarchical' => $h, 'exclude' => $exclude);

        if ($d) {
            $cat_args['show_option_none'] = __('Select Category', DeMomentSomTresCategories::TEXT_DOMAIN);

            /**
             * Filter the arguments for the Categories widget drop-down.
             *
             * @since 2.8.0
             *
             * @see wp_dropdown_categories()
             *
             * @param array $cat_args An array of Categories widget drop-down arguments.
             */
            wp_dropdown_categories(apply_filters('widget_categories_dropdown_args', $cat_args));
            ?>

            <script type='text/javascript'>
                /* <![CDATA[ */
                var dropdown = document.getElementById("cat");
                function onCatChange() {
                    if (dropdown.options[dropdown.selectedIndex].value > 0) {
                        location.href = "<?php echo home_url(); ?>/?cat=" + dropdown.options[dropdown.selectedIndex].value;
                    }
                }
                dropdown.onchange = onCatChange;
                /* ]]> */
            </script>

            <?php
        } else {
            ?>
            <ul <?php if ($class != '') echo "class='$class' " ?>>
                <?php
                $cat_args['title_li'] = '';

                /**
                 * Filter the arguments for the Categories widget.
                 *
                 * @since 2.8.0
                 *
                 * @param array $cat_args An array of Categories widget options.
                 */
                wp_list_categories(apply_filters('widget_categories_args', $cat_args));
                ?>
            </ul>
            <?php
        }

        echo $args['after_widget'];
    }

}
