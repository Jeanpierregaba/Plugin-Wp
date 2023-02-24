<?php
/*
Plugin Name: Empty Cache-Wp
Description: Un plugin pour vider le cache d'un site WordPress
Version: 1.0
Author: Jean Pierre Gaba
License: GPL2
*/

// Ajouter un menu à l'interface d'administration
add_action('admin_menu', 'vider_cache_menu');

function vider_cache_menu() {
  add_options_page('Vider le cache', 'Vider le cache', 'manage_options', 'vider-cache', 'vider_cache_options');
}

// Afficher la page des options de cache
function vider_cache_options() {
  if (!current_user_can('manage_options')) {
    wp_die('Vous n\'êtes pas autorisé à accéder à cette page.');
  }
  ?>
  <div class="wrap">
    <h2>Vider le cache</h2>
    <p>Cliquez sur le bouton ci-dessous pour vider le cache du site :</p>
    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
      <input type="hidden" name="action" value="vider_cache">
      <?php wp_nonce_field('vider_cache'); ?>
      <p><input type="submit" class="button button-primary" value="Vider le cache"></p>
    </form>
  </div>
  <?php
}

// Vider le cache lorsque l'utilisateur clique sur le bouton
add_action('admin_post_vider_cache', 'vider_cache');

function vider_cache() {
  if (!current_user_can('manage_options')) {
    wp_die('Vous n\'êtes pas autorisé à accéder à cette page.');
  }
  if (!wp_verify_nonce($_POST['_wpnonce'], 'vider_cache')) {
    wp_die('Erreur de sécurité. Veuillez réessayer.');
  }
  if (function_exists('wp_cache_clear_cache')) {
    wp_cache_clear_cache();
  }
  if (function_exists('w3tc_pgcache_flush')) {
    w3tc_pgcache_flush();
  }
  wp_redirect(admin_url('options-general.php?page=vider-cache&cache_vidé=1'));
  exit;
}
