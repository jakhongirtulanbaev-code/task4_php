<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/users', name: 'app_users')]
#[IsGranted('ROLE_USER')]
class UserController extends AbstractController
{
    #[Route('', name: '')]
    public function index(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAllOrderedByLastLogin();

        return $this->render('users/index.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/actions', name: '_actions', methods: ['POST'])]
    public function actions(
        Request $request,
        UserRepository $userRepository,
        EntityManagerInterface $em
    ): Response {
        $ids = $request->request->all('ids') ?? [];
        $action = $request->request->get('action', '');

        if (!is_array($ids) || empty($ids) || !in_array($action, ['block', 'unblock', 'delete_unverified', 'delete_verified'], true)) {
            $this->addFlash('error', 'Invalid action.');
            return $this->redirectToRoute('app_users');
        }

        $ids = array_map('intval', $ids);
        $currentUser = $this->getUser();
        $currentUserId = $currentUser instanceof User ? $currentUser->getId() : null;
        $wasCurrentUserAffected = false;
        $affectedRows = 0;

        switch ($action) {
            case 'block':
                $users = $userRepository->createQueryBuilder('u')
                    ->where('u.id IN (:ids)')
                    ->setParameter('ids', $ids)
                    ->getQuery()
                    ->getResult();
                foreach ($users as $user) {
                    $user->setStatus('blocked');
                    if ($currentUserId && $user->getId() === $currentUserId) {
                        $wasCurrentUserAffected = true;
                    }
                }
                $affectedRows = count($users);
                break;

            case 'unblock':
                $users = $userRepository->createQueryBuilder('u')
                    ->where('u.id IN (:ids)')
                    ->andWhere('u.status != :unverified')
                    ->setParameter('ids', $ids)
                    ->setParameter('unverified', 'unverified')
                    ->getQuery()
                    ->getResult();
                foreach ($users as $user) {
                    $user->setStatus('active');
                }
                $affectedRows = count($users);
                break;

            case 'delete_unverified':
                $users = $userRepository->createQueryBuilder('u')
                    ->where('u.id IN (:ids)')
                    ->andWhere('u.status = :status')
                    ->setParameter('ids', $ids)
                    ->setParameter('status', 'unverified')
                    ->getQuery()
                    ->getResult();
                foreach ($users as $user) {
                    if ($currentUserId && $user->getId() === $currentUserId) {
                        $wasCurrentUserAffected = true;
                    }
                    $em->remove($user);
                }
                $affectedRows = count($users);
                break;

            case 'delete_verified':
                $users = $userRepository->createQueryBuilder('u')
                    ->where('u.id IN (:ids)')
                    ->andWhere('u.status IN (:statuses)')
                    ->setParameter('ids', $ids)
                    ->setParameter('statuses', ['active', 'blocked'])
                    ->getQuery()
                    ->getResult();
                foreach ($users as $user) {
                    if ($currentUserId && $user->getId() === $currentUserId) {
                        $wasCurrentUserAffected = true;
                    }
                    $em->remove($user);
                }
                $affectedRows = count($users);
                break;
        }

        $em->flush();

        if ($affectedRows == 0 && !$wasCurrentUserAffected) {
            $this->addFlash('warning', 'No users were affected. Make sure you selected users with the correct status.');
        }

        if ($wasCurrentUserAffected) {
            return $this->redirectToRoute('app_logout');
        }

        return $this->redirectToRoute('app_users');
    }
}

