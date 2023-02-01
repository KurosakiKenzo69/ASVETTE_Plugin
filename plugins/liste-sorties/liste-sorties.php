<?php

/*
Plugin Name: Projet ASVEL
Description: Ce plugin permet de récupérer le contenu d'une page via son url
Version: 1.0
Author: kenzo
*/


// inclusion du DOM
require_once 'simple_html_dom.php';

// ajout dans les menus
add_action('admin_menu', 'add_settings_page');

// ajout dans la section Réglages
function add_settings_page() {
    add_options_page('Projet ASVEL', 'Projet ASVEL', 'manage_options', 'projet-asvel', 'render_settings_page' );
}


function render_settings_page() {
    if (!current_user_can( 'manage_options')) {
        return;
    }
    ?>
<!--        Faire la page de configuration      -->
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        <form action="options.php" method="post">
            <?php
            settings_fields('retrieve_site_content_group');
            do_settings_sections('retrieve-site-content');
            submit_button( 'Sauvegarder les réglages' );
            ?>
        </form>
    </div>
    <?php
}


add_action( 'admin_init', 'register_settings' );
function register_settings() {
    register_setting( 'retrieve_site_content_group', 'retrieve_site_content_url' );
    add_settings_section( 'retrieve_site_content_section', 'Retrieve Site Content Settings', '', 'retrieve-site-content' );
    add_settings_field( 'retrieve_site_content_url', 'Site URL', 'render_url_field', 'retrieve-site-content', 'retrieve_site_content_section' );
}

function render_url_field() {
    $url = get_option( 'retrieve_site_content_url' );
    ?>
    <input type="text" name="retrieve_site_content_url" value="<?php echo esc_attr($url);?>">
    <?php
}

// shortcode qui permet de récupérer l'url qu'on a mis en paramètre
function retrieve_site_content( $atts ) {
//    Récupération du lien
    $url = get_option('retrieve_site_content_url');
    $html = file_get_contents($url);
    $dom = str_get_html($html);
    $result = '';
//     cible les <table> et parcourt dans la liste des sorties (le tableau)
    foreach ( $dom->find( 'table' ) as $table ) {
        $result .= $table->outertext;
    }
    return $result;
}
// précise le nom du shortcode et le nom de la fonction qu'on veut utiliser
add_shortcode('retrieve_content','retrieve_site_content');




