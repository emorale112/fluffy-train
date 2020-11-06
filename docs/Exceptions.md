---
tags: [exceptions, errors, 200, 400, status]
---

# Exceptions

Certaines requêtes vers l'API de Cocolis peuvent mener à des erreurs parfois indétectables si elles ne sont pas traîtées correctement par votre application.

### Détection des exceptions

Lorsque les requêtes vers l'API de Cocolis renvoient des codes d'erreurs supérieurs à 400, la librairie PHP génère des **Exceptions**.

### Types d'exception

#### \Cocolis\Api\Curl\UnauthorizedException

Les exceptions **UnauthorizedException** surviennent lorsque l'authentification échoue sur l'API de Cocolis.

Voici un exemple de code :

```php
$client = Client::create(array(
  'app_id' => 'mon_appid',
  'password' => 'mon_mot_de_passe',
  'live' => false // Permet de choisir l'environnement
));

try {
  $client->signIn();
} catch (\Cocolis\Api\Curl\UnauthorizedException $e) {
  echo "Erreur d'authentification survenue";
}
```

#### \Cocolis\Api\Curl\NotFoundException

Les exceptions **NotFoundException** surviennent lorsque la route demandée est introuvable sur l'API de Cocolis.

Un exemple de code dans une situation similaire que pour l'exception **UnauthorizedException** :

``` php
$client = Client::create(array(
  'app_id' => 'mon_appid',
  'password' => 'mon_mot_de_passe',
  'live' => false // Permet de choisir l'environnement
));

try {
  $client->getWebhookClient()->get(6546545640)
} catch (\Cocolis\Api\Curl\NotFoundException $e) {
  echo "Erreur 404";
}
```

#### \Cocolis\Api\Curl\InternalErrorException

Enfin, les erreurs internes renvoyées par l'API de Cocolis au-delà du code d'erreur 500 génèrent des exceptions du type : **InternalErrorException**.

Cette erreur peut survenir si vous envoyez des mauvais paramètres par exemple :


``` php
$client = Client::create(array(
    'app_id' => 'mon_appid',
    'password' => 'mon_mot_de_passe',
    'live' => false // Permet de choisir l'environnement
  ));
$client->signIn();

try {
  $client->getRideClient()->canMatch('Paris', 31400, 'Volume de 10m3');
} catch (\Cocolis\Api\Curl\InternalErrorException $e) {
  echo "Erreur interne à l'API";
}
```

