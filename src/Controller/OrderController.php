<?php

namespace App\Controller;

use App\Repository\ComboRepository;
use App\Repository\InputRepository;
use App\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class OrderController extends AbstractController
{
    #[Route('/order', name: 'app_order')]
    public function index(): Response
    {
        return $this->render('order/index.html.twig', [
            'controller_name' => 'OrderController',
        ]);
    }

    #[Route('/api/order', name:'order.getAll', methods: ['GET'])]
    public function getAll(OrderRepository $repository, SerializerInterface $serializer)
    {
        $orders = $repository->findAll();
        $jsonOrders = $serializer->serialize($orders, 'json');
        return new JsonResponse($jsonOrders, JsonResponse::HTTP_OK, [], true);
    }

    #[Route('/api/order/combo', name:'order.getCombo', methods: ['GET'])]
    public function getCombo(OrderRepository $repository, SerializerInterface $serializer, ComboRepository $comboRepository, InputRepository $inputRepository)
    {
        $orders = $repository->findAll();
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
                $currentCombo = $combo->getName() . " (";
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
        return new JsonResponse($formattedResponse, JsonResponse::HTTP_OK);
    }
}

// if ($order->getType() == "inputInCombo") {
//     $input = $inputRepository->find($order->getSequence());
//     $response[] = $input->getInput();
// } else 