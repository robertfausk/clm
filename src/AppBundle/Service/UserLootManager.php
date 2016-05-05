<?php

namespace AppBundle\Service;

use AppBundle\Repository\UserRepositoryInterface;

class UserLootManager
{
    /**
     * @var UserRepositoryInterface
     */
    protected $userRepository;

    /**
     * UserLootManager constructor.
     * @param UserRepositoryInterface $userRepositoryInterface
     */
    public function __construct(UserRepositoryInterface $userRepositoryInterface)
    {
        $this->userRepository = $userRepositoryInterface;
    }

    /**
     * @return \AppBundle\Entity\User[]
     */
    public function getAllUsers()
    {
        $users = $this->userRepository->findAll();

        return $users;
    }
}