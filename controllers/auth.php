<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use \Firebase\JWT\JWT;

// login
$app->post('/api/auth/login', function (Request $request, Response $response, array $args) {
  $data = $request->getParsedBody();

  if (!empty($data['email']) && !empty($data['password'])) {
    try {
      $db = $this->dbConnection;
    } catch (\PDOException $e) {
      return $response->withStatus(400);
    }

    try {
      // 1. auth user with db
      $stmt = $db->prepare('SELECT id, password FROM user WHERE email=? LIMIT 1;');
      $stmt->bindParam(1, $data['email']);
      $stmt->execute();

      $row = $stmt->fetch(\PDO::FETCH_ASSOC);
      if(!password_verify($data['password'], $row['password'])) {
        throw new \PDOException('password verifycation failed!');
      }
    } catch(\PDOException $e) {
      $db = null;
      return $response->withStatus(401);
    }

    // 2. issue token
    $token = [
      'iss' => 'http://example.ch',
      'iat' => time(),
      'exp' => (time() + 60 * 60 * 3), // token is valid for 3 hours
      'userid' => $row['id']
    ];

    $jwt = JWT::encode($token, JWT_KEY, JWT_ALGORITHM);
    $db = null;

    return $response->withStatus(200)->getBody()->write(json_encode(['jwtToken' => $jwt]));
  } else {
    return $response->withStatus(401);
  }
})->add($enableCors);

// signup
$app->post('/api/auth/signup', function (Request $request, Response $response, array $args) {
  $data = $request->getParsedBody();

  if (!empty($data['email']) && !empty($data['password'])) {
    try {
      $db = $this->dbConnection;
    } catch (\PDOException $e) {
      return $response->withStatus(400);
    }
    try {
      $stmt = $db->prepare('INSERT INTO user(`email`, password) VALUES(?, ?)');
      $stmt->bindParam(1, $data['email']);
      $stmt->bindParam(2, password_hash($data['password'], PASSWORD_DEFAULT));
      $stmt->execute();
    } catch(\PDOException $e) {
      $db = null;
      return $response->withStatus(401);
    }

    $db = null;
    return $response->withStatus(201);
  } else {
    return $response->withStatus(401);
  }
})->add($enableCors);