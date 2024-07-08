<?php

namespace App\MessageHandler;

use App\Message\CreateUserMessage;
use App\Service\UserService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CreateUserHandler
{
    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function __invoke(CreateUserMessage $message)
    {
        $data = $message->getData();
        $this->userService->createUser($data);
    }
}
