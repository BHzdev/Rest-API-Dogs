<?php
function photo_data($post){
    $post_meta = get_post_meta($post->ID);
    $src = wp_get_attachment_image_src($post_meta["img"][0], "large")[0];
    $user = get_userdata($post->post_author);
    $total_comments = get_comments_number($post->ID);

    // Retorna os dados estruturados.
    return [
        "id" => $post->ID,
        "author" => $user->user_login,
        "title" => $post->post_title,
        "date" => $post->post_date,
        "src" => $src,
        "peso" => $post_meta["peso"][0],
        "idade" => $post_meta["idade"][0],
        "acessos" => $post_meta["acessos"][0],
        "total_comments" => $total_comments
    ];
}

function api_photo_get($request) {
  $post_id = $request["id"];
  $post = get_post($post_id);

  //Verifica se o post solicitado existe.
  if(!isset($post) || empty($post_id)){
    $response = new WP_Erro("error", "Post não encontrado.", [
      "status" => 404
  ]);
  return rest_ensure_response($response);
  }

  $photo = photo_data($post);
  // Altera o valor de acesso do post a cada requisição.
  $photo["acessos"] = (int) $photo["acessos"] + 1;
  // Atualiza o valor de acessos no banco.
  update_post_meta($post->ID, "acessos", $photo["acessos"]);

  // Busca os comentários do post
  $comments = get_comments([
    'post-id' => $post_id,
    'order' => 'ASC'
  ]);

  $response = [
    'photo' => $photo,
    'comments' => $comments
  ]; 

  return rest_ensure_response($response);
}

// Função para registrar o endpoint da API.
function register_api_photo_get() {
  register_rest_route('api', '/photo/(?P<id>[0-9]+)', [
      'methods' => WP_REST_Server::READABLE,
      'callback' => 'api_photo_get'
  ]);
}

add_action('rest_api_init', 'register_api_photo_get');

function api_photos_get($request) {
  $_total = sanitize_text_field($request["_total"]) ?: 6;
  $_page = sanitize_text_field($request["_page"]) ?: 1;
  $_user = sanitize_text_field($request["_user"]) ?: 0;

  if (!is_numeric($_user)) {
    $user = get_user_by("login", $_user);
    if (!$user) {
        $response = new WP_Error("error", "Usuário não encontrado.", [
            "status" => 404
        ]);
        return rest_ensure_response($response);
    }
    $_user = $user->ID;
  }

  // Argumentos para serem usados na busca dos posts.
  $args = [
      "post_type" => "post",
      "author" => $_user,
      "posts_per_page" => $_total,
      "paged" => $_page
  ];

  // Busca dos posts.
  $query = new WP_Query($args);
  // Posts.
  $posts = $query->posts;
  $photos = [];

  // Verifica se existe posts.
  if (isset($posts)) {
      foreach ($posts as $post) {
          $photos[] = photo_data($post);
      }
  }

  return rest_ensure_response($photos);
}

// Função para registrar o endpoint da API.
function register_api_photos_get() {
  register_rest_route('api', '/photo', [
      'methods' => WP_REST_Server::READABLE,
      'callback' => 'api_photos_get'
  ]);
}

// Faz com que a função de registro ocorra na hora em que a api inicia.
add_action('rest_api_init', 'register_api_photos_get');
?>