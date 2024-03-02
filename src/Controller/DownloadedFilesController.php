<?php

namespace App\Controller;

use App\Entity\DownloadedFiles;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\SerializerInterface;
use OpenApi\Attributes as OA;

class DownloadedFilesController extends AbstractController
{
    #[Route('/', name: 'app_downloaded_files')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/DownloadedFilesController.php',
        ]);
    }

    /**
     * Méthode permettant d'enregister une image
     */
    #[Route('/api/files', name: 'file.create', methods:["POST"])]
    #[OA\Response(
        response: 201,
        description: "Récupère l'image enregistée",
        content: new OA\MediaType(
            mediaType: "image",
            schema: new OA\Schema(
                type: "string",
                format: "binary"
            )
        ),
    )]
    #[OA\Parameter(
        name: 'file',
        description: 'Image à enregister',
        schema: new OA\Schema(type: 'file')
    )]
    #[OA\Tag(name:'File')]
    public function createDownloadedFile(
        Request $request, 
        EntityManagerInterface $entityManager,
       SerializerInterface $serializer, 
       UrlGeneratorInterface $urlGenerator 
    ): JsonResponse
    {
        $downloadFile = new DownloadedFiles();
        $file = $request->files->get('file');
        
        $downloadFile->setFile($file);
        $downloadFile->setMimeType($file->getClientMimeType());
        $downloadFile->setRealName($file->getClientOriginalName());
        $downloadFile->setName($file->getClientOriginalName());
        $downloadFile->setPublicPath("/public/medias/pictures");
        $downloadFile->setUpdatedAt(new \DateTime())
        ->setCreatedAt(new \DateTime())->setStatus("on");

        $entityManager->persist($downloadFile);
        $entityManager->flush( );

        $jsonFiles = $serializer->serialize($downloadFile, 'json');
        $location = $urlGenerator->generate('file.get', ["downloadedFiles" => $downloadFile->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
        dd($location);

        return new JsonResponse($jsonFiles, JsonResponse::HTTP_CREATED, ["Location" => $location], true);
    
    }

    /**
     * Méthode permettant de récupérer une image grâce à son ID
     */
    #[Route('/api/files/{downloadedFiles}', name: 'file.get', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: "Récupère une image avec son ID",
        content: new OA\MediaType(
            mediaType: "image",
            schema: new OA\Schema(
                type: "string",
                format: "binary"
            )
        ),
    )]
    #[OA\Tag(name:'File')]
    public function getDownloadedFile(
        DownloadedFiles $downloadedFile,
        SerializerInterface $serializer,
        UrlGeneratorInterface $urlGenerator): JsonResponse
    {
        $publicPath = $downloadedFile->getPublicPath();
        $location = $urlGenerator->generate('app_downloaded_files', [], UrlGeneratorInterface::ABSOLUTE_URL);
        $location = $location . str_replace("/public/", "", $publicPath . "/" . $downloadedFile->getRealPath());
        $jsonFiles = $serializer->serialize($downloadedFile,'json');

        return $downloadedFile ? new JsonResponse($jsonFiles, Response::HTTP_OK, ["Location" => $location], true) :
        new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }
}
