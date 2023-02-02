<?php
function api_comment_post($request) {
  // Busca os dados do usuáiro que está logado.
  $user = wp_get_current_user();
  $post_id = $request["id"];
  $user_id = (int) $user->ID;

  // Verifica se o id do autor é diferente do id do usuário logado, e se o post existe.
  if ($user_id !== $author_id || !isset($post)) {
    $reponse = new WP_Erro("error", "Sem permissão.", [
        "status" => 401
    ]);
    return rest_ensure_response($response);
  }


  return rest_ensure_response("Post deletado.");
}

// Função para registrar o endpoint da API.
function register_api_comment_post() {
  register_rest_route('api', '/comment/(?P<id>[0-9]+)', [
      'methods' => WP_REST_Server::CREATABLE,
      'callback' => 'api_comment_post'
  ]);
}

add_action('rest_api_init', 'register_api_comment_post');
?>