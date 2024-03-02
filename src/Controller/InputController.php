<?php

namespace App\Controller;

use App\Entity\Input;
use App\Repository\InputRepository;
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

class InputController extends AbstractController
{
    #[Route('/input', name: 'app_input')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/InputController.php',
        ]);
    }

    /**
     * Méthode permettant de récupérer tous les inputs.
     */
    #[Route('/api/input', name:"input.getAll", methods:["GET"])]
    #[OA\Response(
        response: 200,
        description: "Récupère l'ensemble des inputs",
        content: new OA\JsonContent(
            ref: new Model(
                type: Input::class
            )
        ),
    )]
    #[OA\Tag(name:'Input')]
    public function getAllInputs(InputRepository $repository, SerializerInterface $serializer){
        $inputs = $repository->findAll();
        $jsonInputs = $serializer->serialize($inputs, 'json');
        return new JsonResponse($jsonInputs, JsonResponse::HTTP_OK, [], true);
    }
        
    /**
     * Méthode permettant de récupérer un input grâce à son ID
     */
    #[Route('/api/input/{input}', name:"input.get", methods:["GET"])]
    #[OA\Response(
        response: 200,
        description: "Récupère un input avec son ID",
        content: new OA\JsonContent(
            ref: new Model(
                type: Input::class
            )
        ),
    )]
    #[OA\Tag(name:'Input')]
    public function getInput(Input $input, SerializerInterface $serializer){
        $jsonInputs = $serializer->serialize($input, 'json');
        return new JsonResponse($jsonInputs, JsonResponse::HTTP_OK, [], true);
    }

    /**
     * Méthode permettant de créer un input
     */
    #[Route('/api/input', name:"input.create", methods:["POST"])]
    #[OA\Response(
        response: 201,
        description: "Récupère l'input créé",
        content: new OA\JsonContent(
            ref: new Model(
                type: Input::class
            )
        ),
    )]
    #[OA\Parameter(
        name: 'name',
        description: 'Nom de l\'input',
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Tag(name:'Input')]
    public function createInput(Request $request, ValidatorInterface $validator,UrlGeneratorInterface $urlGenerator,  SerializerInterface $serializer, EntityManagerInterface $manager){
    $date = new \DateTime();
    $input = $serializer->deserialize($request->getContent(), Input::class,'json');
    $input
        ->setCreatedAt($date)
        ->setUpdatedAt($date);
       
        $errors = $validator->validate($input);
        if($errors->count() > 0 ){
            return new JsonResponse($serializer->serialize($errors, 'json'), JsonResponse::HTTP_BAD_REQUEST, [], true);
        }
        $manager->persist($input);
        $manager->flush();
       
        $jsonInputs = $serializer->serialize($input, 'json');
        $location = $urlGenerator->generate("input.get", ["input" => $input->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
        return new JsonResponse($jsonInputs, JsonResponse::HTTP_CREATED, ["Location" => $location], true);
    }

    /**
     * Méthode permettant de mettre à jour un inputs
     */
    #[Route('/api/input/{input}', name:"input.update", methods:["PUT"])]
    #[OA\Response(
        response: 204,
        description: "Mets à jour l'input"
    )]
    #[OA\Tag(name:'Input')]
    public function updateInput(Input $input,Request $request,  SerializerInterface $serializer, EntityManagerInterface $manager){
        $date = new \DateTime();
        
        $updatedInput = $serializer->deserialize($request->getContent(), 
            Input::class,
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $input]
        );
        $updatedInput
        ->setUpdatedAt($date);
        $manager->persist($updatedInput);
        $manager->flush();

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }

    /**
     * Méthode permettant de supprimer un input
     */
    #[Route('/api/input/{input}', name:"input.delete", methods:["DELETE"])]
    #[OA\Response(
        response: 204,
        description: "Supprime l'input"
    )]
    #[OA\Tag(name:'Input')]
    public function deleteInput(Input $input, EntityManagerInterface $manager){
        $manager->remove($input);
        $manager->flush();

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }
}
