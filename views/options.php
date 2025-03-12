<?php
if (!defined('ABSPATH')) {
    exit;
}

// function wp_annotation_options_update_messages() {
//     add_settings_error(
//         'wp_annotation_messages',
//         'wp_annotation_message',
//         __('Paramètres mis à jour.', 'wp_annotation'),
//         'success'
//     );
// }
// add_action('admin_notices', 'wp_annotation_options_update_messages');

$users = get_users();
$allowed_users = get_option('wp_annotation_users', []);
$plugin_enabled = get_option('wp_annotation_enabled', '1');
$interface_color = get_option('wp_annotation_color', 'blue');
?>

<div class="wrap">
    <h1>Gestion des annotations</h1>
    <form id="annotation-form" method="post" action="options.php">
        <?php settings_fields('wp_annotation_options'); ?>

        <h2>Activation du plugin</h2>
        <table class="form-table">
            <tr>
                <th scope="row">Activer le plugin</th>
                <td>
                    <label class="switch">
                        <input type="checkbox" name="wp_annotation_enabled" value="1" <?= checked(1, $plugin_enabled, false) ?>>
                        <span class="slider round"></span>
                    </label>                    
                </td>
            </tr>
        </table>

        <h2>Utilisateurs autorisés</h2>
        <table class="form-table">
            <?php foreach ($users as $user) : $checked = in_array($user->ID, (array) $allowed_users) ? 'checked' : ''; ?>
                <tr>
                    <th><?= esc_html($user->display_name) ?></th>
                    <td>
                        <label class="switch">
                            <input type="checkbox" name="wp_annotation_users[]" value="<?= $user->ID ?>" <?= $checked ?>>
                            <span class="slider round"></span>
                        </label>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>

        <h2>Couleur de l'interface</h2>
        <table class="form-table">
            <tr>
                <th>Choisissez une couleur</th>
                <td class="color-choice">
                    <label  class="color-box blue">
                        <input type="radio" name="wp_annotation_color" value="blue" <?= checked('blue', $interface_color, false) ?>>
                        <span></span>
                    </label>
                    <label  class="color-box red">
                        <input type="radio" name="wp_annotation_color" value="red" <?= checked('red', $interface_color, false) ?>>
                        <span></span>
                    </label>
                    <label  class="color-box green">
                        <input type="radio" name="wp_annotation_color" value="green" <?= checked('green', $interface_color, false) ?>>
                        <span></span>
                    </label>
                    <label class="color-box orange">
                        <input type="radio" name="wp_annotation_color" value="orange" <?= checked('orange', $interface_color, false) ?>>
                        <span></span>
                    </label>
                    <label class="color-box purple">
                        <input type="radio" name="wp_annotation_color" value="purple" <?= checked('purple', $interface_color, false) ?>>
                        <span></span>
                    </label>
                </td>
            </tr>
        </table>
        
        <h2>Gestion de la base de données</h2>
        <table class="form-table">
            <tr>
                <th scope="row">Supprimer tout</th>
                <td>
                    <button type="button" id="flush-button" class="button button-secondary">Supprimer</button>
                </td>
            </tr>
        </table>

        <?php submit_button(); ?>
    </form>
</div>

<script type="text/javascript">
    jQuery(document).ready(function($) {
        $('#flush-button').on('click', function() {
            if (confirm("Êtes-vous sûr de vouloir supprimer tous les commentaires ? Cette action est irréversible.")) {
                $.post(ajaxurl, {
                    action: 'flush_reviews'
                }, function(response) {
                    if (response.success) {
                        alert(response.data);
                    } else {
                        alert('Une erreur s\'est produite.');
                    }
                });
            }
        });
    });
</script>

<style>
    .switch {
        position: relative;
        display: inline-block;
        width: 40px;
        height: 20px;
    }

    /* Hide default HTML checkbox */
    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    /* The slider */
    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        -webkit-transition: .4s;
        transition: .4s;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 12px;
        width: 12px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        -webkit-transition: .4s;
        transition: .4s;
    }

    input:checked + .slider {
        background-color: #2196F3;
    }

    input:focus + .slider {
        box-shadow: 0 0 1px #2196F3;
    }

    input:checked + .slider:before {
        -webkit-transform: translateX(20px);
        -ms-transform: translateX(20px);
        transform: translateX(20px);
    }

    /* Rounded sliders */
    .slider.round {
        border-radius: 34px;
    }

    .slider.round:before {
        border-radius: 50%;
    }

    .color-choice{
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .color-box{
        height: 20px;
        display: flex;
        align-items: center;
        cursor: pointer;
    }

    .color-box input{
        display: none;
    }

    .color-box span{
        display: inline-block;
        width: 20px;
        height: 20px;
        border-radius: 50%; 
        margin-right: 5px;
        position: relative;
    }

    .color-box span::after{
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 24px;
        height: 24px;
        border: 2px solid #2196F3;
        border-radius: 50%;
        opacity: 0;
        transition: opacity 0.4s;
    }

    .color-box input:checked + span::after{
        opacity: 1;
    }

    .color-box.blue span{
        background: #3C77A1;
    }

    .color-box.red span{
        background: #A31F1A;
    }

    .color-box.green span{
        background: #0EA31A;
    }

    .color-box.orange span{
        background: #A86608;
    }

    .color-box.purple span{
        background: #7A08A3;
    }
</style>
