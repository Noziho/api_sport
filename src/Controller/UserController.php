<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\UserService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/user')]
class UserController extends AbstractController
{
    private SerializerInterface $serializer;
    private UserRepository $userRepository;
    private EntityManagerInterface $em;

    /**
     * @param SerializerInterface $serializer
     * @param UserRepository $userRepository
     * @param EntityManagerInterface $em
     */
    public function __construct
    (SerializerInterface $serializer,
     UserRepository $userRepository,
     EntityManagerInterface $em
    )
    {
        $this->serializer = $serializer;
        $this->userRepository = $userRepository;
        $this->em = $em;
    }


    #[Route('/', name: 'app_user', methods: ['GET'])]
    public function getAll(): JsonResponse
    {
        $userList = $this->userRepository->findAll();
        $jsonUserList = $this->serializer->serialize($userList, 'json', ['groups' => 'getUser']);

        return new JsonResponse($jsonUserList, Response::HTTP_OK, [], true);
    }

    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $user = $this->userRepository->find($id);
        if ($user) {
            $jsonUser = $this->serializer->serialize($user, 'json', ['groups' => 'getUser']);

            return new JsonResponse($jsonUser, Response::HTTP_OK, [], true);
        }
        return new JsonResponse(['message' => 'user not found'], Response::HTTP_NOT_FOUND);
    }

    #[Route('/show/{name}', name: 'app_user_show_name', methods: ['GET'])]
    public function getAllByDate(string $name, UserService $userService): JsonResponse
    {
        if ($userService->showUserByName($name)){

            return new JsonResponse($this->serializer->serialize
            ($userService->showUserByName($name), 'json', ['groups' => 'getActivity']), Response::HTTP_OK, [], true);
        }

        return new JsonResponse(['message' => 'not found'], Response::HTTP_NOT_FOUND);

    }

    #[Route('/', name: 'app_user_add', methods: ['POST'])]
    public function addUser(Request $request, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        $user = $this->serializer->deserialize($request->getContent(), User::class, 'json');

        $this->em->persist($user);
        $user->setPassword($passwordHasher->hashPassword($user, $user->getPassword()));
        $user->setRegistrationDate(new DateTime());
        $this->em->flush();

        return new JsonResponse
        ($this->serializer->serialize($user, 'json', ['groups' => 'getUser']), Response::HTTP_CREATED, [], true);
    }

    #[Route('/{id}', name: 'app_user_edit', methods: ['PUT'])]
    public function editUser(Request $request, User $currentUser): JsonResponse
    {
        $editUser = $this->serializer->deserialize
        ($request->getContent(), User::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $currentUser]);
        $this->em->persist($editUser);
        $this->em->flush();

        return new JsonResponse(['message' => 'user update'], Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'app_user_delete', methods: ['DELETE'])]
    public function deleteUser(int $id, UserRepository $repository, EntityManagerInterface $em): JsonResponse
    {
        $user = $repository->find($id);
        if ($user) {
            $em->remove($user);
            $em->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }
        return new JsonResponse(['message' => 'user not found'], Response::HTTP_NOT_FOUND);
    }
}
