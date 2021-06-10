<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\User;
use App\Entity\Video;
use Symfony\Component\HttpFoundation\Request;

class UserController extends AbstractController
{

    // creamos un metodo que solo le vamos a dar uso en el controlodaor 

    private function resJson($data) {

        //serializar datos con servicio serializer

        $json = $this->get('serializer')->serialize($data,'json');

        //Response con httpfoudation

        $response = new Response();

        // Asignar contenido a la respuesta

        $response->setContent($json);

        //Indicar formato de la respuesta
        
        $response->headers->set('content-Type','application/json');

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

        return $this->resJson([$videos]);
    }


    // Metodo Crear 

    public function create(Request $request){

        // Recoger datos 


        //Decodificar el json


        // Respuesta por defecto

        $data = [

            "Status"=> "Success",
            "code"=>"200",
            "message"=>"User creado con exito"
        ];


        //Comprobar y validar los datos


        //Si todo ok, crear el objeto del user


        //Encryptar la contraseña


        //Comporbar si ya existe el user


        // Si no existe , guardar el user en la base de datos


        // Hacer la respuesta json

        return $this->resJson($data);




    }
}
