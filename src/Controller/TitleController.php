<?php

// src/Controller/TitleController.php
namespace App\Controller;

use App\Entity\Title;
use App\Repository\TitleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/titles')]
class TitleController extends AbstractController
{
    #[Route('/', name: 'title_index', methods: ['GET'])]
    public function index(TitleRepository $titleRepository): JsonResponse
    {
        $titles = $titleRepository->findAllTitles();

        $titleData = [];
        foreach ($titles as $title) {
            $titleData[] = $this->serializeTitle($title);
        }

        return $this->json($titleData);
    }

    #[Route('/{id}', name: 'title_show', methods: ['GET'])]
    public function show(Title $title): JsonResponse
    {
        return $this->json($this->serializeTitle($title));
    }

    #[Route('/create', name: 'title_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $title = new Title();
        $title->setName($data['name']);
        $title->setCreatedAt(new \DateTimeImmutable());
        $title->setUpdatedAt(new \DateTimeImmutable());

        $entityManager->persist($title);
        $entityManager->flush();

        return $this->json($this->serializeTitle($title), Response::HTTP_CREATED);
    }

    #[Route('/{id}/update', name: 'title_update', methods: ['PUT'])]
    public function update(Request $request, Title $title, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $title->setName($data['name']);
        $title->setUpdatedAt(new \DateTimeImmutable());

        $entityManager->flush();

        return $this->json($this->serializeTitle($title));
    }

    #[Route('/{id}', name: 'title_delete', methods: ['DELETE'])]
    public function delete(Title $title, EntityManagerInterface $entityManager): JsonResponse
    {
        $entityManager->remove($title);
        $entityManager->flush();

        return $this->json(['message' => 'Title deleted']);
    }

    private function serializeTitle(Title $title): array
    {
        return [
            'id' => $title->getId(),
            'name' => $title->getName(),
            'created_at' => $title->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $title->getUpdatedAt()->format('Y-m-d H:i:s'),
            
        ];
    }
}
