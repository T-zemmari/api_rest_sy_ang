<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\User;
use App\Entity\Video;
use Symfony\Component\HttpFoundation\Request;
use App\Services\JwtAuth;
use Knp\Component\Pager\PaginatorInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;

class VideoController extends AbstractController
{

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

    public function index(): Response
    {
        return $this->json([
            'message' => 'Welcome to your new controller video!',
            'path' => 'src/Controller/VideoController.php',
        ]);
    }



    public function create(Request $request, JwtAuth $jwtAuth)
    {



        $token = $request->headers->get('Autorization', null);


        $auth = $jwtAuth->authToken($token);

        $data = [

            "Status" => "Error",
            "Code" => 500,
            "Message" => "Video no Guardado"
        ];


        if ($auth) {

            $json = $request->get('json', null);
            $params = json_decode($json);

            $em = $this->getDoctrine()->getManager();
            $identity = $jwtAuth->authToken($token, true);




            if (!empty($json)) {


                $user = (!empty($identity->sub)) ? $identity->sub : null;
                $title = (!empty($params->title)) ? $params->title : null;
                $url = (!empty($params->url)) ? $params->url : null;
                $descripction = (!empty($params->descripction)) ? $params->descripction : null;
                $status = (!empty($params->status)) ? $params->status : null;


                if (!empty($title) && !empty($url)) {


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

                $user_repo = $this->getDoctrine()->getRepository(User::class);
                $user = $user_repo->findOneBy([
                    "id" => $identity->sub
                ]);

                $video = new Video();

                $video->setTitle($title);
                $video->setUrl($url);
                $video->setUser($user);
                $video->setStatus($status);
                $video->setDescription($descripction);

                $createdAt = new \DateTime('now');
                $updatedAt = new \DateTime('now');

                $video->setCreatedAt($createdAt);
                $video->setUpdatedAt($updatedAt);

                $video_repo = $this->getDoctrine()->getRepository(Video::class);


                $isset_video = $video_repo->findOneBy(array(
                    'url' => $url
                ));


                if (count($isset_video) == 0) {
                    $data = [

                        "Status" => "Success",
                        "code" => "200",
                        "message" => "Video Creado Correctamente",
                        "user" => $video

                    ];
                } else {
                    $data = [

                        "Status" => "Error",
                        "code" => "500",
                        "message" => "El Video ya existe"

                    ];
                }



                $em->persist($video);
                $em->flush();
            }
        }







        return $this->resJson($data);
    }


    // Listar los videos del usuario 

    public function listarVideos(Request $request, JwtAuth $jwtAuth, PaginatorInterface $paginator, EntityManagerInterface $e)
    {

        $token = $request->headers->get('Autorization');

        $auth = $jwtAuth->authToken($token);
        $identity = $jwtAuth->authToken($token, true);


        $data = [

            "Status" => "Error",
            "Code" => 500,
            "Message" => "No se encontraron videos"
        ];


        if ($auth) {

            $identity = $jwtAuth->authToken($token, true);
            $identity = $jwtAuth->authToken($token, true);

            $em = $this->getDoctrine()->getManager();
            //$em->getRepository()





            $dql = " SELECT v FROM App\Entity\Video v WHERE v.user = {$identity->sub} ORDER BY v.id DESC";
            $query = $e->createQuery($dql);

            $page = $request->query->getInt('page', 1);

            $item_per_page = 5;

            $pagination = $paginator->paginate($query, $page, $item_per_page);
            $total = $pagination->getTotalItemCount();

            $data = [
                "Status" => "Success",
                "Code" => 200,
                "Total_items_count" => $total,
                "page_actual" => $page,
                "item_per_page" => $item_per_page,
                "total_pages" => ceil($total / $item_per_page),
                "videos" => $pagination,
                "user_id" => $identity->sub

            ];
        }




        return $this->resJson($data);
    }

    // Obtener un video mediante su id pasado como parametro desde la url


    public function videoPorId(Request $request, $id = null, JwtAuth $jwtAuth)
    {



        $token = $request->headers->get('Autorization');
        $auth = $jwtAuth->authToken($token);
        $data = [

            "Status" => "Error",
            "Code" => 500,
            "Message" => "El video No existe",
            "id" => $id
        ];


        if ($auth) {


            // Conseguir la identidad del usuario

            $identity = $jwtAuth->authToken($token, true);



            // Conseguir el objeto de la base de datos

            $video_repo = $this->getDoctrine()->getRepository(Video::class);

            $miVideo = $video_repo->findOneBy([

                "id" => $id
            ]);





            //Comprobar que el video existe y que pertenece al usuario

            if ($miVideo && is_object($miVideo) && $identity->sub == $miVideo->getUser()->getId()) {

                $data = [

                    "Status" => "Success",
                    "Code" => 200,
                    "Message" => "Video Encontrado",
                    "id" => $id,
                    "Video" => $miVideo
                ];
            } else {
                $data = [

                    "Status" => "Error",
                    "Code" => 500,
                    "Message" => "El video No existe",

                ];
            }
        }


        //Devolver la respuesta  

        return $this->resJson($data);
    }
}
