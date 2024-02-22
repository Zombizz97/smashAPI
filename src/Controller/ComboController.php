<?php

namespace App\Controller;

use App\Entity\Combo;
use App\Repository\ComboRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Validator\Constraints\Json;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ComboController extends AbstractController
{
    #[Route('/combo', name: 'app_combo')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/ComboController.php',
        ]);
    }

    #[Route('/api/combo', name:"combo.getAll", methods:["GET"])]
    public function getAllCombos(ComboRepository $repository, SerializerInterface $serializer){
        $combos = $repository->findAll();
        $jsonCombos = $serializer->serialize($combos, 'json',['groups' => "getAllCombos"]);
        return new JsonResponse($jsonCombos, JsonResponse::HTTP_OK, [], true);
    }
        
    #[Route('/api/combo/{combo}', name:"combo.get", methods:["GET"])]
    public function getCombo(Combo $combo, SerializerInterface $serializer){
        $jsonCombos = $serializer->serialize($combo, 'json',['groups' => "getAllCombos"]);
        return new JsonResponse($jsonCombos, JsonResponse::HTTP_OK, [], true);
    }

    #[Route('/api/combo', name:"combo.create", methods:["POST"])]
    public function createCombo(Request $request, ValidatorInterface $validator,UrlGeneratorInterface $urlGenerator,  SerializerInterface $serializer, EntityManagerInterface $manager){
    $date = new \DateTime();
    $combo = $serializer->deserialize($request->getContent(), Combo::class,'json');
    $combo
        ->setCreatedAt($date)
        ->setUpdatedAt($date);
       
        $errors = $validator->validate($combo);
        if($errors->count() > 0 ){
            return new JsonResponse($serializer->serialize($errors, 'json'), JsonResponse::HTTP_BAD_REQUEST, [], true);
        }
        $manager->persist($combo);
        $manager->flush();
       
        $jsonCombos = $serializer->serialize($combo, 'json',['groups' => "getAllCombos"]);
        $location = $urlGenerator->generate("combo.get", ["combo" => $combo->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
        return new JsonResponse($jsonCombos, JsonResponse::HTTP_CREATED, ["Location" => $location], true);
    }

            #[Route('/api/combo/{combo}', name:"combo.update", methods:["PUT"])]
    public function updateCombo(Combo $combo,Request $request,  SerializerInterface $serializer, EntityManagerInterface $manager){
        $date = new \DateTime();
        
        $updatedCombo = $serializer->deserialize($request->getContent(), 
            Combo::class,
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $combo]
        );
        $updatedCombo
        ->setUpdatedAt($date);
        $manager->persist($updatedCombo);
        $manager->flush();

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }

    #[Route('/api/combo/{combo}', name:"combo.delete", methods:["DELETE"])]
    public function deleteCombo(Combo $combo, EntityManagerInterface $manager){
        $manager->remove($combo);
        $manager->flush();

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }
}
