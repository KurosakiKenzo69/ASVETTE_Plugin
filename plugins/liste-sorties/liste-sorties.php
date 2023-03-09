    <?php

    /*
    Plugin Name: ASVETTE
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

//    function api_activites($nomActivite) {
//        $url = 'http://localhost/asvel/public/api/api-sorties.php?nomActivite=' . urlencode($nomActivite);
//        $response = wp_remote_get( $url );
//        if (is_wp_error($response)) {
//            return false;
//        }
//        $body = wp_remote_retrieve_body( $response );
//        $data = json_decode( $body );
//        return $data;
//    }



    function render_settings_page() {
        if (!current_user_can( 'manage_options')) {
            return;
        }
        ?>
           Faire la page de configuration
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
        add_settings_section( 'retrieve_site_content_section', 'Paramètres de ASVETTE', '', 'retrieve-site-content' );
        add_settings_field( 'retrieve_site_content_url', 'Site URL', 'render_url_field', 'retrieve-site-content', 'retrieve_site_content_section' );
    }

    function render_url_field() {
        $url = get_option( 'retrieve_site_content_url' );
        ?>
        <input type="text" name="retrieve_site_content_url" value="<?php echo esc_attr($url);?>">
        <?php
    }

    // shortcode qui permet de récupérer l'url qu'on a mis en paramètre
    function retrieve_site_content($atts) {
        // Récupération du lien
        $id_activite = isset($atts['id_activite']) ? (int) $atts['id_activite'] : 0;

        if ($id_activite === 0 || "") {
            $url = get_option('retrieve_site_content_url');
        }

        else {
            $id_activite = max(min($id_activite, 9), 1);
            $url = get_option('retrieve_site_content_url') . "?Passées=T&Activite=" . $id_activite;
        }


        $html = file_get_contents($url);
        $dom = str_get_html($html);
        $result = '';


//        $nomActivite = '';
//        if (isset($atts['nomActivite'])) {
//            $nomActivite = $atts['nomActivite'];
//        }
//
//        $activites = api_activites();

        wp_enqueue_script('datatables', 'http://localhost/asvel/public/js/datatables.js', array( 'jquery' ), '1.0', true );
        wp_enqueue_style('datatables-style', 'http://localhost/asvel/public/css/datatables.css');
        wp_enqueue_style('bootstrap', 'http://localhost/asvel/public/css/bootstrap.css');


    ?>
    <script>
        jQuery(document).ready(function ($) {
            var options = {
                "order": [[4, "<?php
                        if (true) {
                        echo "desc";
                    } else {
                        echo "asc";
                    }
                    ?>"]],
                "language": {
                    processing: "Traitement en cours...",
                    search: "Rechercher&nbsp;:",
                    lengthMenu: "Afficher _MENU_ &eacute;l&eacute;ments",
                    info: "Affichage de l'&eacute;lement _START_ &agrave; _END_ sur _TOTAL_ &eacute;l&eacute;ments",
                    infoEmpty: "Affichage de l'&eacute;lement 0 &agrave; 0 sur 0 &eacute;l&eacute;ments",
                    infoFiltered: "(filtr&eacute; de _MAX_ &eacute;l&eacute;ments au total)",
                    infoPostFix: "",
                    loadingRecords: "Chargement en cours...",
                    zeroRecords: "Aucun &eacute;l&eacute;ment &agrave; afficher",
                    emptyTable: "Aucune donnée disponible dans le tableau",
                    paginate: {
                        first: "Premier",
                        previous: "Pr&eacute;c&eacute;dent",
                        next: "Suivant",
                        last: "Dernier"
                    },
                    aria: {
                        sortAscending: ": activer pour trier la colonne par ordre croissant",
                        sortDescending: ": activer pour trier la colonne par ordre décroissant"
                    }
                }
            };
            var table = $('#table_sortie').DataTable(options);
        });
    </script>
    <?php

    //    $result .= "<ul>";
    //    foreach ($activites as $activite) {
    //        $result .= "<li>" . $activite['NomActivite'] . "</li>";
    //    }
    //    $result .= "</ul>";
        foreach ( $dom->find( 'table' ) as $table ) {
            $result .= $table->outertext;
        }
        return $result;
    }


    // précise le nom du shortcode et le nom de la fonction qu'on veut utiliser
    add_shortcode('Asvette_ListeSorties','retrieve_site_content');








