<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;

#[Route('/api', name: 'api_user_')]
class UserController extends AbstractController
{
    #[Route('/user', name: 'index', methods: ['GET'])]
    public function index(ManagerRegistry $doctrine): JsonResponse
    {
        $users = $doctrine->getManager()->getRepository(User::class)->findAll();

        $arrayResponse = [];

        foreach ($users as $user) {
            $arrayResponse[] = [
                "id" => $user->getId(),
                "first_name" => $user->getFirstName(),
                "last_name" => $user->getLastName(),
                "email" => $user->getEmail()
            ];
        }

        return new JsonResponse($arrayResponse);
    }


    #[Route('/user', name: 'store', methods: ['POST'])]
    public function store(ManagerRegistry $doctrine, Request $request): JsonResponse
    {

        $parameters = json_decode($request->getContent(), true);

        $user = new User();
        $user->setFirstName($parameters['first_name']);
        $user->setLastName($parameters['last_name']);
        $user->setEmail($parameters['email']);

        $doctrine->getManager()->persist($user);
        $doctrine->getManager()->flush();

        $responseArray = [
            "id" => $user->getId(),
            "first_name" => $user->getFirstName(),
            "last_name" => $user->getLastName(),
            "email" => $user->getEmail()
        ];


        return new JsonResponse($responseArray, 201);
    }


    #[Route('/user/{user}', name: 'show', methods: ['GET'])]
    public function show(User $user): JsonResponse
    {

        //evita di fare questo
        // $user = $doctrine->getManager()->getRepository(User::class)->find($id);

        $responseArray = [
            "id" => $user->getId(),
            "first_name" => $user->getFirstName(),
            "last_name" => $user->getLastName(),
            "email" => $user->getEmail()
        ];

        return new JsonResponse($responseArray);
    }


    #[Route('/user/{user}', name: 'edit', methods: ['PATCH'])]
    public function edit(User $user, ManagerRegistry $doctrine, Request $request): JsonResponse
    {

        $parameters = json_decode($request->getContent(), true);

        $user->setFirstName($parameters['first_name']);
        $user->setLastName($parameters['last_name']);
        $user->setEmail($parameters['email']);

        $doctrine->getManager()->persist($user);
        $doctrine->getManager()->flush();

        return new JsonResponse('', 204);
    }



    #[Route('/user/{user}', name: 'delete', methods: ['DELETE'])]
    public function delete(ManagerRegistry $doctrine, User $user): JsonResponse
    {

        $doctrine->getManager()->remove($user);
        $doctrine->getManager()->flush();

        return new JsonResponse('', 204);
    }

    /*
     * @todo: Creare chiamata di PATCH per aggiornare solamente i campi che passo nel json body di richiesta
     * TIP: creare un aggiornamento dinamico dei campi trasformando il nome del campo json in input in
     * nome del metodo dell'entità USER
     *
     * first_name -> setFirstName(---)
     * last_name -> setLastName(---)
     *
     * trasformare snake case in came case
     * https://stackoverflow.com/questions/5451394/call-method-by-string
     * https://www.php.net/manual/en/function.method-exists.php
     *
     *
     */



}
