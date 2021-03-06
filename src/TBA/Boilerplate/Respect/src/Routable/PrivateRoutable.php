<?php
namespace Acoes;

use \Respect\Rest\Routable;
use \TBA\TokenBasedAuth;
use \TBA\Header;

abstract class PrivateRoutable implements Routable {
  public function isAdmin() {
    return true; //roadmap
  }

  public function isOwner() {
    return true; //roadmap
  }

  public function checkAppAndClientToken() {
    return ( $this->checkAppToken() && $this->checkClientToken() );
  }

  public function checkAppToken() {
    $token = \TBA\Header::me()->getAppToken();
      //error_log("TOKEN API: {$token}");
    return ( $token == APP_TOKEN );
  }

  public function checkClientToken() {
    $token = \TBA\Header::me()->getClientToken();
      //error_log("TOKEN: {$token}");

    $a = new \TBA\TokenBasedAuth;
      $a->setConnection( \Charon\Connection::me()->get() );
      
    try {
      return $a->check($token);
    } catch (\Exception $e) {
      if ( $e->getCode() == 401 ) {
        $this->naoAutorizado( $e->getMessage() );
      }
    }
  }

  /**
  * Verifica se o usuário está devidamente logado,
  * se o token de cliente é válido,
  * e se o token da aplicação está correto
  */
  public function validate() {
    return true;
  }

  public function naoAutorizado($msg="Não autorizado") {
    http_response_code(401);
    return ["msg"=>$msg];
  }
}
