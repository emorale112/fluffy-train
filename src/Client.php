<?php

/**
 * Cocolis API Class
 * API Documentation: https://doc.cocolis.fr
 * Class Documentation: https://github.com/Cocolis-1/cocolis-php/
 *
 * @author Cocolis
 */

namespace Cocolis\Api;

use Cocolis\Api\Clients\RideClient;
use Cocolis\Api\Clients\WebhookClient;
use Cocolis\Api\Curl as CurlClient;

class Client
{
  // Local informations
  private static $_app_id;
  private static $_password;
  private static $_live = false;

  private static $_client;
  private static $_http_client;

  // Returned by the API
  private static $_auth;

  const API_SANDBOX = "https://sandbox-api.cocolis.fr/api/v1/"; //  Test environment during your implementation
  const API_PROD = "https://api.cocolis.fr/api/v1/"; // Online environment (in production, be careful what you do with this)

  const FRONTEND_PROD = "https://www.cocolis.fr/";
  const FRONTEND_SANDBOX = "https://sandbox.cocolis.fr/";

  public static function isLive()
  {
    return self::$_live;
  }

  public static function getAppId()
  {
    return self::$_app_id;
  }

  public static function getPassword()
  {
    return self::$_password;
  }

  public static function getClient(array $auth = array())
  {
    if (!static::$_client) {
      static::$_client = static::create($auth);
    }

    return static::$_client;
  }

  public function getHttpClient()
  {
    $options = array();
    if (!self::isLive()) {
      // $options['debug'] = true;
    }
    return new CurlClient($options);
  }

  public static function setAppId($app_id)
  {
    self::$_app_id = $app_id;
  }

  public static function setPassword($password)
  {
    self::$_password = $password;
  }

  public static function setLive($live)
  {
    self::$_live = $live;
  }

  public static function setClient($client)
  {
    self::$_client = $client;
  }

  public static function setAuth($auth)
  {
    self::$_auth = $auth;
  }

  public static function getCurrentAuthInfo()
  {
    return self::$_auth;
  }

  public static function setCurrentAuthInfo($token, $client, $expiry, $uid)
  {
    self::$_auth = array('access-token' => $token, 'client' => $client, 'expiry' => $expiry, 'uid' => $uid);
    return self::$_auth;
  }

  public function getRideClient()
  {
    return new RideClient($this);
  }

  public function getWebhookClient()
  {
    return new WebhookClient($this);
  }

  // Initialize the connection to the API
  public static function create(array $auth)
  {
    $client = new static();

    if (!isset($auth['app_id']) || empty($auth['app_id'])) {
      throw new \InvalidArgumentException('Key app_id is missing');
    } elseif (!isset($auth['password']) || empty($auth['password'])) {
      throw new \InvalidArgumentException('Key password is missing');
    }

    if (isset($auth['live'])) {
      self::setLive($auth['live']);
    }

    self::setAppId($auth['app_id']);
    self::setPassword($auth['password']);

    self::setClient($client);

    return $client;
  }

  public static function getBaseUrl()
  {
    return self::isLive() ? self::API_PROD : self::API_SANDBOX;
  }

  // Connect to the API
  public function signIn()
  {
    $res = $this->call('app_auth/sign_in', 'POST', ['app_id' => self::getAppId(), 'password' => self::getPassword()]);

    return self::setCurrentAuthInfo($res->headers['access-token'], $res->headers['client'], $res->headers['expiry'], $res->headers['uid']);
  }

  public function validateToken($authinfo = array())
  {
    $auth = !empty($authinfo) ? $authinfo : self::getCurrentAuthInfo();
    if (empty($authinfo) && empty($auth)) {
      throw new \InvalidArgumentException('Missing auth informations (no params)');
    } else {
      $res = $this->callAuthentificated('app_auth/validate_token', 'GET', array_merge(['token-type' => 'Bearer'], $auth));
    }
    return json_decode($res->text())->success === true;
  }

  public function call($url, $method = 'GET', $body = array())
  {
    return $this->getHttpClient()->$method(self::getBaseUrl() . $url, $body);
  }

  public function callAuthentificated($url, $method = 'GET', $body = array())
  {
    $client = $this->getHttpClient();

    if (self::getCurrentAuthInfo()) {
      foreach (self::getCurrentAuthInfo() as $key => $value) {
        $client->appendRequestHeader($key, $value);
      }
    }
    return $client->$method(self::getBaseUrl() . $url, $body);
  }
}
