<?php

namespace App\Controller;

use App\Entity\DownloadedFiles;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\SerializerInterface;

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

    #[Route('/api/files', name: 'files.create', methods: ['POST'])]
    public function createDownloadedFile(
        Request $request,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer,
        UrlGeneratorInterface $urlGenerator): JsonResponse
    {
        $downloadFile = new DownloadedFiles();
        $file = $request->files->get('file');

        $downloadFile->setFile($file);
        $downloadFile->setMimeType($file->getClientMimeType());
        $downloadFile->setRealName($file->getClientOriginalName());
        $downloadFile->setName($file->getClientOriginalName());
        $downloadFile->setPublicPath("/public/media/pictures");
        $downloadFile->setUpdatedAt(new \DateTime());
        $downloadFile->setCreatedAt(new \DateTime());
        $downloadFile->setStatus("on");

        $entityManager->persist($downloadFile);
        $entityManager->flush();
        
        $jsonFiles = $serializer->serialize($downloadFile,'json');
        $location = $urlGenerator->generate('file.get', ["downloadedFiles" => $downloadFile->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        return new JsonResponse($jsonFiles, Response::HTTP_CREATED, ["Location" => $location], true);
    }

    #[Route('/api/files/{id}', name: 'files.get', methods: ['GET'])]
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
