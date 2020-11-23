<?php



namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
class SecurityController extends AbstractController
{

    /**
     * @Route("/redirect", name="redirect_login")
     */
    public function redirectAction()
    {$authChecker= $this->container->get('security.authorization_checker');
        if ($authChecker->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('doctor_clients');
        } else if ($authChecker->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('patient_doctors');
        }else{
            return $this->render('@FOSUser/Security/login_content.html.twig');
        }

    }
}