<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class UserService
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function createUser(array $data): User
    {
        $user = new User();
        $user->setFirstName($data['firstName'] ?? "");
        $user->setLastName($data['lastName'] ?? "");
        $user->setContacts($data['phoneNumbers'] ?? []);

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

    public function getCountryFromRequest(Request $request): ?string
    {
        $apiKey = "9b8564ee30fcee6a0990fff66292c565";
        $ip = $request->getClientIp();

        $res = file_get_contents("https://www.iplocate.io/api/lookup/{$ip}?apikey={$apiKey}");
        $res = json_decode($res, true);

        return $res['country'];
    }
}
