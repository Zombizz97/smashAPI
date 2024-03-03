<?php

namespace App\Controller;

use App\Entity\ProPlayer;
use App\Repository\CharacterRepository;
use App\Repository\ProPlayerRepository;
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

class ProPlayerController extends AbstractController
{
    #[Route('/proPlayer', name: 'app_proPlayer')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/ProPlayerController.php',
        ]);
    }

    /**
     * Méthode permettant de récupérer tous les joueurs pro.
     */
    #[Route('/api/proPlayer', name:"proPlayer.getAll", methods:["GET"])]
    #[OA\Response(
        response: 200,
        description: "Récupère l'ensemble des joueurs pro",
        content: new OA\JsonContent(
            ref: new Model(
                type: ProPlayer::class,
                groups: ["getAllProPlayer"]
            )
        ),
    )]
    #[OA\Tag(name:'Joueur Pro')]
    public function getAllProPlayers(ProPlayerRepository $repository, SerializerInterface $serializer){
        $proPlayers = $repository->findAll();
        $jsonProPlayers = $serializer->serialize($proPlayers, 'json', ['groups' => "getAllProPlayer"]);
        return new JsonResponse($jsonProPlayers, JsonResponse::HTTP_OK, [], true);
    }
        
    /**
     * Méthode permettant de récupérer un jouer pro grâce à son ID
     */
    #[Route('/api/proPlayer/{proPlayer}', name:"proPlayer.get", methods:["GET"])]
    #[OA\Response(
        response: 200,
        description: "Récupère un joueur pro avec son ID",
        content: new OA\JsonContent(
            ref: new Model(
                type: ProPlayer::class,
                groups: ["getAllProPlayer"]
            )
        ),
    )]
    #[OA\Tag(name:'Joueur Pro')]
    public function getProPlayer(ProPlayer $proPlayer, SerializerInterface $serializer){
        $jsonProPlayers = $serializer->serialize($proPlayer, 'json', ['groups' => "getAllProPlayer"]);
        return new JsonResponse($jsonProPlayers, JsonResponse::HTTP_OK, [], true);
    }

    /**
     * Méthode permettant de créer un joueur pro
     */
    #[Route('/api/proPlayer', name:"proPlayer.create", methods:["POST"])]
    #[OA\Response(
        response: 201,
        description: "Récupère le joueur pro créé",
        content: new OA\JsonContent(
            ref: new Model(
                type: ProPlayer::class,
                groups: ["getAllProPlayer"]
            )
        ),
    )]
    #[OA\Parameter(
        name: 'name',
        description: 'Nom du joueur pro',
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Tag(name:'Joueur Pro')]
    public function createProPlayer(
        Request $request,
        ValidatorInterface $validator,
        UrlGeneratorInterface $urlGenerator,
        SerializerInterface $serializer,
        EntityManagerInterface $manager,
        CharacterRepository $repository)
    {
        $date = new \DateTime();
        $proPlayer = $serializer->deserialize($request->getContent(), ProPlayer::class,'json');
        
        $main = $request->toArray();
        if (isset($main['main'])) {
            $character = $repository->findOneBy(['id'=> $main['main'][0]]);
            $proPlayer->setMain($character);
        }

        $proPlayer
            ->setCreatedAt($date)
            ->setUpdatedAt($date);
       
        $errors = $validator->validate($proPlayer);
        if($errors->count() > 0 ){
            return new JsonResponse($serializer->serialize($errors, 'json'), JsonResponse::HTTP_BAD_REQUEST, [], true);
        }
        $manager->persist($proPlayer);
        $manager->flush();
       
        $jsonProPlayers = $serializer->serialize($proPlayer, 'json', ['groups' => "getAllProPlayer"]);
        $location = $urlGenerator->generate("proPlayer.get", ["proPlayer" => $proPlayer->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
        return new JsonResponse($jsonProPlayers, JsonResponse::HTTP_CREATED, ["Location" => $location], true);
    }

    /**
     * Méthode permettant de mettre à jour un joueur pro
     */
    #[Route('/api/proPlayer/{proPlayer}', name:"proPlayer.update", methods:["PUT"])]
    #[OA\Response(
        response: 204,
        description: "Mets à jour le joueur pro"
    )]
    #[OA\Tag(name:'Joueur Pro')]
    public function updateProPlayer(ProPlayer $proPlayer,Request $request,  SerializerInterface $serializer, EntityManagerInterface $manager, CharacterRepository $repository){
        $date = new \DateTime();
        
        $updatedProPlayer = $serializer->deserialize($request->getContent(), 
            ProPlayer::class,
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $proPlayer]
        );
        $updatedProPlayer
            ->setUpdatedAt($date);
        
        $main = $request->toArray();
        if (isset($main['main'])) {
            $character = $repository->findOneBy(['id'=> $main['main'][0]]);
            $updatedProPlayer->setMain($character);
        }
    
        $manager->persist($updatedProPlayer);
        $manager->flush();

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }

    /**
     * Méthode permettant de supprimer un joueur pro
     */
    #[Route('/api/proPlayer/{proPlayer}', name:"proPlayer.delete", methods:["DELETE"])]
    #[OA\Response(
        response: 204,
        description: "Supprime le joueur pro"
    )]
    #[OA\Tag(name:'Joueur Pro')]
    public function deleteProPlayer(ProPlayer $proPlayer, EntityManagerInterface $manager){
        $manager->remove($proPlayer);
        $manager->flush();

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }
}
