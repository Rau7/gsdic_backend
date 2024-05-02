<?php

// src/Controller/RegisterUserController.php
namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


#[Route('/register', name: 'register_')]
class RegisterController extends AbstractController
{
    private UserPasswordHasherInterface $passwordEncoder;
    private EntityManagerInterface $entityManager;

    public function __construct(UserPasswordHasherInterface $passwordEncoder, EntityManagerInterface $entityManager)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->entityManager = $entityManager;
    }

    #[Route('/', name: 'register', methods: ['POST'])]
    public function register(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Validate request data, e.g., check if username and password are present
        if (!isset($data['username']) || !isset($data['password'])) {
            return $this->json(['message' => 'Username and password are required'], Response::HTTP_BAD_REQUEST);
        }

        // Check if the username is already in use
        $userRepository = $this->entityManager->getRepository(User::class);
        $existingUser = $userRepository->findOneBy(['username' => $data['username']]);
        if ($existingUser) {
            return $this->json(['message' => 'Username already exists'], Response::HTTP_CONFLICT);
        }        


        // Create a new User entity and set its properties
        $user = new User();
        $user->setUsername($data['username']);
        $user->setEmail($data['email']);
        $user->setDescription($data['description']);

        // Set the password using the UserPasswordHasherInterface

        // Encode the password before setting it
        $encodedPassword = $this->passwordEncoder->hashPassword($user, $data['password']);
        $user->setPassword($encodedPassword);

        // created at and updated at
        $user->setCreatedAt(new \DateTimeImmutable());
        $user->setUpdatedAt(new \DateTimeImmutable());

        // Save the new user to the database
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $this->json(['message' => 'User registered successfully'], Response::HTTP_CREATED);
    }

    #[Route('/register_multiple', name: 'register_multiple', methods: ['POST'])]
public function registerMultipleUsers(Request $request): JsonResponse
{
    $data = json_decode($request->getContent(), true);

    if (!is_array($data)) {
        return $this->json(['message' => 'Invalid request data'], Response::HTTP_BAD_REQUEST);
    }

    $userRepository = $this->entityManager->getRepository(User::class);
    $users = [];

    foreach ($data as $userData) {
        if (!isset($userData['username']) || !isset($userData['password'])) {
            return $this->json(['message' => 'Username and password are required for each user'], Response::HTTP_BAD_REQUEST);
        }

        if ($userRepository->findOneBy(['username' => $userData['username']])) {
            return $this->json(['message' => 'Username already exists'], Response::HTTP_CONFLICT);
        }

        $user = new User();
        $user->setUsername($userData['username']);
        $user->setEmail($userData['email'] ?? null);
        $user->setDescription($userData['description'] ?? null);
        $user->setPassword($this->passwordEncoder->hashPassword($user, $userData['password']));
        $user->setCreatedAt(new \DateTimeImmutable());
        $user->setUpdatedAt(new \DateTimeImmutable());

        $users[] = $user;
    }

    foreach ($users as $user) {
        $this->entityManager->persist($user);
    }

    $this->entityManager->flush();

    return $this->json(['message' => 'Users registered successfully'], Response::HTTP_CREATED);
}

}
