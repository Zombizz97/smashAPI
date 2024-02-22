<?php

namespace App\Controller;

use App\Entity\Character;
use App\Repository\CharacterRepository;
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

    #[Route('/api/character', name:"character.getAll", methods:["GET"])]
    public function getAllCharacters(CharacterRepository $repository, SerializerInterface $serializer){
        $characters = $repository->findAll();
        $jsonCharacters = $serializer->serialize($characters, 'json',['groups' => "getAllCharacters"]);
        return new JsonResponse($jsonCharacters, JsonResponse::HTTP_OK, [], true);
    }
        
    #[Route('/api/character/{character}', name:"character.get", methods:["GET"])]
    public function getCharacter(Character $character, SerializerInterface $serializer){
        $jsonCharacters = $serializer->serialize($character, 'json',['groups' => "getAllCharacters"]);
        return new JsonResponse($jsonCharacters, JsonResponse::HTTP_OK, [], true);
    }

    #[Route('/api/character', name:"character.create", methods:["POST"])]
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
       
        $jsonCharacters = $serializer->serialize($character, 'json',['groups' => "getAllCharacters"]);
        $location = $urlGenerator->generate("character.get", ["character" => $character->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
        return new JsonResponse($jsonCharacters, JsonResponse::HTTP_CREATED, ["Location" => $location], true);
    }

            #[Route('/api/character/{character}', name:"character.update", methods:["PUT"])]
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

    #[Route('/api/character/{character}', name:"character.delete", methods:["DELETE"])]
    public function deleteCharacter(Character $character, EntityManagerInterface $manager){
        $manager->remove($character);
        $manager->flush();

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }
}
