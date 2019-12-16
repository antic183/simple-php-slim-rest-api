<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

// reset forgotten password
$app->put('/api/reset-password', function (Request $request, Response $response, array $args) {
  $data = json_decode(json_encode($request->getParsedBody()), true);
  $email = $data['email'];

  try {
    $db = $this->dbConnection;
  } catch (\PDOException $e) {
    return $response->withStatus(400);
  }

  try {
    $stmt = $db->prepare('UPDATE customer SET password=:password WHERE email=:email');
    $randomizedPassword = bin2hex(random_bytes(5));
    $hashedPassword = password_hash('new-password', PASSWORD_DEFAULT);
    $stmt->bindParam(':password', $hashedPassword);
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
      mail($data['email'], 'password resetting', 'Hello,\n\nyour new password is = ' . $randomizedPassword);
    }

    if (!$stmt->rowCount()) {
      throw new \PDOException();
    }
  } catch (\PDOException $e) {
    $db = null;
    return $response->withStatus(400);
  }

  $db = null;
  return $response->withStatus(200);
})->add($enableCors);