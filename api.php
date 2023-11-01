<?php
require_once __DIR__ . '/models/Login.php';
require_once __DIR__ . '/models/LoginDao.php';
require_once __DIR__ . '/models/Response.php';

switch($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        //parses the query variables into the params array
        parse_str($_SERVER['QUERY_STRING'], $params);

        if(isset($params['id'])) {
            //Get by id
            $id=trim(strip_tags($params['id']));
            $login = LoginDao::select($id);
            if($login) {
                $response = array('result'=>'OK', 'data'=>$login->toArray());
            } else {
                $response = array('result'=>'Error', 'data'=>'Login not found');
            }
        } else {
            //Get all
            $result = LoginDao::getAll();
            $resultArray = [];
            foreach ($result as $object){
                $resultArray[] = $object->toArray();
            }
            $response = array('result'=>'OK', 'data'=>$resultArray);
        }
        echo Response::result(200,$response);
        break;
    case 'POST':
        //file_get_contents reads the post content into a string
        //json_decode parses a json string into an array
        $params = json_decode(file_get_contents("php://input"),true);
        if(!isset($params)) {
            $response=array('result'=>'Error','data'=>'Empty data');
            echo Response::result(400,$response);
        } else {
            $login = new Login();
            $login->setEmail(trim(strip_tags($params['email'] ?? '')));
            $login->setPassword(trim(strip_tags($params['password'] ?? '')));

            $errors = $login->validate();
            if(sizeof($errors) === 0) {
                //Validation successful
                LoginDao::insert($login);
                $response = array(
                    'result'=>'OK',
                    'data'=>$login->toArray(),
                );
                echo Response::result(201, $response);
            } else {
                //Validation errors
                $response = array(
                    'result'=>'Error',
                    'data'=>'Validation errors',
                    'errors'=>$errors,
                );
                echo Response::result(400, $response);
            }
        }
        break;
    case 'PUT':
        parse_str($_SERVER['QUERY_STRING'], $query);
        $params = json_decode(file_get_contents("php://input"),true);
        if(!isset($params) || empty($query['id'])) {
            $response=array('result'=>'Error','data'=>'Empty data');
            echo Response::result(400,$response);
        } else {
            $login = new Login();
            $login->setId(trim(strip_tags($query['id'])));
            $login->setEmail(trim(strip_tags($params['email'] ?? '')));
            $login->setPassword(trim(strip_tags($params['password'] ?? '')));

            $errors = $login->validate();
            if(sizeof($errors) === 0) {
                //Validation successful
                if(LoginDao::update($login)){
                    $response = array(
                        'result'=>'OK',
                        'data'=>$login->toArray(),
                    );
                    echo Response::result(201, $response);
                } else {
                    $response = array(
                        'result'=>'Error',
                        'data'=>'Login not found',
                    );
                    echo Response::result(400, $response);
                }
            } else {
                //Validation errors
                $response = array(
                    'result'=>'Error',
                    'data'=>'Validation errors',
                    'errors'=>$errors,
                );
                echo Response::result(400, $response);
            }
        }
        break;
    case 'DELETE':
        parse_str($_SERVER['QUERY_STRING'], $query);
        if(!isset($query['id'])) {
            $response=array('result'=>'Error','data'=>'Empty data');
            echo Response::result(400, $response);
        } else {
            $id = trim(strip_tags($query['id']));
            $login = LoginDao::select($id);
            if(!is_null($login) && LoginDao::delete($login)){
                $response = array(
                    'result'=>'OK',
                    'data'=>$login->toArray(),
                );
                echo Response::result(200, $response);
            } else {
                $response = array(
                    'result'=>'Error',
                    'data'=>'No logins deleted',
                );
                echo Response::result(200, $response);
            }
        }
        break;
}


