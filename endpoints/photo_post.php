<?php
function api_photo_post($request) {
  // Busca os dados do usuario que está logado.
  $user = wp_get_current_user();
  $user_id = $user->ID;

  // Testa se o usuário passado foi inválido.
  if ($user_id === 0) {
      $response = new WP_Error("error", "Usuário não possui permissão.", [
          "status" => 401
      ]);
      return rest_ensure_response($response);
  }

  $nome = sanitize_text_field($request["nome"]);
  $peso = sanitize_text_field($request["peso"]);
  $idade = sanitize_text_field($request["idade"]);
  $files = $request->get_file_params();

  // Testa se algum dos campos foi vazio.
  if (empty($nome) || empty($peso) || empty($idade) || empty($files)) {
      $response = new WP_Error("error", "Dados incompletos", ["status" => 422]);
      return rest_ensure_response($response);
  }

    // Cria os dados para serem passados na função que cria o post.
    $response = [
      "post_author" => $user_id,
      "post_type" => "post",
      "post_status" => "publish",
      "post_title" => $nome,
      "post_content" => $nome,
      "files" => $files,
      "meta_input" => [
          "peso" => $peso,
          "idade" => $idade,
          "acessos" => 0
      ]
  ];

  // Cria e insere o post no banco de dados.
  $post_id = wp_insert_post($response);

    // Requisição media_handle_upload.
    require_once ABSPATH."wp-admin/includes/image.php";
    require_once ABSPATH."wp-admin/includes/file.php";
    require_once ABSPATH."wp-admin/includes/media.php";

    $photo_id = media_handle_upload("img", $post_id);
    update_post_meta($post_id, "img", $photo_id);

  return rest_ensure_response($response);
}

// Função para registrar o endpoint da API.
function register_api_photo_post() {
  register_rest_route('api', '/photo', [
      'methods' => WP_REST_Server::CREATABLE,
      'callback' => 'api_photo_post'
  ]);
}

add_action('rest_api_init', 'register_api_photo_post');
?>