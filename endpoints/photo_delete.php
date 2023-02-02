<?php
// Define a função que lida com os dados da requisição.
function api_photo_delete($request) {
  // Busca os dados do usuáiro que está logado.
  $user = wp_get_current_user();
  $post_id = $request["id"];
  // Busca o post pelo id.
  $post = get_post($post_id);
  $author_id = (int) $post->post_author;
  $user_id = (int) $user->ID;

  // Verifica se o id do autor é diferente do id do usuário logado, e se o post existe.
  if ($user_id !== $author_id || !isset($post)) {
    $reponse = new WP_Erro("error", "Sem permissão.", [
        "status" => 401
    ]);
    return rest_ensure_response($response);
  }

      // Pega o id da imagem do post.
      $attachment_id = get_post_meta($post_id, "img", true);
      // Depois deleta a imagem.
      wp_delete_attachment($attachment_id, true);
      // Depois deleta o post.
      wp_delete_post($post_id, true);

  return rest_ensure_response("Post deletado.");
}

// Função para registrar o endpoint da API.
function register_api_photo_delete() {
  register_rest_route('api', '/photo/(?P<id>[0-9]+)', [
      'methods' => WP_REST_Server::DELETABLE,
      'callback' => 'api_photo_delete'
  ]);
}

add_action('rest_api_init', 'register_api_photo_delete');
?>