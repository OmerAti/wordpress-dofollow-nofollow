<?php
/*
Plugin Name: Dofollow Nofollow Link Yöneticisi
Plugin URI: https://www.jrodix.com
Description: Belirli bağlantıları dofollow veya nofollow yapmanıza olanak tanır.
Version: 1.0
Author: Ömer ATABER - OmerAti 
Author URI: https://www.jrodix.com
*/

if (!defined('ABSPATH')) {
    exit; 
}


add_action('admin_menu', 'dofollow_nofollow_menu');

function dofollow_nofollow_menu() {
    add_options_page(
        'Dofollow Nofollow Link Yöneticisi',
        'Link Yöneticisi',
        'manage_options',
        'dofollow-nofollow-link-manager',
        'dofollow_nofollow_options_page'
    );
}

function dofollow_nofollow_options_page() {
    ?>
    <div class="wrap dofollow-nofollow-wrap">
        <div id="splash">
            <h1>Dofollow Nofollow Link Yöneticisi</h1>
            <p>Belirli bağlantıları dofollow veya nofollow yapmanıza olanak tanır.</p>
        </div>
        <form method="post" action="options.php">
            <?php
            settings_fields('dofollow_nofollow_options_group');
            do_settings_sections('dofollow-nofollow-link-manager');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}


add_action('admin_init', 'dofollow_nofollow_settings');

function dofollow_nofollow_settings() {
    register_setting('dofollow_nofollow_options_group', 'dofollow_nofollow_links');
    register_setting('dofollow_nofollow_options_group', 'dofollow_nofollow_show_ad'); 
    add_settings_section('dofollow_nofollow_main_section', 'Bağlantı Ayarları', null, 'dofollow-nofollow-link-manager');
    add_settings_field('dofollow_nofollow_links_field', 'Bağlantılar', 'dofollow_nofollow_links_field_callback', 'dofollow-nofollow-link-manager', 'dofollow_nofollow_main_section');
    // Reklam Göster Seçeneğini Alanını Ekle
    add_settings_field('dofollow_nofollow_show_ad_field', 'Reklam Göster', 'dofollow_nofollow_show_ad_field_callback', 'dofollow-nofollow-link-manager', 'dofollow_nofollow_main_section');
}


function dofollow_nofollow_show_ad_field_callback() {
    $show_ad = get_option('dofollow_nofollow_show_ad', '0'); 
    ?>
    <input type="checkbox" name="dofollow_nofollow_show_ad" value="1" <?php checked($show_ad, '1'); ?>>
    <label>Reklamı Göster(Sadece Admin Paneli)</label>
    <?php
}

function dofollow_nofollow_links_field_callback() {
    $links = get_option('dofollow_nofollow_links');
    echo '<textarea name="dofollow_nofollow_links" rows="10" cols="50" class="large-text">' . esc_textarea($links) . '</textarea>';
    echo '<p>Her bağlantıyı yeni bir satıra ekleyin, format: URL|dofollow veya URL|nofollow</p>';
}

add_filter('the_content', 'dofollow_nofollow_filter_content');

function dofollow_nofollow_filter_content($content) {
    $links = get_option('dofollow_nofollow_links');
    if ($links) {
        $link_lines = explode("\n", $links);
        foreach ($link_lines as $line) {
            list($url, $type) = explode('|', trim($line));
            $rel = ($type == 'nofollow') ? 'nofollow' : 'dofollow';
            $pattern = '/<a\s+(.*?)href=["\']' . preg_quote($url, '/') . '["\'](?![^>]*\brel=["\']nofollow["\'])(.*?)>/i';
            $replacement = '<a $1href="' . $url . '" rel="' . $rel . '"$2>';
            $content = preg_replace($pattern, $replacement, $content);
        }
    }
    return $content;
}
function dofollow_nofollow_enqueue_styles() {
    wp_enqueue_style('dofollow-nofollow-styles', plugin_dir_url(__FILE__) . 'dofollow-nofollow-styles.css');
}
add_action('admin_enqueue_scripts', 'dofollow_nofollow_enqueue_styles');

add_action('admin_footer', 'add_custom_admin_footer');
function add_custom_admin_footer() {
    $show_ad = get_option('dofollow_nofollow_show_ad', '0');
    if ($show_ad == '1') {
        echo '<div style="position: fixed; bottom: 0; right: 0; background-color: #fff; padding: 10px;">R10.Net Ücretsiz Yayınlanmıştır</div>';
    }
}
?>
