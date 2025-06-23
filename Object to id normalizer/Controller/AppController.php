<?php

namespace App\Controller;

use App\Dto\RequestDto;
use App\Dto\ResponseDto;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

class AppController extends AbstractController
{
    #[Route('/example1', methods: ['POST'])]
    public function example1(#[MapRequestPayload] RequestDto $dto): Response
    {
        dump($dto);
    }

    #[Route('/example2')]
    public function example2(EntityManagerInterface $entityManager): Response
    {
        $dto = new ResponseDto;
        $dto->user = $entityManager->find(User::class, 'foo');

        return $this->json($dto);
    }
}
