<?php
function wp_annotation_add_review_mode_class() {
    if ( isset($_GET['review-mode']) && $_GET['review-mode'] == '1' ) {
        echo '<script type="text/javascript">
            document.documentElement.classList.add("review-mode");
        </script>';
    }
}

function wp_annotation_add_overlay() {
    // $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    // $current_url = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    ?>

    <div id="wp-annotations" class="wp-annotations">
        <div class="wp-annotations--wrapper">
            <?php  
                if ( file_exists( WP_ANNOTATION_PATH . 'views/frontend.php' ) ) {
                    include WP_ANNOTATION_PATH . 'views/frontend.php';
                }
            ?>
        </div>
    </div>
    <script>
        jQuery(window).on("load", function($) {
            var $annotationDiv = $("#wp-annotations");

            if ($annotationDiv.length) {                
                $("body").prepend($annotationDiv);
            }
        });
    </script>
    <?php
}

function wp_annotation_init() {
    $plugin_enabled = get_option('wp_annotation_enabled', '0');

    
    if ($plugin_enabled && is_user_logged_in() && is_user_allowed_for_annotations()) {
        add_action('wp_head', function(){
            $interface_color = get_option('wp_annotation_color', 'blue');
        ?>
        <style>
            :root {
                --main-wp-annotations: <?= WP_ANNOTATION_COLORS[$interface_color]['main'] ?>;
                --main-wp-annotations-rgb: <?= WP_ANNOTATION_COLORS[$interface_color]['main-rgb'] ?>;
                --sombre-wp-annotations: <?= WP_ANNOTATION_COLORS[$interface_color]['sombre'] ?>;
                --sombre-wp-annotations-rgb: <?= WP_ANNOTATION_COLORS[$interface_color]['sombre-rgb'] ?>;
                --clair-wp-annotations: <?= WP_ANNOTATION_COLORS[$interface_color]['clair'] ?>;
                --clair-wp-annotations-rgb: <?= WP_ANNOTATION_COLORS[$interface_color]['clair-rgb'] ?>;
                --alt-wp-annotations: <?= WP_ANNOTATION_COLORS[$interface_color]['alt'] ?>;
                --alt-wp-annotations-rgb: <?= WP_ANNOTATION_COLORS[$interface_color]['alt-rgb'] ?>;
            }
        </style>
        <?php
        }, 1);
        add_action('wp_head', 'wp_annotation_add_review_mode_class');
        add_action('wp_footer', 'wp_annotation_add_overlay');
    }
}
add_action('init', 'wp_annotation_init');

?>
