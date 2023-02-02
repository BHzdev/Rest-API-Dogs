<?php
function api_comment_get($request) {

  return rest_ensure_response($comment);
}

// Função para registrar o endpoint da API.
function register_api_comment_get() {
  register_rest_route('api', '/comment/(?P<id>[0-9]+)', [
      'methods' => WP_REST_Server::READABLE,
      'callback' => 'api_comment_get'
  ]);
}

add_action('rest_api_init', 'register_api_comment_get');
?>