<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\EmailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher,
        EmailService $emailService
    ): Response {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_users');
        }

        $errors = [];
        $success = false;
        $token = null;

        if ($request->isMethod('POST')) {
            $name = trim($request->request->get('name', ''));
            $email = trim($request->request->get('email', ''));
            $password = (string)$request->request->get('password', '');

            if ($name === '') {
                $errors[] = 'Name is required.';
            }
            if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Valid e-mail is required.';
            }
            if ($password === '') {
                $errors[] = 'Password must not be empty.';
            }

            if (!$errors) {
                $existingUser = $em->getRepository(User::class)->findByEmail($email);
                if ($existingUser) {
                    $errors[] = 'E-mail already registered.';
                } else {
                    $user = new User();
                    $user->setName($name);
                    $user->setEmail($email);
                    $user->setPasswordHash($passwordHasher->hashPassword($user, $password));
                    $user->setStatus('unverified');

                    $token = bin2hex(random_bytes(32));
                    $user->setConfirmationToken($token);

                    $em->persist($user);
                    $em->flush();

                    // Async email sending
                    try {
                        $emailService->sendConfirmationEmail($user);
                    } catch (\Exception $e) {
                        // Email yuborishda xato bo'lsa, lekin registratsiya muvaffaqiyatli
                        // Dev rejimida xatoni ko'rsatamiz
                        if ($_ENV['APP_ENV'] === 'dev') {
                            $errors[] = 'Email sending failed: ' . $e->getMessage();
                        }
                    }

                    $success = true;
                }
            }
        }

        return $this->render('registration/register.html.twig', [
            'errors' => $errors,
            'success' => $success,
            'confirmation_link' => $success && $token ? $this->generateUrl('app_confirm', ['token' => $token]) : null,
        ]);
    }
}

