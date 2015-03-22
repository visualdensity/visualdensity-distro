<?php
namespace Distro\LinksManagerBundle\Controller;

use Nocarrier\Hal;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;


/**
 * @Route("/manager/link")
 */
class LinksController extends Controller
{

    /**
     * @Route("/today/{category}/{limit}", name="link_today", defaults={"limit":"5"} )
     * @Method("GET")
     * @Template()
     */
    public function listAction($category, $limit)
    {

        $data = null;

        $start_date = new \MongoDate(strtotime(date('j F Y 00:00:00')));
        $end_date   = new \MongoDate(strtotime(date('j F Y 23:59:59')));

        $m = new \MongoClient(); // connect
        $col = $m->selectCollection("distro-2015","links");

        $links = $col->find(array(
            'category' => $category,
            'publish'  => array('$gt' => $start_date, '$lte' => $end_date)
        ))->limit($limit);

        $hal = new Hal($this->generateUrl('link_today', array('category' => $category)));
        foreach($links as $id => $l ) {
            $data = array(
                'id'          => (string) $l['_id'],
                'title'       => $l['title'],
                'destination' => $l['destination'],
                'description' => $l['description'],
                'category'    => $l['category'],
                'publish'     => date('Y-M-d', $l['publish']->sec),
            );

            $self_link = $this->generateUrl('link_view', array('id' => $data['id']) );
            $resource = new Hal($self_link, $data);
            $hal->addResource(
                'link',
                $resource 
            );
        }

        $response = new Response();
        $response->headers->set('Content-Type', 'application/hal+json');
        $response->setContent( $hal->asJson() );

        return $response;
    }//listAction

    /**
     * @Route("/{id}", name="link_view")
     * @Method("GET")
     * @Template()
     */
    public function viewAction($id)
    {
        $m = new \MongoClient(); // connect
        $col = $m->selectCollection("distro-2015","links");
        $data = $col->findOne(array('_id' => new \MongoId($id)));

        $self_link = $this->generateUrl('link_view',array('id' => $id) );

        $hal = new Hal($self_link);
        $hal->addResource(
            'link',
            new Hal($this->generateUrl('link_view', array( 'id' => $id)), $data) 
        );

        $response = new Response();
        $response->headers->set('Content-Type', 'application/hal+json');
        $response->setContent( $hal->asJson() );

        return $response;

    }//viewAction


    /**
     * @Route("/", name="link_create")
     * @Method({"POST", "PUT"})
     * @Template()
     */
    public function createAction(Request $request)
    {
        $errors = array();

        if( $request->request->get('title') ) {
            $title = $request->request->get('title');
        } else {
            $errors[] = "Please provide a title";
        }

        if( $request->request->get('destination') ) {
            $title = $request->request->get('destination');
        } else {
            $errors[] = "Please provide a destination link or URL";
        }

        if( $request->request->get('category') ) {
            $title = $request->request->get('destination');
        } else {
            $errors[] = "Please provide a category";
        }

        if( count($errors) > 0 ) {
            $data = array(
                'errors' => $errors
            );
        } else {
            $data = array(
                'title'       => $request->request->get('title'),
                'destination' => $request->request->get('destination'),
                'category'    => $request->request->get('category'),
                'description' => $request->request->get('description'),
                'publish'     => new \MongoDate(strtotime($request->request->get('publish') . ' 03:00:00')),
            );
        }

        $m = new \MongoClient(); // connect
        $col = $m->selectCollection("distro-2015","links");
        
        if( !$col->insert($data) ) {
            $create_link = $this->generateUrl('link_create');
            $hal = new Hal($create_link, array('error' => 'Could not save record'));

            $response = new Response();
            $response->headers->set('Content-Type', 'application/hal+json');
            $response->setContent( $hal->asJson() );
        } else {
            $response = $this->redirect($this->generateUrl('link_view', array('id' => (string) $data['_id'])));
        }

        return $response;
    } 

}//LinksController
