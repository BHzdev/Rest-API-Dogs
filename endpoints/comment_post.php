<?php
function api_comment_post($request) {
  // Busca os dados do usuáiro que está logado.
  $user = wp_get_current_user();
  $user_id = $user->ID;
  
  // Verifica se o usuário está logado.
  if ($user_id === 0) {
    $response = new WP_Erro("error", "Sem permissão.", [
      "status" => 401
    ]);
    return rest_ensure_response($response);
  }

  $comment = sanitize_text_field($request['comment']);
  $post_id = $request["id"]; 

  // Verifica se o comentário está vazio
  if(empty($comment)){
    $response = new WP_Erro("error", "Dados Incompletos.", [
      "status" => 422
    ]);
    return rest_ensure_response($response);
  }

  // Pegando as informações necessárias para o comentário.
  $response = [
    "comment_author" => $user->user_login,
    "comment_content" => $comment,
    "comment_post_ID" => $post_id,
    "user_id" => $user_id
  ];

  // Inserindo o comentário no post.
  $comment_id = wp_insert_comment($response);
  // Buscando o comentário.
  $comment = get_comment($comment_id);

  return rest_ensure_response($comment);
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