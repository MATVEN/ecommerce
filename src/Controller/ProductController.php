<?php

namespace App\Controller;

use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

#[Route('/api', name: 'api_product_')]
class ProductController extends AbstractController
{
    #[Route('/product', name: 'index', methods: ['GET'])]
    public function index(ManagerRegistry $doctrine): JsonResponse
    {
        $products = $doctrine->getManager()->getRepository(Product::class)->findAll();

        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];

        $serializer = new Serializer($normalizers, $encoders);

        $arrayResponse = $serializer->serialize($products, 'json');
        $arrayResponse = json_decode($arrayResponse, true);
        return new JsonResponse($arrayResponse);
    }


    #[Route('/product', name: 'store', methods: ['POST'])]
    public function store(ManagerRegistry $doctrine, Request $request, ValidatorInterface $validator): JsonResponse
    {

        $parameters = json_decode($request->getContent(), true);

        $product = new Product();
        if(isset($parameters['name'])){
            $product->setName($parameters['name']);
        }
        if(isset($parameters['price'])){
            $product->setPrice($parameters['price']);
        }

        $errors = $validator->validate($product);

        if(count($errors) > 0)
        {
            $arrayErrors = [];
            foreach($errors as $error)
            {
                $arrayErrors[] = [
                    'name' => $error->getPropertyPath(),
                    'error' => $error->getMessage()
                ];
            }

            return new JsonResponse($arrayErrors, 400);
        }

        $doctrine->getManager()->persist($product);
        $doctrine->getManager()->flush();

        $responseArray = [
            'id' => $product->getId(),
            'name' => $product->getName(),
            'price' => $product->getPrice()
        ];

        return new JsonResponse($responseArray, 201);
    }


    #[Route('/product/{product}', name: 'show', methods: ['GET'])]
    public function show(Product $product): JsonResponse
    {

        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];

        $serializer = new Serializer($normalizers, $encoders);

        $arrayResponse = $serializer->serialize($product, 'json');
        $arrayResponse = json_decode($arrayResponse, true);

        return new JsonResponse($arrayResponse);
    }


    #[Route('/product/{product}', name: 'update', methods: ['PATCH'])]
    public function update(Product $product, ManagerRegistry $doctrine, Request $request): JsonResponse
    {

        $parameters = json_decode($request->getContent(), true);

        $product->setName($parameters['name']);
        $product->setPrice($parameters['price']);

        $doctrine->getManager()->persist($product);
        $doctrine->getManager()->flush();

        return new JsonResponse('', 204);
    }



    #[Route('/product/{product}', name: 'delete', methods: ['DELETE'])]
    public function delete(ManagerRegistry $doctrine, Product $product): JsonResponse
    {

        $doctrine->getManager()->remove($product);
        $doctrine->getManager()->flush();

        return new JsonResponse('', 204);
    }
}
