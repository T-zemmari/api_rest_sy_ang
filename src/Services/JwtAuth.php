<?php

namespace App\Services;

use Firebase\JWT\JWT;
use App\Entity\User;


class JwtAuth
{

    public $manager;
    public $key;

    public function __construct($manager)
    {
        $this->manager = $manager;
        $this->key = ' Hola_generando_mi_key_959595595';
    }

    public function signup($email, $password, $getToken = null)
    {

        //Comprobar si el usuario existe

        $user = $this->manager->getRepository(User::class)->findOneBy([

            'email' => $email,
            'password' => $password
        ]);


        //Si existe , genear el token

        $signup = false;

        if (is_object($user)) {
            $signup = true;
        }

        if ($signup) {

            $token = [

                'sub' => $user->getId(),
                'name' => $user->getName(),
                'lastname' => $user->getLastname(),
                'email' => $user->getEmail(),
                'iat' => time(),
                'exp' => time() + (7 * 24 * 60 * 60)
            ];
            $jwt = JWT::encode($token, $this->key, 'HS256');

            //Comprobar el flag getToken , condicion

            if ($getToken) {

                $data = $jwt;
            } else {

                $decoded = JWT::decode($jwt, $this->key, ['HS256']);
                $data = $decoded;
            }
        } else {

            $data = [
                'Status' => 'Error',
                'Message' => 'Login incorrecto'
            ];
        }

        //Devolver los datos

        return $data;
    }

    // CheckToken--------------------

    public function authToken($jwt){

          $auth = false;

          $decoded = JWT::decode($jwt,$this->key,['HS256']);

          if(isset($decoded) && is_object($decoded) && !empty($decoded) && isset($decoded->sub)){
              $auth = true;
          }else{
            $auth = false;
          }


          return $auth;

    }
}
