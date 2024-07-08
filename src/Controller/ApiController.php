<?php

namespace App\Controller;

use App\Message\CreateUserMessage;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

class ApiController extends AbstractController
{
    private $userService;
    private $messageBus;

    public function __construct(UserService $userService, MessageBusInterface $messageBus)
    {
        $this->userService = $userService;
        $this->messageBus = $messageBus;
    }

    #[Route("/api/users", name: 'api_get', methods:['GET'])]
    public function getAllContacts(Request $request): Response
    {
        $sortField = $request->query->get('sortField', 'firstName');
        $sortOrder = $request->query->get('sortOrder', 'ASC');

        $validSortOrders = ['ASC', 'DESC'];
        $sortOrder = in_array(strtoupper($sortOrder), $validSortOrders) ? strtoupper($sortOrder) : 'ASC';

        $contacts = $this->userService->getAllContacts($sortField, $sortOrder);

        return $this->json($contacts);
    }

    #[Route("/api/users", name: 'api_post', methods:['POST'])]
    public function createContact(Request $request): Response
    {
        $jsonData = $request->getContent();
        $data = json_decode($jsonData, true);
        $data['country'] = $this->userService->getCountryFromRequest($request);

        $this->messageBus->dispatch(new CreateUserMessage($data));

        $contact = $this->userService->createUser($data);

        return $this->json(['message' => 'User created', 'id' => $contact->getId()], Response::HTTP_CREATED);
    }
}
