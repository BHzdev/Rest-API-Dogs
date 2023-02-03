<?php
function api_stats_get($request) {
  $user = wp_get_current_user();
  $user_id = $user->ID;

  // Verificar se o usuário existe
  if($user_id === 0){
    $response = new WP_Erro("error", "Usuário não possui permissão.", [
      "status" => 401
    ]);
    return rest_ensure_response($response);
  }

  // Argumentos da query(busca)
  $args = [
    'post-type' => 'post',
    'author' => $user_id,
    'posts_per_page' => -1,
  ];
  // Buscando os posts com WP_Query
  $query = new WP_Query($args);
  // Pegando os posts
  $posts = $query->posts;

  $stats = [];
  if($posts){
    foreach($posts as $post){
      $stats[] = [
        'id' => $post->ID,
        'title' => $post->post_title,
        'acessos' => get_post_meta($post->ID, 'acessos', true)
      ];
    };
  };
  
  return rest_ensure_response($stats);
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