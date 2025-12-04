<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ConfirmController extends AbstractController
{
    #[Route('/confirm/{token}', name: 'app_confirm')]
    public function confirm(string $token, UserRepository $userRepository, EntityManagerInterface $em): Response
    {
        $user = $userRepository->findByConfirmationToken($token);

        if (!$user) {
            $this->addFlash('error', 'Invalid confirmation token.');
            return $this->redirectToRoute('app_login');
        }

        // If user was blocked, status remains blocked
        if ($user->getStatus() !== 'blocked') {
            $user->setStatus('active');
        }
        $user->setConfirmationToken(null);
        $em->flush();

        $this->addFlash('success', 'Account confirmed successfully. You can now log in.');
        return $this->redirectToRoute('app_login');
    }
}

