<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;


$app->get('/api/todo/{id}', function (Request $request, Response $response, array $args) {
    if (empty($id = $args['id'])) {
      return $response->withStatus(401);
    }

    try {
      $db = $this->dbConnection;
    } catch (\PDOException $e) {
      return $response->withStatus(400);
    }

    try {
      $stmt = $db->prepare('SELECT * FROM todo WHERE id=? AND userid=? IMIT 1;');
      $userid = $request->getAttribute('-userid-'); // userid from jwt-token
      $stmt->bindParam(1, $id);
      $stmt->bindParam(2, $userid);
      $stmt->execute();

      $data = [];
      if ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
        $data = [
          'id' => $row['id'],
          'userid' => $row['userid'],
          'title' => $row['title'],
          'description' => $row['description'],
          'created_at' => $row['created_at'],
          'updated_at' => $row['updated_at']
        ];
      }
    } catch(\PDOException $e) {
      $db = null;
      return $response->withStatus(401);
    }

     $response->getBody()->write(json_encode($data));
     $db = null;
     return $response;
})->add($enableCors)->add($checkAuthorization)->add($returnJsonHeader);


$app->get('/api/todo', function (Request $request, Response $response, array $args) {
    try {
      $db = $this->dbConnection;
    } catch (\PDOException $e) {
      return $response->withStatus(400);
    }

    try {
      $stmt = $db->prepare('SELECT * FROM todo userid=?;');
      $userid = $request->getAttribute('-userid-'); // userid from jwt-token
      $stmt->bindParam(1, $userid);
      $stmt->execute();

      $data = [];
      while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
        $data[] = [
          'id' => $row['id'],
          'userid' => $row['userid'],
          'title' => $row['title'],
          'description' => $row['description'],
          'created_at' => $row['created_at'],
          'updated_at' => $row['updated_at']
        ];
      }
    } catch(\PDOException $e) {
      $db = null;
      return $response->withStatus(401);
    }

    $response->getBody()->write(json_encode($data));
    $db = null;
    return $response;
})->add($enableCors)->add($checkAuthorization)->add($returnJsonHeader);

// post request (insert todo)     $app->post('/api/todo/{id}',...
// update request (update todo)   $app->update('/api/todo/{id}',...
// delete request (delete todo)   $app->delete('/api/todo/{id}',...