<?php


namespace AppBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
class PatientController extends Controller
{
    /**
     * @Route("/patient/show", name="patient_show")
     */
    public function patientAction()
    {

        $query = $this->getDoctrine()->getEntityManager()
            ->createQuery(
                'SELECT u FROM AppBundle:User u WHERE u.roles LIKE :role'
            )->setParameter('role', '%"ROLE_USER"%'
            );
        $patients = $query->getResult();


        return $this->render('patient/show.html.twig', array(
            'posts' => $patients,
        ));
    }
    /**
     * @Route("/patient/msg", name="patient_msg")
     */
    public function showmsgAction(){
        $patient = $this->container->get('security.token_storage')->getToken()->getUser();
        $query = $this->getDoctrine()->getEntityManager()
        ->createQuery(
            'SELECT m FROM AppBundle:Message m WHERE m.receiver = :receiver'
        )->setParameter('receiver', $patient
        );
        $msgs = $query->getResult();
        return $this->render('msg/msg.html.twig', array(
            'msgs' => $msgs,
        ));
    }
}