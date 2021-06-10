<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\User;
use App\Entity\Video;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Encoder\JsonEncode;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Validation;
use App\Services\JwtAuth;


class UserController extends AbstractController
{

    // creamos un metodo que solo le vamos a dar uso en el controlodaor 

    private function resJson($data)
    {

        //serializar datos con servicio serializer

        $json = $this->get('serializer')->serialize($data, 'json');

        //Response con httpfoudation

        $response = new Response();

        // Asignar contenido a la respuesta

        $response->setContent($json);

        //Indicar formato de la respuesta

        $response->headers->set('content-Type', 'application/json');

        //Devolver la respuesta
        return $response;
    }

    public function index()
    {

        $user_repo = $this->getDoctrine()->getRepository(User::class);
        $video_repo = $this->getDoctrine()->getRepository(Video::class);

        $users = $user_repo->findAll();

        $videos = $video_repo->findAll();



        /* foreach($users as $user){
           echo "<h1>{$user->getName()} {$user->getLastname()}</h1>";
       };
       foreach($user->getVideos() as $video){
        echo "<p>{$video->getUrl()}</p>";

       }*/

        //die();

        return $this->resJson([$videos, $users]);
    }


    // Metodo Crear-usuarios---------------------------------------------------------//

    public function create(Request $request)
    {

        // Recoger datos 
        $json = $request->get('json', null);


        //Decodificar el json

        $params = json_decode($json);

        // Respuesta por defecto

        $data_example = [

            "Status" => "Success",
            "code" => "200",
            "message" => "User creado con exito",
            'json' => $params
        ];


        //Comprobar y validar los datos

        if ($json != null) {

            $name = (!empty($params->name)) ? $params->name : null;
            $lastname = (!empty($params->lastname)) ? $params->lastname : null;
            $email = (!empty($params->email)) ? $params->email : null;
            $password = (!empty($params->password)) ? $params->password : null;
            $role = (!empty($params->role)) ? $params->role : null;


            $validator = Validation::createValidator();
            $validate_email = $validator->validate($email, [
                new Email()
            ]);

            if (!empty($email) && count($validate_email) == 0 && !empty($password) && !empty($name) && !empty($lastname) && !empty($role)) {
                $data = [

                    "Status" => "Success",
                    "code" => "200",
                    "message" => "Validacion correcta",

                ];
            } else {
                $data = [

                    "Status" => "Error",
                    "code" => "500",
                    "message" => "Validacion Incorrecta",

                ];
            }
        }


        //Si todo ok, crear el objeto del user


        $user = new User();

        $user->setName($name);
        $user->setLastname($lastname);
        $user->setEmail($email);
        $user->setRole('ROLE_USER');
        $user->setCreatedAt(new \DateTime('now'));


        //Encryptar la contraseña

        $password_hasheado = hash('sha256', $password);
        $user->setPassword($password_hasheado);

        $data = $user;


        //Comporbar si ya existe el user

        $doctrine = $this->getDoctrine();
        $em = $doctrine->getManager();
        $user_repo = $doctrine->getRepository(User::class);

        $isset_user = $user_repo->findBy(array(
            'email' => $email
        ));



        if (count($isset_user) == 0) {
            $data = [

                "Status" => "Success",
                "code" => "200",
                "message" => "Usuario Creado Correctamente",
                "user" => $user

            ];
        } else {
            $data = [

                "Status" => "Error",
                "code" => "500",
                "message" => "El Usuario ya existe"

            ];
        }

        // Si no existe , guardar el user en la base de datos

        $em->persist($user);
        $em->flush();

        // Hacer la respuesta json


        return  $this->resJson($data);
    }



    // Metodo login----------------------------------//

    public  function login(Request $request, JwtAuth $jwtAuth)
    {
        // Recibir datos por post

        $json = $request->get('json', null);
        $params = json_decode($json);

        // El array por defecto a devolver


        $data_por_defecto = [

            "Status" => "Error",
            "Code" => 500,
            "Message" => "El usuario no se ha podido identificar"
        ];


        //Comprobar y validar los datos

        if ($json != null) {

            $email = (!empty($params->email)) ? $params->email : null;
            $password = (!empty($params->password)) ? $params->password : null;
            $getToken = (!empty($params->getToken)) ? $params->getToken : null;

            $validator = Validation::createValidator();
            $validate_email = $validator->validate($email, [
                new Email()
            ]);
        }

        if (!empty($email) && !empty($password) && count($validate_email) == 0) {

            //Cifrar el password

            $password_hashed = hash('sha256', $password);

            // Validacion

            if ($getToken) {

                $signup = $jwtAuth->signup($email, $password_hashed, $getToken);
            } else {
                $signup = $jwtAuth->signup($email, $password_hashed);
            }
            return new JsonResponse($signup);
        }
        //Respuesta HTTP

        return $this->resJson($data_por_defecto);
    }



    //--Metodo editar y actualizar los datos del usuario-------------------------//

    public function update(Request $request , JwtAuth $jwtAuth){
    
       $token = $request->headers->get('Autorization');

      $authCheck= $jwtAuth->authToken($token);

      


        $data_por_defecto = [

            "Status" => "Error",
            "Code" => 500,
            "Message" => "El usuario no se ha podido identificar",
            "token"=>$token,
            "autochek_Token"=>$authCheck
        ];


        return $this->resJson($data_por_defecto);
    
    
    }
}
