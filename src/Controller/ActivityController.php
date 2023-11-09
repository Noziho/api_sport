<?php

namespace App\Controller;

use App\Entity\Activity;
use App\Repository\ActivityRepository;
use App\Service\ActivityService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/activity')]
class ActivityController extends AbstractController
{
    private SerializerInterface $serializer;
    private ActivityRepository $activityRepository;
    private EntityManagerInterface $em;

    /**
     * @param SerializerInterface $serializer
     * @param ActivityRepository $activityRepository
     * @param EntityManagerInterface $em
     */
    public function __construct
    (SerializerInterface $serializer,
     ActivityRepository $activityRepository,
     EntityManagerInterface $em
    )
    {
        $this->serializer = $serializer;
        $this->activityRepository = $activityRepository;
        $this->em = $em;
    }


    #[Route('/', name: 'app_activity', methods: ['GET'])]
    public function getAll(): JsonResponse
    {
        $activityList = $this->activityRepository->findAll();
        $jsonActivityList = $this->serializer->serialize($activityList, 'json', ['groups' => 'getActivity']);

        return new JsonResponse($jsonActivityList, Response::HTTP_OK, [], true);
    }

    #[Route('/{id}', name: 'app_activity_show', methods: ['GET'])]
    public function showByUser(int $id, ActivityService $activityService): JsonResponse
    {
        if ($activityService->showActivityByUser($id)){
            $jsonActivity = $this->serializer->serialize
            ($activityService->showActivityByUser($id), 'json', ['groups' => 'getActivity']);
            return new JsonResponse($jsonActivity, Response::HTTP_OK, [], true);
        }
        else {
            return new JsonResponse($activityService->showActivityByUser($id), Response::HTTP_NOT_FOUND);
        }

    }

    #[Route('/show/byDate/{id}', name: 'app_activity_date', methods: ['GET'])]
    public function getAllByDate(int $id, ActivityService $activityService): JsonResponse
    {
        if ($activityService->showActivityByDate($id)){

            return new JsonResponse($this->serializer->serialize
            ($activityService->showActivityByDate($id), 'json', ['groups' => 'getActivity']), Response::HTTP_OK, [], true);
        }

        return new JsonResponse(['message' => 'not found'], Response::HTTP_NOT_FOUND);

    }

    #[Route('/show/byCalories/{id}', name: 'app_activity_calories', methods: ['GET'])]
    public function getAllByBurnedCalories(int $id, ActivityService $activityService): JsonResponse
    {
        if ($activityService->showActivityByBurnedCalories($id)){

            return new JsonResponse($this->serializer->serialize
            ($activityService->showActivityByBurnedCalories($id), 'json', ['groups' => 'getActivity']), Response::HTTP_OK, [], true);
        }

        return new JsonResponse(['message' => 'not found'], Response::HTTP_NOT_FOUND);

    }

    #[Route('/show/byType/{id}/{type}', name: 'app_activity_type', methods: ['GET'])]
    public function getAllByType(int $id, string $type, ActivityService $activityService): JsonResponse
    {
        if ($activityService->showActivityByType($id, $type)){

            return new JsonResponse($this->serializer->serialize
            ($activityService->showActivityByType($id, $type), 'json', ['groups' => 'getActivity']), Response::HTTP_OK, [], true);
        }

        return new JsonResponse(['message' => 'not found'], Response::HTTP_NOT_FOUND);

    }

    #[Route('/', name: 'app_activity_add', methods: ['POST'])]
    public function addActivity(Request $request, ActivityService $activityService): JsonResponse
    {
        $activity = $this->serializer->deserialize($request->getContent(), Activity::class, 'json');
        $activityService->addActivity($activity, $this->getUser());

        return new JsonResponse
        ($this->serializer->serialize
             ($activity, 'json', ['groups' => 'getActivity']),
            Response::HTTP_CREATED, [], true
        );
    }

    #[Route('/{id}', name: 'app_activity_edit', methods: ['PUT'])]
    public function editActivity(Request $request, Activity $currentActivity): JsonResponse
    {
        $editActivity = $this->serializer->deserialize
        ($request->getContent(), Activity::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $currentActivity]);
        $this->em->persist($editActivity);
        $this->em->flush();

        return new JsonResponse(['message' => 'activity update'], Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'app_activity_delete', methods: ['DELETE'])]
    public function deleteActivity(int $id, ActivityRepository $repository, EntityManagerInterface $em): JsonResponse
    {
        $activity = $repository->find($id);
        if ($activity) {
            $em->remove($activity);
            $em->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }
        return new JsonResponse(['message' => 'activity not found'], Response::HTTP_NOT_FOUND);
    }
}
