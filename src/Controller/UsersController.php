<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/users', name: 'users_')]
class UsersController extends AbstractController
{
    #[Route('', name: 'list', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): JsonResponse
    {
        return $this->json($entityManager->getRepository(User::class)->findAll());
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(User $user): JsonResponse
    {
        return $this->json($user);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $now = new \DateTimeImmutable();
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->submit(array_merge($request->toArray(), ['roles' => ['ROLE_USER'], 'created_at' => $now, 'updated_at' => $now]));
        if (!$form->isValid()) {
            return $this->json(['success' => false, 'errors' => $form->getErrors(true)], 422);
        }

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->json(['success' => true, 'user' => $user]);
    }
}
