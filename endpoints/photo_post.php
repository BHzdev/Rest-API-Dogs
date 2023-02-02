<?php
  function api_photo_post($request){
    $user = wp_get_current_user();

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