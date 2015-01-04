<?php 
$loader = new \Phalcon\Loader();

$loader->registerDirs(array(
  __DIR__.'/models/'
))->register();

$di = new \Phalcon\DI\FactoryDefault();
$di->set('db',function(){
  return new \Phalcon\Db\Adapter\Pdo\Mysql(array(
    'host'  => 'localhost',
    'username'=> 'userdb',
    'password'=> 'password',
    'dbname' => 'userdb'
  ));
});



$app = new \Phalcon\Mvc\Micro($di);

$app->get('/api/users',function() use($app){

  $phql = "SELECT * FROM Users ORDER BY userid";
  $users = $app->modelsManager->executeQuery($phql);
  $data = array();
  foreach($users as $user){
    $data[] = array(
      'userid'=>$user->userid,
      'username'=>$user->username
    );
  }
  echo json_encode($data);

});
$app->post('/api/users',function() use ($app){
  $user = $app->request->getPost();

  $phql = "INSERT INTO Users (username, gender, createtime) VALUES (:username:, :gender:, :createtime:)"; 
  $status = $app->modelsManager->executeQuery($phql,array(
    'username' => $user['username'],
    'gender' => $user['gender'],
    'createtime' => time()
  ));

  $response = new Phalcon\Http\Response();
  if($status->success() == true){
    $response->setStatusCode(201,'Create New User');
    $user->userid = $status->getModel()->id;

    $response->setJsonContent(array('status'=>'ok','data'=>$user));
  }else{
    $response->setStatusCode(409,'Conflict');

    $errors = array();
    foreach($status->getMessages() as $message){
      $errors[] = $message->getMessage();
    }
    $response->setJsonContent(array('status'=>'ERROR','data'=>$errors));
  }
  return $response;

});

$app->get('/api/users/search/{username}',function($username) use ($app){

  $phql = "SELECT * FROM Users WHERE username LIKE :username: ORDER BY username";
  $users = $app->modelsManager->executeQuery($phql,array(
    'username' => '%'. $username .'%'
  ));

  $data = array();
  foreach($users as $user){
    $data[] = array(
      'userid' => $user->userid,
     'username' => $user->username 
   );
  }
  echo json_encode($data);

});

$app->get('/api/users/{userid:[0-9]+}',function($userid) use ($app){
  $phql = "SELECT * FROM Users WHERE userid = :userid: ";
  $user = $app->modelsManager->executeQuery($phql,array(
    'userid' => $userid
  ))->getFirst();

  if($user == false){
    $response = array('status'=>'NOT FOUNT');
  }else{
    $response = array(
      'status'   => 'FOUNT',
      'userid'   => $user->userid,
      'username' => $user->username
    );
  }

  echo json_encode($response);

});

$app->put('/api/users/{userid:[0-9]+}',function($userid) use ($app){
  $user = json_decode($app->request->getRawBody());
  $phql = "UPDATE Users SET username = :username:, gender = :gender: WHERE userid = :userid: ";
  $status = $app->modelsManager->executeQuery($phql,array(
    'userid'  => $userid,
    'username'=> $user->username,
    'gender'  => $user->gender
  ));

  if($status->success() == true){
      $user->userid = $userid;
      $response = array('status'=>'OK','data'=>$user);
  }else{
    $this->response->setStatusCode(500,'Internal error')->setHeader();

    $errors = array();
    foreach($status->getMessages as $message){
      $errors[] = $message->getMessage();
    }

    $response = array('status'=>'ERROR','data'=>$errors);
  }
  echo json_encode($response);

});

$app->delete('/api/users/{userid:[0-9]+}',function($userid) use ($app){

  $phql = "DELETE FROM Users WHERE userid = :userid: ";
  $status = $app->modelsManager->executeQuery($phql,array(
      'userid' => $userid
    ));

  if($status->success() == true){
    $response = array('status'=>'OK');
  }else{
    $this->response->setStatusCode(500,'Internal Error')->setHeaders();

    $errors = array();
    foreach($status->getMessages() as $message){
      $errors[] = $message->getMessage();
    }
    $response = array('status'=>'Error','data'=>$errors);
  }
  echo json_encode($response);
});

$app->handle();

?>
