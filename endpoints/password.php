<?php
function api_password_lost($request) {
  $login = $request['login'];
  $url = $request['url'];

  // Verifica se o login está vazio.
  if (empty($login)) {
    $reponse = new WP_Erro("error", "Informe o email ou login.", [
        "status" => 406
    ]);
      return rest_ensure_response($response);
  }
  // Salva o login do usuário em uma variavel
  $user = get_user_by('email', $login);

  // Validação para que o usuario consiga entrar tanto com o email quando seu nome de usuario(login)
  if(empty($user)){
    $user = get_user_by('login', $login)
  }

  // Verifica se o usuário existe.
  if (empty($login)) {
    $reponse = new WP_Erro("error", "Usuário não existe.", [
        "status" => 401
    ]);
    return rest_ensure_response($response);
  }
  
  return rest_ensure_response($request);
}

// Função para registrar o endpoint da API.
function register_api_password_lost() {
  register_rest_route('api', '/password/lost', [
      'methods' => WP_REST_Server::CREATABLE,
      'callback' => 'api_password_lost'
  ]);
}

add_action('rest_api_init', 'register_api_password_lost');
?>