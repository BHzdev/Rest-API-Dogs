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
    $user = get_user_by('login', $login);
  };

  // Verifica se o usuário existe.
  if (empty($login)) {
    $reponse = new WP_Erro("error", "Usuário não existe.", [
        "status" => 401
    ]);
    return rest_ensure_response($response);
  };

  // Dados do usuario
  $user_login = $user->user_login;
  $user_email = $user->user_email;
  $key = get_password_reset_key($user);

  // Escrevendo a mensagem (url) que vai ser enviada para o usuario
  $message = "Utilize o link abaixo para resetar a sua senha : \r\n";
  $url = esc_url_raw($url . "/?key=$key&login=" . rawurlencode($user_login) . "\r\n");
  $body = $message . $url;
  
  // Enviando a mensagem via email para o usuario
  wp_mail($user_email, "Password Reset", $body);
  
  return rest_ensure_response("Email enviado.");
}

// Função para registrar o endpoint da API.
function register_api_password_lost() {
  register_rest_route('api', '/password/lost', [
      'methods' => WP_REST_Server::CREATABLE,
      'callback' => 'api_password_lost'
  ]);
}

add_action('rest_api_init', 'register_api_password_lost');

// Password Reset

function api_password_reset($request) {
  $login = $request["login"];
  $password = $request["password"];
  $key = $request["key"];

  $user = get_user_by("login", $login);

  // Verifica se o usuário existe.
  if (empty($user)) {
    $response = new WP_Erro("error", "Usuário não existe.", [
        "status" => 401
    ]);
    return rest_ensure_response($response);
  }

  // Confere se a key e o login são válidos.
  $check_key = check_password_reset_key($key, $login);

  // Se a key e o login forem invalidos retorna esse erro.
  if (is_wp_error($check_key)){
    $response = new WP_Erro("error", "Token Expirado.", [
      "status" => 401
    ]);
    return rest_ensure_response($response);
  }

  // Se a key e o login forem validos, a senha é resetada
  reset_password($user, $password);

  return rest_ensure_response("Senha alterada.");
}

// Função para registrar o endpoint da API.
function register_api_password_reset() {
  register_rest_route('api', '/password/reset', [
      'methods' => WP_REST_Server::CREATABLE,
      'callback' => 'api_password_reset'
  ]);
}

add_action('rest_api_init', 'register_api_password_reset');
?>