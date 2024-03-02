<?php

namespace App\Controller;

use App\Entity\Character;
use App\Repository\CharacterRepository;
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

class CharacterController extends AbstractController
{
    #[Route('/character', name: 'app_character')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/CharacterController.php',
        ]);
    }

    /**
     * Méthode permettant de récupérer tous les personnages.
     */
    #[Route('/api/character', name:"character.getAll", methods:["GET"])]
    #[OA\Response(
        response: 200,
        description: "Récupère l'ensemble des personnages",
        content: new OA\JsonContent(
            ref: new Model(
                type: Character::class,
                groups: ["getAllCharacter"]
            )
        ),
    )]
    #[OA\Tag(name:'Personnage')]
    public function getAllCharacters(CharacterRepository $repository, SerializerInterface $serializer){
        $characters = $repository->findAll();
        $jsonCharacters = $serializer->serialize($characters, 'json',['groups' => "getAllCharacter"]);
        return new JsonResponse($jsonCharacters, JsonResponse::HTTP_OK, [], true);
    }
        
    /**
     * Méthode permettant de récupérer un personnage grâce à son ID
     */
    #[Route('/api/character/{character}', name:"character.get", methods:["GET"])]
    #[OA\Response(
        response: 200,
        description: "Récupère un personnage avec son ID",
        content: new OA\JsonContent(
            ref: new Model(
                type: Character::class,
                groups: ["getAllCharacter"]
            )
        ),
    )]
    #[OA\Tag(name:'Personnage')]
    public function getCharacter(Character $character, SerializerInterface $serializer){
        $jsonCharacters = $serializer->serialize($character, 'json',['groups' => "getAllCharacter"]);
        return new JsonResponse($jsonCharacters, JsonResponse::HTTP_OK, [], true);
    }

    /**
     * Méthode permettant de créer un personnage
     */
    #[Route('/api/character', name:"character.create", methods:["POST"])]
    #[OA\Response(
        response: 201,
        description: "Récupère le personnage créé",
        content: new OA\JsonContent(
            ref: new Model(
                type: Character::class,
                groups: ["getAllCharacter"]
            )
        ),
    )]
    #[OA\Parameter(
        name: 'name',
        description: 'Nom du personnage',
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Tag(name:'Personnage')]
    public function createCharacter(Request $request, ValidatorInterface $validator,UrlGeneratorInterface $urlGenerator,  SerializerInterface $serializer, EntityManagerInterface $manager){
    $date = new \DateTime();
    $character = $serializer->deserialize($request->getContent(), Character::class,'json');
    $character
        ->setCreatedAt($date)
        ->setUpdatedAt($date);
       
        $errors = $validator->validate($character);
        if($errors->count() > 0 ){
            return new JsonResponse($serializer->serialize($errors, 'json'), JsonResponse::HTTP_BAD_REQUEST, [], true);
        }
        $manager->persist($character);
        $manager->flush();
       
        $jsonCharacters = $serializer->serialize($character, 'json',['groups' => "getAllCharacter"]);
        $location = $urlGenerator->generate("character.get", ["character" => $character->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
        return new JsonResponse($jsonCharacters, JsonResponse::HTTP_CREATED, ["Location" => $location], true);
    }

    /**
     * Méthode permettant de mettre à jour un personnage
     */
    #[Route('/api/character/{character}', name:"character.update", methods:["PUT"])]
    #[OA\Response(
        response: 204,
        description: "Mets à jour le personnage"
    )]
    #[OA\Tag(name:'Personnage')]
    public function updateCharacter(Character $character,Request $request,  SerializerInterface $serializer, EntityManagerInterface $manager){
        $date = new \DateTime();
        
        $updatedCharacter = $serializer->deserialize($request->getContent(), 
            Character::class,
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $character]
        );
        $updatedCharacter
        ->setUpdatedAt($date);
        $manager->persist($updatedCharacter);
        $manager->flush();

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }

    /**
     * Méthode permettant de supprimer un personnage
     */
    #[Route('/api/character/{character}', name:"character.delete", methods:["DELETE"])]
    #[OA\Response(
        response: 204,
        description: "Supprime le personnage"
    )]
    #[OA\Tag(name:'Personnage')]
    public function deleteCharacter(Character $character, EntityManagerInterface $manager){
        $manager->remove($character);
        $manager->flush();

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }
}
