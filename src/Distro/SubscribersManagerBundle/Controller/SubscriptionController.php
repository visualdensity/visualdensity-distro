<?php
namespace Distro\SubscribersManagerBundle\Controller;

use Nocarrier\Hal;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;


/**
 * @Route("/subscribe")
 */
class SubscriptionController extends Controller
{

    /**
     * @Route("/", name="subscribe_create")
     * @Method("POST")
     * @Template()
     */
    public function createAction(Request $request)
    {
        $errors = array();

        if( $request->request->get('name') ) {
            $title = $request->request->get('name');
        } else {
            $errors[] = "Please provide a name";
        }

        if( $request->request->get('email') ) {
            $title = $request->request->get('email');
        } else {
            $errors[] = "Please provide an email";
        }

        if( $request->request->get('subscription') ) {
            $title = $request->request->get('subscription');
        } else {
            $errors[] = "Please subscribe to at least one category";
        }

        if( count($errors) > 0 ) {
            $data = array(
                'errors' => $errors
            );
        } else {
            $data = array(
                'name'         => $request->request->get('name'),
                'email'        => $request->request->get('email'),
                'subscription' => $request->request->get('subscription'),
                'status'       => $request->request->get('status'),
                'created'      => new \MongoDate(),
            );
        }

        $m = new \MongoClient(); // connect
        $col = $m->selectCollection("distro-2015","users");
        
        if( !$col->insert($data) ) {
            $self_link = $this->generateUrl('subscribe_create');
            $hal = new Hal($self_link, array('error' => 'Could not save record'));

            $response = new Response();
            $response->headers->set('Content-Type', 'application/hal+json');
            $response->setContent( $hal->asJson() );
        } else {
            $response = $this->redirect($this->generateUrl('subscribe_view', array('id' => (string) $data['_id'])));
        }

        return $response;
    }//createAction


    /**
     * @Route("/{id}", name="subscribe_view")
     * @Method("GET")
     * @Template()
     */
    public function viewAction($id)
    {
        $m = new \MongoClient(); // connect
        $col = $m->selectCollection("distro-2015","users");
        $subscriber = $col->findOne(array('_id' => new \MongoId($id)));

        $response = new Response();

        if($subscriber) {
            $data['id'] = (string) $subscriber['_id'];
        } else {
            $data['error'] = 'Subscription not found. Error had occured';
            $response->setStatusCode(404);
        }
        
        $self_link = $this->generateUrl('subscribe_view',array('id' => $id) );
        $hal = new Hal($self_link, $data);
        $response->headers->set('Content-Type', 'application/hal+json');
        $response->setContent( $hal->asJson() );

        return $response;
    }//viewAction


}//SubscriptionController

