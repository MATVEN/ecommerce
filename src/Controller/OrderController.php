<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api', name: 'api_order_')]
class OrderController extends AbstractController
{
    #[Route('/order', name: 'index', methods: ['GET'])]
    public function index(ManagerRegistry $doctrine): JsonResponse
    {
        $orders = $doctrine->getManager()->getRepository(Order::class)->findAll();

        $arrayResponse = [];

        foreach ($orders as $order) {

            $products = $order->getProducts();
            $jsonProducts = [];

            foreach ($products as $product) {
                $jsonProducts[] = [
                    "id" => $product->getId(),
                    "name" => $product->getName(),
                    "price" => $product->getPrice()
                ];
            }

            $arrayResponse[] = [
                "id" => $order->getId(),
                "date" => $order->getDate(),
                "payment_method" => $order->getPaymentMethod(),
                "status" => $order->getPaymentStatus(),
                "products" => $jsonProducts
            ];
        }

        return new JsonResponse($arrayResponse);
    }


    #[Route('/order', name: 'store', methods: ['POST'])]
    public function store(ManagerRegistry $doctrine, Request $request, ValidatorInterface $validator): JsonResponse
    {

        $parameters = json_decode($request->getContent(), true);

        $order = new Order();
        if(isset($parameters['date']))
        {
            $order->setDate(new \DateTime($parameters['date']));    
        }
        if(isset($parameters['payment_method']))
        {
            $order->setPaymentMethod($parameters['payment_method']);
        }
        if(isset($parameters['status']))
        {
            $order->setPaymentStatus($parameters['status']);
        }

        $errors = $validator->validate($order);

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

        $productIds = $parameters['product_ids'];
        foreach ($productIds as $productId) {
            $product = $doctrine->getRepository(Product::class)->find($productId);
            $order->addProduct($product);
        }
        /* FOREACH https://symfonycasts.com/screencast/collections/insert-many-to-many */

        $doctrine->getManager()->persist($order);
        $doctrine->getManager()->flush();

        /*
    @TODO: scorerre array associativo di id_prodotti, prendere singolo prodotto con doctrine, 
           chiamare addProduct dell'ordine passando come parametro il prodotto preso.
*/
        $products = $order->getProducts();
        $jsonProducts = [];

        foreach ($products as $product) {
            $jsonProducts[] = [
                "id" => $product->getId(),
                "name" => $product->getName(),
                "price" => $product->getPrice()
            ];
        }

        $responseArray = [
            "id" => $order->getId(),
            "date" => $order->getDate(),
            "payment_method" => $order->getPaymentMethod(),
            "status" => $order->getPaymentStatus(),
            "products" => $jsonProducts
        ];


        return new JsonResponse($responseArray, 201);
    }


    #[Route('/order/{order}', name: 'show', methods: ['GET'])]
    public function show(Order $order): JsonResponse
    {

        $products = $order->getProducts();
        $jsonProducts = [];

        foreach ($products as $product) {
            $jsonProducts[] = [
                "id" => $product->getId(),
                "name" => $product->getName(),
                "price" => $product->getPrice()
            ];
        }

        $responseArray = [
            "id" => $order->getId(),
            "date" => $order->getDate(),
            "payment_method" => $order->getPaymentMethod(),
            "status" => $order->getPaymentStatus(),
            "products" => $jsonProducts
        ];

        return new JsonResponse($responseArray);
    }


    #[Route('/order/{order}', name: 'update', methods: ['PATCH'])]
    public function update(Order $order, ManagerRegistry $doctrine, Request $request): JsonResponse
    {

        $parameters = json_decode($request->getContent(), true);

        $order->setPaymentStatus($parameters['status']);

        $doctrine->getManager()->persist($order);
        $doctrine->getManager()->flush();

        return new JsonResponse('', 204);
    }

    #[Route('/order/{order}', name: 'delete', methods: ['DELETE'])]
    public function delete(ManagerRegistry $doctrine, Order $order): JsonResponse
    {

        $doctrine->getManager()->remove($order);
        $doctrine->getManager()->flush();

        return new JsonResponse('', 204);
    }
}
