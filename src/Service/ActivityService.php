<?php

namespace App\Service;

use App\Entity\Activity;
use App\Entity\User;
use App\Repository\ActivityRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class ActivityService
{
    private EntityManagerInterface $em;
    private ActivityRepository $activityRepository;

    /**
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em, ActivityRepository $activityRepository)
    {
        $this->em = $em;
        $this->activityRepository = $activityRepository;
    }


    public function addActivity(Activity $activity, $user): void
    {
        $activity->setDate(new DateTime());
        $activity->setUser($user);
        $this->em->persist($activity);
        $this->em->flush();
    }

    public function showActivityByUser(int $id)
    {
        $activity = ['message' => 'Not found'];
        if ($this->activityRepository->findBy(['user' => $id])){
            $activity = $this->activityRepository->findBy(['user' => $id]);
            return $activity;
        }
        return $activity;
    }

    public function showActivityByDate($id)
    {
        if($this->activityRepository->getByDate($id)){
            return $this->activityRepository->getByDate($id);
        }
        return null;
    }

    public function showActivityByBurnedCalories($id)
    {
        if($this->activityRepository->getByBurnedCalories($id)){
            return $this->activityRepository->getByBurnedCalories($id);
        }
        return null;
    }

    public function showActivityByType(int $id, string $type)
    {
        if($this->activityRepository->getByType($id, $type)){
            return $this->activityRepository->getByType($id, $type);
        }
        return null;
    }
}