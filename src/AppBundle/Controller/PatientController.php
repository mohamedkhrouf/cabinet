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
     * @Route("/patient/msgs/{id}", name="patient_msg")
     */
    public function showmsgAction($id){
        $em = $this->getDoctrine()->getManager();
        $doctor=$em->getRepository('AppBundle:User')->find($id);
        $patient = $this->container->get('security.token_storage')->getToken()->getUser();
        $query = $this->getDoctrine()->getEntityManager()
        ->createQuery(
            'SELECT m FROM AppBundle:Message m WHERE m.receiver = :receiver and m.sender = :sender'
        )->setParameter('receiver', $patient
        )->setParameter('sender', $doctor
            )
        ;
        $msgs = $query->getResult();
        return $this->render('msg/msgp.html.twig', array(
            'msgs' => $msgs,
        ));
    }
    /**
     * @Route("/patient/rdv/{id}", name="patient_rdv")
     */
    public function showrdvAction($id){
        $em = $this->getDoctrine()->getManager();
        $doctor=$em->getRepository('AppBundle:User')->find($id);
        $patient = $this->container->get('security.token_storage')->getToken()->getUser();
        $query = $this->getDoctrine()->getEntityManager()
            ->createQuery(
                'SELECT m FROM AppBundle:Rdv m WHERE m.patient = :patient and m.doctor = :doctor'
            )->setParameter('patient', $patient
            )->setParameter('doctor', $doctor
             );
        $rdvs = $query->getResult();
        return $this->render('rdv/rdv.html.twig', array(
            'rdvs' => $rdvs,
        ));
    }
    /**
     * @Route("/patient/doctors", name="patient_doctors")
     */
    public function doctorsAction()
    {
        $patient = $this->container->get('security.token_storage')->getToken()->getUser();
        $doctor=$patient->getDoctors();

        return $this->render('patient/doctors.html.twig', array(
            'posts' => $doctor,
        ));
    }
    /**
     * @Route("/patient/rv", name="patient_rv")
     */
    public function showrvAction(){
        $em = $this->getDoctrine()->getManager();

        $patient = $this->container->get('security.token_storage')->getToken()->getUser();
        $query = $this->getDoctrine()->getEntityManager()
            ->createQuery(
                'SELECT m FROM AppBundle:Rdv m WHERE m.patient = :patient '
            )->setParameter('patient', $patient

            );
        $rdvs = $query->getResult();
        return $this->render('rdv/rdvp.html.twig', array(
            'rdvs' => $rdvs,
        ));
    }
}