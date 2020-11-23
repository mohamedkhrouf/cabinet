<?php


namespace AppBundle\Controller;


use AppBundle\Entity\Message;
use AppBundle\Entity\Rdv;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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
                'SELECT u FROM AppBundle:User u WHERE u.roles LIKE :role'
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
            $user->makeUser();
            $plainPassword= '123';

            $encoded = $encoder->encodePassword($user, $plainPassword);

            $user->setPassword($encoded);
            $em = $this->getDoctrine()->getManager();
            $user1=$em->getRepository('AppBundle:User')->findOneBy(array('username' => $user->getUsername()));

            if ($user1 != null){
             $user=$user1;
            }
            if (!$user->hasRole('ROLE_ADMIN')) {
                $user->addDoctor($doctor);
                $user->setEnabled(1);


                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();
            }
            return $this->redirectToRoute('doctor_clients');
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
        return $this->redirectToRoute("doctor_create");



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
            /** @var UploadedFile $uploadedFile */
            $uploadedFile=$form['pFile']->getData();
            $destination = $this->getParameter('kernel.project_dir').'/public/uploads';
            $originalFilename= pathinfo($uploadedFile->getClientOriginalName(),PATHINFO_FILENAME);
            $newFileName = $originalFilename.'-'.uniqId().'-'.$uploadedFile->guessExtension();
          $uploadedFile->move($destination
                ,$newFileName
            );
          $msg->setFile($newFileName);
            $msg->setSender($doctor);
            $msg->setReceiver($patient);
            $em = $this->getDoctrine()->getManager();
            $em->persist($msg);
            $em->flush();
            return $this->redirectToRoute('doctors_msg', array('id'=>$id));
        }
        return $this->render('msg/new.html.twig', array(
            'msg' => $msg,
            'form' => $form->createView(),
        ));

    }
    /**
     * @Route("/upload", name="upload")
     */
    public function uploadAction(Request $request){
        /** @var UploadedFile $uploadedFile */
 $uploadedFile = $request->files->get('image');
 $destination = $this->getParameter('kernel.project.dir').'/public/uploads';
 $originalFilename= pathinfo($uploadedFile->getClientOriginalName(),PATHINFO_FILENAME);
 $newFileName = $originalFilename.'-'.uniqId().'-'.$uploadedFile->guessExtension();
 dd($uploadedFile->move($destination
 ,$newFileName
     ));
    }
    /**
     * @Route("/rdv", name="rdv_show")
     */
    public function rdvshowAction(){
        $em = $this->getDoctrine()->getManager();

        $rdvs = $em->getRepository('AppBundle:Rdv')->findAll();
        return $this->render('rdv/rdv.html.twig', array(
            'rdvs' => $rdvs,
        ));
    }
    /**
     * @Route("/rdvd", name="rdv_shower")
     */
    public function rdvdshowAction(){
        $em = $this->getDoctrine()->getManager();
        $doctor = $this->container->get('security.token_storage')->getToken()->getUser();
        $rdvs = $em->getRepository('AppBundle:Rdv')->findby(array('doctor'=>$doctor));
        return $this->render('rdv/rdv.html.twig', array(
            'rdvs' => $rdvs,
        ));
    }
    /**
     * @Route("/doctor/rdv/{id}", name="doctor_rdv")
     */
    public function rdvAction($id,Request $request){
        $doctor = $this->container->get('security.token_storage')->getToken()->getUser();
        $rdv= new Rdv();
        $form = $this->createForm('AppBundle\Form\RdvType', $rdv);
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        $patient=$em->getRepository('AppBundle:User')->find($id);
        if ($form->isSubmitted() && $form->isValid()) {
            $rdv->setDoctor($doctor);
            $rdv->setPatient($patient);
            $em = $this->getDoctrine()->getManager();
            $em->persist($rdv);
            $em->flush();
            return $this->redirectToRoute('doctors_rdv',array('id'=>$id));
        }
        return $this->render('rdv/new.html.twig', array(
            'rdv' => $rdv,
            'form' => $form->createView(),
        ));
    }
    /**
     * @Route("/calendar", name="calendar")
     */
    public function calenderAction(){
        return $this->render('calendar/calendar.html.twig');
    }
    /**
     * @Route("/patient/msg/{id}", name="patient_msgp")
     */
    public function sendMailAction(Request $request,$id){
        $sender = $this->container->get('security.token_storage')->getToken()->getUser();
        $mail = new Message();
        $em = $this->getDoctrine()->getManager();
        $receiver=$em->getRepository('AppBundle:User')->find($id);
        $mail->setSender($sender);
        $mail->setReceiver($receiver);
        $form = $this->createForm('AppBundle\Form\MsgPPtType',$mail);
        $form ->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $titre= $mail->getTitre();
            $text=$mail->getText();
            $r = $mail->getReceiver();
            $s = $mail->getSender();
            $rMail=$r->getEmail();
            $sMail=$s->getEmail();
            $message= \Swift_Message::newInstance()
                ->setSubject($titre)
                ->setFrom($sMail)
                ->setTo($rMail)
                ->setBody($text);
                $this->get('mailer')->send($message);
            $this ->get('session')->getFlashBag()->add('notice','message envoyé');
            $em = $this->getDoctrine()->getManager();
            $em->persist($mail);
            $em->flush();
            return $this->redirectToRoute("patient_doctors");
        }
        $em = $this->getDoctrine()->getManager();
        $msgs=$em->getRepository('AppBundle:Message')->findAll();

        return $this->render('msg/newp.html.twig', array(
            'msg' => $mail,
            'form' => $form->createView(),
        ));
        }
    /**
     * @Route("/doctor/msgs/{id}", name="doctors_msg")
     */
    public function showmAction($id){
        $em = $this->getDoctrine()->getManager();
        $patient=$em->getRepository('AppBundle:User')->find($id);
        $doctor = $this->container->get('security.token_storage')->getToken()->getUser();
        $query = $this->getDoctrine()->getEntityManager()
            ->createQuery(
                'SELECT m FROM AppBundle:Message m WHERE m.receiver = :receiver and m.sender = :sender'
            )->setParameter('receiver', $patient
            )->setParameter('sender', $doctor
            )
        ;
        $msgs = $query->getResult();
        return $this->render('msg/msg.html.twig', array(
            'msgs' => $msgs,
        ));
    }
    /**
     * @Route("/doctors/rdv/{id}", name="doctors_rdv")
     */
    public function showrAction($id){
        $em = $this->getDoctrine()->getManager();
        $patient=$em->getRepository('AppBundle:User')->find($id);
        $doctor = $this->container->get('security.token_storage')->getToken()->getUser();
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
     * @Route("/doctors/delete/{id}", name="rdv_delete")
     */
    public function deleterAction($id){




        $em = $this->getDoctrine()->getManager();
        $rdv=$em->getRepository('AppBundle:Rdv')->find($id);
        $em->remove($rdv);
        $em->flush();


        return $this->redirectToRoute('rdv_shower');
    }
    /**
     * @Route("/doctors/edit/{id}", name="rdv_edit")
     */
    public function editrAction($id,Request $request){


        $em = $this->getDoctrine()->getManager();
        $rdv=$em->getRepository('AppBundle:Rdv')->find($id);
        $form = $this->createForm('AppBundle\Form\RdvType', $rdv);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($rdv);
            $em->flush();
            return $this->redirectToRoute('rdv_shower');
        }


        return $this->render('rdv/edit.html.twig', array(
            'rdv' => $rdv,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/clients/show/{id}", name="clients_show")
     */
    public function showcAction($id){

        $em = $this->getDoctrine()->getManager();
        $client=$em->getRepository('AppBundle:User')->find($id);
        return $this->render('user/show.html.twig', array(
            's' => $client));
    }

}