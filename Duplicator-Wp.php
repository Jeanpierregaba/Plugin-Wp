<?php
/*
Plugin Name: Duplicator-Wp
Description: Un plugin pour dupliquer facilement les pages et les articles sur un site WordPress
Version: 1.0
Author: Jean Pierre Gaba
License: GPL2
*/

// Ajouter un bouton "Dupliquer" sur les pages et les articles
add_action('post_row_actions', 'dupliquer_post_action', 10, 2);

function dupliquer_post_action($actions, $post) {
  if ($post->post_type == 'page' || $post->post_type == 'post') {
    $actions['dupliquer'] = '<a href="' . wp_nonce_url('admin-post.php?action=dupliquer_post&id=' . $post->ID, 'dupliquer_post_' . $post->ID) . '">Dupliquer</a>';
  }
  return $actions;
}

// Traiter la duplication lorsqu'elle est déclenchée
add_action('admin_post_dupliquer_post', 'dupliquer_post');

function dupliquer_post() {
  if (!isset($_GET['id']) || !isset($_GET['_wpnonce'])) {
    wp_die('Erreur de duplication. Veuillez réessayer.');
  }
  $post_id = intval($_GET['id']);
  if (!wp_verify_nonce($_GET['_wpnonce'], 'dupliquer_post_' . $post_id)) {
    wp_die('Erreur de sécurité. Veuillez réessayer.');
  }
  $post = get_post($post_id);
  $new_post = array(
    'post_title' => $post->post_title . ' (copie)',
    'post_content' => $post->post_content,
    'post_status' => 'draft',
    'post_type' => $post->post_type,
  );
  $new_post_id = wp_insert_post($new_post);
  if ($new_post_id) {
    $metas = get_post_meta($post_id);
    foreach ($metas as $meta_key => $meta_value) {
      foreach ($meta_value as $value) {
        add_post_meta($new_post_id, $meta_key, $value);
      }
    }
    wp_redirect(get_edit_post_link($new_post_id));
    exit;
  } else {
    wp_die('Erreur de duplication. Veuillez réessayer.');
  }
}

//
