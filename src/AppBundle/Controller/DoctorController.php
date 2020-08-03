<?php


namespace AppBundle\Controller;


use AppBundle\Entity\Message;
use AppBundle\Entity\Rdv;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class DoctorController extends Controller
{
    /**
     * @Route("/doctor/show", name="doctor_show")
     */
    public function doctorAction()
    {

        $query = $this->getDoctrine()->getEntityManager()
            ->createQuery(
                'SELECT u FROM My:UserBundle:User u WHERE u.roles LIKE :role'
            )->setParameter('role', '%"ROLE_ADMIN"%'
            );
        $doctors = $query->getResult();


        return $this->render('doctor/show.html.twig', array(
            'posts' => $doctors,
        ));
    }

    /**
     * @Route("/doctor/create", name="doctor_create")
     */
    public function createClientAction(Request $request,UserPasswordEncoderInterface $encoder)
    {

        $doctor = $this->container->get('security.token_storage')->getToken()->getUser();

        $user = new User();
        $form = $this->createForm('AppBundle\Form\UserType', $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->addDoctor($doctor);
            $user->setEnabled(1);

            $plainPassword= $user->getPassword();

                 $encoded = $encoder->encodePassword($user, $plainPassword);

    $user->setPassword($encoded);
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('doctor_clients', array('id' => $user->getId()));
        }

        return $this->render('user/new.html.twig', array(
            'user' => $user,
            'form' => $form->createView(),
        ));
    }
    /**
     * @Route("/doctor/clients", name="doctor_clients")
     */
    public function clientsAction()
    {
        $doctor = $this->container->get('security.token_storage')->getToken()->getUser();
        $patient=$doctor->getPatients();

        return $this->render('doctor/clients.html.twig', array(
            'posts' => $patient,
        ));
    }
    /**
     * @Route("/doctor/clients/delete/{id}", name="doctor_clients_delete")
     */
    public function deleteAction($id)

    {  $doctor = $this->container->get('security.token_storage')->getToken()->getUser();

        $em = $this->getDoctrine()->getManager();
        $patient=$em->getRepository('AppBundle:User')->find($id);
        $em->flush();
       $doctor->removePatient($patient);

        $em = $this->getDoctrine()->getManager();
        $em->persist($patient);
        $em->flush();
        return $this->redirectToRoute("doctor_clients");



    }
    /**
     * @Route("/msg", name="msg_show")
     */
    public function msgshowAction(){
        $em = $this->getDoctrine()->getManager();

        $msgs = $em->getRepository('AppBundle:Message')->findAll();
        return $this->render('msg/msg.html.twig', array(
            'msgs' => $msgs,
        ));
    }
    /**
     * @Route("/doctor/msg/{id}", name="doctor_msg")
     */
    public function msgAction($id,Request $request){
        $doctor = $this->container->get('security.token_storage')->getToken()->getUser();
        $msg = new Message();
        $form = $this->createForm('AppBundle\Form\MsgDcType', $msg);
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        $patient=$em->getRepository('AppBundle:User')->find($id);
        if ($form->isSubmitted() && $form->isValid()) {
            $msg->setSender($doctor);
            $msg->setReceiver($patient);
            $em = $this->getDoctrine()->getManager();
            $em->persist($msg);
            $em->flush();
            return $this->redirectToRoute("msg_show");
        }
        return $this->render('msg/new.html.twig', array(
            'msg' => $msg,
            'form' => $form->createView(),
        ));
    }
    /**
     * @Route("/doctor/rd/{id}", name="doctor_rdv")
     */
    public function rdvAction($id,Request $request){
        $doctor = $this->container->get('security.token_storage')->getToken()->getUser();
        $rdv= new Rdv();
        $form = $this->createForm('AppBundle\Form\MsgDcType', $rdv);
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        $patient=$em->getRepository('AppBundle:User')->find($id);
        if ($form->isSubmitted() && $form->isValid()) {
            $msg->setSender($doctor);
            $msg->setReceiver($patient);
            $em = $this->getDoctrine()->getManager();
            $em->persist($msg);
            $em->flush();
            return $this->redirectToRoute("msg_show");
        }
        return $this->render('msg/new.html.twig', array(
            'msg' => $msg,
            'form' => $form->createView(),
        ));
    }
}