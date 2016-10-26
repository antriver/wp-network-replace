<?php

/**
 * Plugin Name: Network Replace
 * Version: 1.0.0
 * Plugin URI: https://github.com/antriver/wp-network-replace
 * Description: Automatically replace text in posts across the Wordpress Multisite installation.
 * Author: Anthony Kuske
 * Author URI: http://www.anthonykuske.com
 */

/**
 * Add link to the settings page to the network admin menu.
 */
function network_replace_admin_menu()
{
    add_submenu_page(
        'settings.php', // parent_slug
        'Replacement Settings', // page_title
        'Network Text Replacements', // menu_title
        'manage_options', // capability
        'network_replace_settings', // menu_slug
        'network_replace_display_options' // function
    );
}

/**
 * @return array
 */
function network_replace_get_options()
{
    // Return an array of options and their default value
    return array(
        'network_replace_replacements' => '',
    );
}

function network_replace_display_options()
{
    // Kill magic quotes if necessary
    foreach ($_POST as $key => &$value) {
        $value = stripslashes($value);
    }

    // Save changes
    if (!empty($_POST)) {
        $options = network_replace_get_options();
        foreach ($options as $key => $defaultValue) {
            if (isset($_POST[$key])) {
                update_site_option($key, $_POST[$key]);
            }
        }
    }
    ?>

    <div class="wrap">
        <h2><?php _e('Network Text Replacements'); ?></h2>

        <form method="post" action="settings.php?page=network_replace_settings">

            <?php settings_fields('network_replace_settings'); ?>

            <h3><label for="network_replace_replacements">Replacements</label></h3>
            <textarea
                style="width:100%; height:200px;"
                name="network_replace_replacements"><?php
                echo get_site_option('network_replace_replacements');
                ?></textarea>

            <p class="submit">
                <input type="submit" name="Submit" value="Save changes"/>
            </p>

        </form>
    </div>
    <?php
}

/**
 * @return object
 */
function network_replace_get_replacements()
{
    if ($options = get_site_option('network_replace_replacements')) {
        return json_decode($options);
    }

    return [];
}

/**
 * @param string $find
 * @param string $replace
 * @param string $content
 *
 * @return string
 */
function network_replace_replace($find, $replace, $content)
{
    $content = preg_replace($find, $replace, $content);

    return $content;
}

/**
 * Content filter.
 *
 * @param string $content
 *
 * @return string
 */
function network_replace_content_filter($content)
{
    $replacements = network_replace_get_replacements();
    var_dump($replacements);

    foreach ($replacements as $find => $replace) {
        $content = network_replace_replace($find, $replace, $content);
    }

    return $content;
}

// Add the menu
add_action('network_admin_menu', 'network_replace_admin_menu');

// Add content filter
if (function_exists('add_filter')) {
    add_filter('the_content', 'network_replace_content_filter');
}
