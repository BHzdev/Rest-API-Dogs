<?php
function api_stats_get($request) {
  $user = wp_get_current_user();
  $user_id = $user->ID;

  if($user_id === 0){
    $response = new WP_Erro("error", "Usuário não possui permissão.", [
      "status" => 401
    ]);
    return rest_ensure_response($response);
  }
  return rest_ensure_response();
}

// Função para registrar o endpoint da API.
function register_api_stats_get() {
  register_rest_route('api', '/stats', [
      'methods' => WP_REST_Server::READABLE,
      'callback' => 'api_stats_get'
  ]);
}

add_action('rest_api_init', 'register_api_stats_get');
?>