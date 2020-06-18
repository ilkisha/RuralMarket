<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    private $passwordEncoder;
    private $mailer;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder,
                                MailerInterface $mailer)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->mailer = $mailer;
    }

    /**
     * @Route("/register", name="user_register")
     * @param Request $request
     * @return Response
     * @throws TransportExceptionInterface
     */
    public function register(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted()) {
            $nativePassword = $user->getPassword();
            $passwordHash = $this->passwordEncoder->encodePassword($user, $nativePassword);
            $user->setPassword($passwordHash);
            $user->setRoles(['ROLE_USER']);
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($user);
            $manager->flush();

            $email = (new Email())
                ->from('shalvarkohomecamera@gmail.com')
                ->to($user->getEmail())
                ->subject('Activate profile')
                ->text('Your profile is active')
                ->html('<h1>Hello ' . $user->getFullName() . '</h1></br>'
                    . 'Username: ' . $user->getEmail() . '</br>'
                    . 'Password: ' . $nativePassword
                );

            $this->mailer->send($email);

            return $this->redirectToRoute('app_login');
        }

        return $this->render('users/register.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
