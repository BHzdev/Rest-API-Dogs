<?php
function api_comment_post($request) {
  // Busca os dados do usuáiro que está logado.
  $user = wp_get_current_user();
  $user_id = $user->ID;
  
  // Verifica se o usuário está logado.
  if ($user_id === 0) {
    $reponse = new WP_Erro("error", "Sem permissão.", [
      "status" => 401
    ]);
    return rest_ensure_response($response);
  }

  $comment = sanitize_text_field($request['comment']);
  $post_id = $request["id"]; 

  // Verifica se o comentário está vazio
  if(empty($comment)){
    $reponse = new WP_Erro("error", "Dados Incompletos.", [
      "status" => 422
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