<?php
function api_photo_get($request) {
  $post_id = $request["id"];
  $post = get_post($post_id);

  return rest_ensure_response($post_id);
}

// Função para registrar o endpoint da API.
function register_api_photo_get() {
  register_rest_route('api', '/photo/(?P<id>[0-9]+)', [
      'methods' => WP_REST_Server::READABLE,
      'callback' => 'api_photo_get'
  ]);
}

add_action('rest_api_init', 'register_api_photo_get');
?>