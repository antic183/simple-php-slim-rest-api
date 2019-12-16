<?php
use \Firebase\JWT\JWT;

// Middleware set application/json header
$returnJsonHeader = function ($request, $response, $next) {
    $response = $response->withHeader('Content-type', 'application/json');
    $response = $next($request, $response);
    return $response;
};

// Middleware check authorization
$checkAuthorization = function ($request, $response, $next) {
  if ($request->hasHeader('Authorization')) {
    try {
      $jwtToken = substr($request->getHeader('Authorization')[0], 7); // remove "Bearer "
      $decoded = JWT::decode($jwtToken, JWT_KEY, [JWT_ALGORITHM]);
      $userid = !empty($decoded->userid)? $decoded->userid : null;

      if ($userid === null) {
        throw new \Exception();
      }

      $request = $request->withAttribute('-userid-', $userid); // with $request->getAttribute('-userid-') you can access on jwt userid

      $response = $next($request, $response);
      return $response;
    } catch(\Exception $e) {
      return $response->withStatus(401);
    }
  } else {
    return $response->withStatus(401);
  }
};



// Middleware enable cors
$enableCors = function ($request, $response, $next) {
  $response = $next($request, $response);
  return $response
  ->withHeader('Access-Control-Allow-Origin', '*')
  ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
  ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
};

$app->options('/api/.+', function ($request, $response, $args) {
  return $response;
})->add($enableCors);