<?php

namespace App\Service;

use App\Entity\Contact;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class UserService
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function createContact(array $data): User
    {
        $user = new User();
        $user->setFirstName($data['firstName']);
        $user->setLastName($data['lastName']);
        $user->setContacts($data['phoneNumbers']);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    public function getAllContacts(string $sortField = 'firstName', string $sortOrder = 'ASC'): array
    {
        $validSortOrders = ['ASC', 'DESC'];
        $sortOrder = in_array(strtoupper($sortOrder), $validSortOrders) ? strtoupper($sortOrder) : 'ASC';

        return $this->entityManager->getRepository(User::class)->findBy([], [$sortField => $sortOrder]);
    }
}
