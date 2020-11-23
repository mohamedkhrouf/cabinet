<?php


namespace AppBundle\Controller;

use AppBundle\Entity\Message;
use AppBundle\Entity\Rdv;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Notification;
use Symfony\Component\Routing\Annotation\Route;
/**
 * Discussion controller.
 *
 */
class NotificationController extends Controller
{
    /**
     * @Route("/display", name="notification_display")
     */
    public function displayAction(){
        $user= $this->container->get('security.token_storage')->getToken()->getUser();
        $notifications=$this->getDoctrine()->getManager()->getRepository('AppBundle:Notification')->findBy(array('description'=>$user->getUsername(),'route'=>'patient_msg'),array('date' => 'desc'));
        return $this->render('notif/notificationm.html.twig', array('notifications'=>$notifications));
    }
    /**
     * @Route("/rdisplay", name="notifrdv_display")
     */
    public function notifrdvAction(){
        $user= $this->container->get('security.token_storage')->getToken()->getUser();
        $notifications=$this->getDoctrine()->getManager()->getRepository('AppBundle:Notification')->findBy(array('description'=>$user->getUsername(),'icon'=>'new'),array('date' => 'desc'));
        return $this->render('notif/notification.html.twig', array('notifications'=>$notifications));
    }
    /**
     * @Route("/notif", name="notification_show")
     */
    public function notifAction(){

        return $this->render('notif/notifview.html.twig');
    }
    /**
     * @Route("notif/delete/{id}", name="notification_delete")
     */
    public function deleteAction($id)
    {
        $notification=$this->getDoctrine()->getRepository(Notification::class)->find($id);
        $em=$this->getDoctrine()->getManager();
        $em->remove( $notification);
        $em->flush();
        return $this->redirectToRoute("notification_display");
    }

    /**
     * @Route("notif/remove/{id}", name="notification_remove")
     */
    public function removeAction($id)
    {
        $notification=$this->getDoctrine()->getRepository(Notification::class)->find($id);
        $em=$this->getDoctrine()->getManager();
        $em->remove( $notification);
        $em->flush();
        return $this->redirectToRoute("notification_affich");
    }

    public function countmsgAction(){
        $user= $this->container->get('security.token_storage')->getToken()->getUser();
        $notifications=$this->getDoctrine()->getManager()->getRepository('AppBundle:Notification')->findBy(array('description'=>$user->getUsername(),'route'=>'patient_msg'),array('date' => 'desc'));
        $total=sizeof($notifications);
        return $this->render('notif/countmsg.html.twig', array('total'=>$total));
    }
    public function countrdvuAction(){
        $user= $this->container->get('security.token_storage')->getToken()->getUser();
        $notifications=$this->getDoctrine()->getManager()->getRepository('AppBundle:Notification')->findBy(array('description'=>$user->getUsername(),'icon'=>'modif'),array('date' => 'desc'));
        $total=sizeof($notifications);
        return $this->render('notif/countmsg.html.twig', array('total'=>$total));
    }
    public function countrdvnAction(){
        $user= $this->container->get('security.token_storage')->getToken()->getUser();
        $notifications=$this->getDoctrine()->getManager()->getRepository('AppBundle:Notification')->findBy(array('description'=>$user->getUsername(),'icon'=>'new'),array('date' => 'desc'));
        $total=sizeof($notifications);
        return $this->render('notif/countmsg.html.twig', array('total'=>$total));
    }
    /**
     * @Route("/rudisplay", name="notifrdvu_display")
     */
    public function notifrdvuAction(){
        $user= $this->container->get('security.token_storage')->getToken()->getUser();
        $notifications=$this->getDoctrine()->getManager()->getRepository('AppBundle:Notification')->findBy(array('description'=>$user->getUsername(),'icon'=>'modif'),array('date' => 'desc'));
        return $this->render('notif/notification.html.twig', array('notifications'=>$notifications));
    }

}