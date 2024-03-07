<?php

namespace App\Controller;

use App\Entity\Order;
use App\Repository\ComboRepository;
use App\Repository\InputRepository;
use App\Repository\OrderRepository;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use OpenApi\Attributes as OA;

class OrderController extends AbstractController
{
    #[Route('/order', name: 'app_order')]
    public function index(): Response
    {
        return $this->render('order/index.html.twig', [
            'controller_name' => 'OrderController',
        ]);
    }

    /**
     * Méthode permettant de récupérer toutes les données de la table order
     */
    #[Route('/api/order', name:'order.getAll', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: "Récupère toutes les données de la table order",
        content: new OA\JsonContent(
            ref: new Model(
                type: Order::class
            )
        ),
    )]
    #[OA\Tag(name:'Order')]
    public function getAll(OrderRepository $repository, SerializerInterface $serializer)
    {
        $orders = $repository->findAll();


        $jsonOrders = $serializer->serialize($orders, 'json', ['groups' => "getAllOrder"]);
        return new JsonResponse($jsonOrders, JsonResponse::HTTP_OK, [], true);
    }
    
    /**
     * Méthode permettant de récupérer les combo grâce à l'id du character
     */
    #[Route('/api/order/combo/{idMain}', name:'order.getCombo', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: "Récupère les combos d'un personnage",
        content: new OA\JsonContent(
            ref: 'string'
        ),
    )]
    #[OA\Tag(name:'Order')]
    public function getCombo(
        OrderRepository $repository,
        ComboRepository $comboRepository,
        InputRepository $inputRepository,
        int $idMain)
    {
        $orders = $repository->findBy(['main'=> $idMain]);
        $response = [];
        $currentCombo = null;
        foreach ($orders as $i => $order) {
            if ($order->getType() == "inputInCombo") {
                if ($currentCombo !== null) {
                    $response[] = rtrim($currentCombo, " +") . ")";
                    $currentCombo = null;
                }
                $input = $inputRepository->find($order->getSequence());
                $response[] = $input->getInput();
            } else if ($order->getType() == "comboStarter") {
                if ($currentCombo !== null) {
                    $response[] = rtrim($currentCombo, " +") . ")";
                }
                $combo = $comboRepository->find($order->getSequence());
                $currentCombo = ";" . $combo->getName() . " (";
            } else if ($order->getType() == "combo" || $order->getType() == "comboStarter") {
                if ($currentCombo !== null) {
                    $response[] = rtrim($currentCombo, " +") . ")";
                }
                $combo = $comboRepository->find($order->getSequence());
                $currentCombo = $combo->getName() . " (";
            } else if ($order->getType() == "input") {
                $input = $inputRepository->find($order->getSequence());
                if ($i > 0 && ($orders[$i - 1]->getType() == "combo" || $orders[$i - 1]->getType() == "comboStarter")) {
                    $currentCombo .= " " . $input->getInput();
                } else {
                    $currentCombo .= " + " . $input->getInput();
                }
            }
        }
        if ($currentCombo !== null) {
            $response[] = rtrim($currentCombo, " +") . ")";
        }
        
        $formattedResponse = implode(" / ", $response);
        $formattedResponse = explode(";", $formattedResponse);
        array_shift( $formattedResponse );

        return new JsonResponse($formattedResponse, JsonResponse::HTTP_OK);
    }
}