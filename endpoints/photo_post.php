<?php
  function api_photo_post($request){
     // Busca os dados do usuario que está logado.
    $user = wp_get_current_user();
    $user_id = $user->ID;

    // Testa se o usuário passado foi inválido.
    if ($user_id === 0) {
      $response = new WP_Error("error", "Usuário não possui permissão.", [
          "status" => 401
      ]);

    return rest_ensure_response(3);
  } 

  // Função para registrar o endpoint da API.
  function register_api_photo_post(){
    register_rest_route('api', '/photo', [
      'methods' => WP_REST_Server::CREATABLE,
      'callback' => 'api_photo_post',
    ]);
  }
  add_action('rest_api_init', 'register_api_photo_post');
?>