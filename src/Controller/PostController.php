<?php

// src/Controller/PostController.php
namespace App\Controller;

use App\Entity\Post;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/posts', name: 'post_')]
class PostController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(PostRepository $postRepository): JsonResponse
    {   

        $posts = $postRepository->findAll();
        
        $postData = [];
        foreach ($posts as $post) {

            $postData[] = $this->serializePost($post);
        };
        
        return $this->json($postData);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Post $post): JsonResponse
    {
        return $this->json($this->serializePost($post));
    }

    #[Route('/create', name: 'create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $post = new Post();
        $post->setTitle($data['title']);
        $post->setPostInfo($data['post_info']);

        // Assuming $user is the logged-in user
        $user = $this->getUser();
        $post->setUser($user);

        $entityManager->persist($post);
        $entityManager->flush();

        return $this->json($post, Response::HTTP_CREATED);
    }

    #[Route('/{id}/update', name: 'update', methods: ['PUT'])]
    public function update(Request $request, Post $post, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $post->setTitle($data['title']);
        $post->setPostInfo($data['post_info']);

        $entityManager->flush();

        return $this->json($post);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(Post $post, EntityManagerInterface $entityManager): JsonResponse
    {
        $entityManager->remove($post);
        $entityManager->flush();

        return $this->json(['message' => 'Post deleted']);
    }

    private function serializePost(Post $post): array
    {
        
        return [
            'id' => $post->getId(),
            'post_info' => $post->getPostInfo(),
            'created_at' => $post->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $post->getUpdatedAt()->format('Y-m-d H:i:s'),
            'title' => [
                'id' => $post->getTitle()->getId(),
                'name' => $post->getTitle()->getName(),
            ],
            'author' => [
                'id' => $post->getUser()->getId(),
                'username' => $post->getUser()->getUsername(),
                'email' => $post->getUser()->getEmail(),
            ],
        ];
    }
}
