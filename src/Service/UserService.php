<?php

namespace App\Service;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

class UserService
{
    private EntityManagerInterface $em;
    private UserRepository $userRepository;

    /**
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em, UserRepository $userRepository)
    {
        $this->em = $em;
        $this->userRepository = $userRepository;
    }

    public function showUserByName(string $name)
    {
        if($this->userRepository->getByUserName($name)){
            return $this->userRepository->getByUserName($name);
        }
        return null;
    }
}