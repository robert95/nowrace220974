<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Company;
use AppBundle\Entity\Runner;
use AppBundle\Entity\User;
use AppBundle\Form\Security\CompanyType;
use AppBundle\Form\Security\LoginType;
use AppBundle\Form\Security\NowraceEmailType;
use AppBundle\Form\Security\ResetPasswordType;
use AppBundle\Form\Security\RunnerType;
use AppBundle\Form\Security\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

/**
 * Security controller.
 *
 * @Route("/")
 */
class SecurityController extends Controller
{
    /**
     * @Route("/login", name="security_login")
     */
    public function loginAction(Request $request, AuthenticationUtils $authUtils)
    {
        if($authUtils->getLastAuthenticationError()){
            $this->addFlash('error', 'Niepoprawne dane');
        }

        return $this->render(
            'security/login.html.twig',
            array(
                'error' => $authUtils->getLastAuthenticationError(),
                'last_username' => $authUtils->getLastUsername(),
            )
        );
    }

    /**
     * @Route("/register-runner", name="register-runner")
     */
    public function registerRunnerAction(Request $request)
    {
        $runner = new Runner();
        $form = $this->createForm(RunnerType::class, $runner);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $runner->getUser();
            $user->addRoles('ROLE_RUNNER');
            $user->setConfirmationToken(self::generateConfirmationToken($user));

            $password = $this->get('security.password_encoder')->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);

            $em = $this->getDoctrine()->getManager();
            $em->persist($runner);
            $em->flush();

            $this->sendActivationMail($user);
            $this->addFlash('success', 'Rejestracja przebiegła pomyślnie, na adres: ' . $user->getEmail() . ' przesłaliśmy link aktywacyjny.');

