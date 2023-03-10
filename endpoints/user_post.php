<?php
  function api_user_post($request){
    //Pegando dados via POST
    $email = sanitize_email($request['email']);
    $username = sanitize_text_field($request['username']);
    $password = $request['password'];

    //Verificando se os dados foram enviados
    if(empty($email) || empty($username) || empty($password)){
      $response = new WP_Error('error', 'Dados Incompletos', ['status' => 406]);
      return rest_ensure_response($response);
    }

    //Verificando se os dados já existem
    if(username_exists($username) || email_exists($email)){
      $response = new WP_Error('error', 'Email já cadastrado', ['status' => 403]);
      return rest_ensure_response($response);
    }

    //Criando um novo user e inserindo os dados
    $response = wp_insert_user([
      'user_login' => $username,
      'user_email' => $email,
      'user_pass' => $password,
      'role' => 'subscriber'
    ]);
    
    // Retorna os dados no formato de "response" de REST API.
    return rest_ensure_response($response);
  }

  // Função para registrar o endpoint da API.
  function register_api_user_post(){
    register_rest_route('api', '/user', [
      'methods' => WP_REST_Server::CREATABLE,
      'callback' => 'api_user_post',
    ]);
  }
  add_action('rest_api_init', 'register_api_user_post');
?>