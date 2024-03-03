<?php

namespace App\Controller;

use App\Entity\Combo;
use App\Repository\ComboRepository;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use OpenApi\Attributes as OA;

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

    /**
     * Méthode permettant de récupérer tous les combos.
     */
    #[Route('/api/combo', name:"combo.getAll", methods:["GET"])]
    #[OA\Response(
        response: 200,
        description: "Récupère l'ensemble des combo",
        content: new OA\JsonContent(
            ref: new Model(
                type: Combo::class,
                groups: ["getAllCombo"]
            )
        ),
    )]
    #[OA\Tag(name:'Combo')]
    public function getAllCombos(ComboRepository $repository, SerializerInterface $serializer){
        $combos = $repository->findAll();
        $jsonCombos = $serializer->serialize($combos, 'json',['groups' => "getAllCombo"]);
        return new JsonResponse($jsonCombos, JsonResponse::HTTP_OK, [], true);
    }
    
    /**
     * Méthode permettant de récupérer un combo grâce à son ID
     */
    #[Route('/api/combo/{combo}', name:"combo.get", methods:["GET"])]
    #[OA\Response(
        response: 200,
        description: "Récupère un combo avec son ID",
        content: new OA\JsonContent(
            ref: new Model(
                type: Combo::class,
                groups: ["getAllCombo"]
            )
        ),
    )]
    #[OA\Tag(name:'Combo')]
    public function getCombo(Combo $combo, SerializerInterface $serializer){
        $jsonCombo = $serializer->serialize($combo, 'json',['groups' => "getAllCombo"]);
        return new JsonResponse($jsonCombo, JsonResponse::HTTP_OK, [], true);
    }

    /**
     * Méthode permettant de créer un combo
     */
    #[Route('/api/combo', name:"combo.create", methods:["POST"])]
    #[OA\Response(
        response: 201,
        description: "Récupère le combo créé",
        content: new OA\JsonContent(
            ref: new Model(
                type: Combo::class,
                groups: ["getAllCombo"]
            )
        ),
    )]
    #[OA\Parameter(
        name: 'name',
        description: 'Nom du combo',
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Tag(name:'Combo')]
    public function createCombo(
        Request $request,
        ValidatorInterface $validator,
        UrlGeneratorInterface $urlGenerator,
        SerializerInterface $serializer,
        EntityManagerInterface $manager){
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
       
        $jsonCombos = $serializer->serialize($combo, 'json',['groups' => "getAllCombo"]);
        $location = $urlGenerator->generate("combo.get", ["combo" => $combo->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
        return new JsonResponse($jsonCombos, JsonResponse::HTTP_CREATED, ["Location" => $location], true);
    }

    /**
     * Méthode permettant de mettre à jour un combo
     */
    #[Route('/api/combo/{combo}', name:"combo.update", methods:["PUT"])]
    #[OA\Response(
        response: 204,
        description: "Mets à jour le combo"
    )]
    #[OA\Tag(name:'Combo')]
    public function updateCombo(
        Combo $combo,
        Request $request, 
        SerializerInterface $serializer,
        EntityManagerInterface $manager){
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

    /**
     * Méthode permettant de supprimer un combo
     */
    #[Route('/api/combo/{combo}', name:"combo.delete", methods:["DELETE"])]
    #[OA\Response(
        response: 204,
        description: "Supprime le combo"
    )]
    #[OA\Tag(name:'Combo')]
    public function deleteCombo(Combo $combo, EntityManagerInterface $manager){
        $manager->remove($combo);
        $manager->flush();

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }
}