            return $this->redirectToRoute('security_login');
        }

        return $this->render(
            'security/register-runner.html.twig',
            array('form' => $form->createView())
        );
    }

    /**
     * @Route("/register-company", name="register-company")
     */
    public function registerCompanyAction(Request $request)
    {
        $company = new Company();
        $form = $this->createForm(CompanyType::class, $company);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $user = $company->getUser();
            $user->addRoles('ROLE_COMPANY');
            $user->setConfirmationToken(self::generateConfirmationToken($user));

            $password = $this->get('security.password_encoder')->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);

            $em = $this->getDoctrine()->getManager();
            $em->persist($company);
            $em->flush();

            $this->sendActivationMail($user);
            $this->addFlash('success', 'Rejestracja przebiegła pomyślnie, na adres: ' . $user->getEmail() . ' przesłaliśmy link aktywacyjny.');

            return $this->redirectToRoute('security_login');
        }

        return $this->render(
            'security/register-company.html.twig',
            array('form' => $form->createView())
        );
    }

    /**
     * Wyświetla anonimowemu użytkownikowi stronę z formularzem e-mail.
     * Użytkownik podaje tam swojego maila aby dostać link resetujący.
     *
     * @Route("/reset-password", name="reset-password")
     */
    public function resetPasswordAction(Request $request)
    {
        $form = $this->createForm(NowraceEmailType::class);
        $form->handleRequest($request);
        if($form->isValid()){
            $email = $form->getData()['email'];
            $user = $this->getDoctrine()->getRepository(User::class)->findOneByEmail($email);
            if ($user != null) {
                $user->setConfirmationToken(SecurityController::generateConfirmationToken($user));
                $em = $this->getDoctrine()->getManager();
                $em->flush();
                $this->sendPasswordResetMail($user);
                $this->addFlash('success', 'Wiadomość została wysłana na adres: ' . $email . '.');
                return $this->redirectToRoute('security_login');
            }else{
                $this->addFlash('error', 'Nieprawidłowy adres email');
            }
        }

        return $this->render('security/reset-password.html.twig',
            array(
                'form' => $form->createView()
            ));
    }

    /**
     * @Route("/new-password/{email}/{token}", name="new-password")
     */
    public function newPasswordAction($email, $token, Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->findOneByEmail($email);
        if (!$user){
            $this->addFlash('success', 'Nieprawidłowy adres email');
            return false;
        }
        if($token !== $user->getConfirmationToken()) {
            $this->addFlash('success', 'Nieprawidłowy token');
            return false;
        }

        $resetForm = $this->createForm(ResetPasswordType::class);
        $resetForm->handleRequest($request);
        if($resetForm->isSubmitted() && $resetForm->isValid()){
            $newPassword = $resetForm->getData();

            $password = $passwordEncoder->encodePassword($user, $newPassword['plainPassword']);
            $user->setPassword($password);
            $em->flush();

            $this->addFlash('success', 'Hasło zmienione');
            return $this->redirectToRoute('security_login');
        }

        return $this->render('security/new-password.html.twig',
            array(
                'form' => $resetForm->createView()
            ));
    }

    /**
     * @Route("/confirm/{email}/{token}", name="confirm_account")
     */
    public function confirmAction($email, $token)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->findOneByEmail($email);
        if (!$user) {
            $this->addFlash('error', 'Nieprawidłowy adres email');
            return $this->redirect($this->generateUrl('security_login'));
        }
        $isDriver = $user->hasRole('ROLE_DRIVER');
        if ($user->isIsActive()) {
            $this->addFlash('success', 'Twoje konto jest już aktywne');
            return $this->redirect($this->generateUrl('security_login'));
        }
        if($token !== $user->getConfirmationToken()) {
            $this->addFlash('error', 'Nieprawidłowy token');
            return $this->redirect($this->generateUrl('security_login'));
        }
        $user->setIsActive(true);
        $em->flush();

        $this->addFlash('success', 'Twoje konto zostało aktywowane możesz się zalogować');
        return $this->redirect($this->generateUrl('security_login'));

    }

    /**
     * @Route("/logout", name="security_logout")
     */
    public function logoutAction() {
        throw new Exception('This should not be reached!');
    }

    /**
     * @Route("/redirect-after-login", name="after_login")
     */
    public function redirectAfterLoginAction() {
        $user = $this->getUser();
        if($user){
            if($user->isIsActive()){
                return $this->redirectToRoute('homepage');
            }else{
                $this->addFlash('error', 'Użytkownik nieaktywny. Kliknij w link aktywacyjny przysłany w mailu');
                return $this->redirectToRoute('security_logout');
            }
        }else{
            return $this->redirectToRoute('security_login');
        }
    }

    /**
     * @param User $user
     */
    private function sendPasswordResetMail(User $user) {
        $messageBody = $this->get('twig')->render(
            'mailers/resert-password.html.twig',
            array(
                'email' => $user->getEmail(),
                'token' => $user->getConfirmationToken()
            )
        );
        $message = \Swift_Message::newInstance()
            ->setSubject("Przypomnienie hasła w serwisie NowRace.pl")
            ->setFrom("nowracenoreply@gmail.com")
            ->setTo($user->getEmail())
            ->setBody($messageBody, 'text/html');
        $this->get('mailer')->send($message);
    }

    /**
     * @param User $user
     */
    private function sendActivationMail(User $user) {
        $messageBody = $this->get('twig')->render(
            'mailers/activation.html.twig',
            array(
                'email' => $user->getEmail(),
                'token' => $user->getConfirmationToken()
            )
        );
        $message = \Swift_Message::newInstance()
            ->setSubject("Potwierdź swój email w serwisie NowRace.pl")
            ->setFrom("nowracenoreply@gmail.com")
            ->setTo($user->getEmail())
            ->setBody($messageBody, 'text/html');
        $this->container->get('mailer')->send($message);
    }

    /**
     * @param User $user
     * @return string
     */
    public static function generateConfirmationToken(User $user)
    {
        return hash('sha256', $user->getPlainPassword() . (new \DateTime())->format('Ymd'));
    }

    private function loginUser(User $user) {
        $token = new UsernamePasswordToken($user, null, 'public', $user->getRoles());
        $this->get("security.token_storage")->setToken($token);
        $request = $this->get("request_stack");
        $event = new InteractiveLoginEvent($request->getCurrentRequest(), $token);
        $this->get("event_dispatcher")->dispatch("security.interactive_login", $event);
    }
}
