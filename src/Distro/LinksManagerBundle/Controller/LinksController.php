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
     * @Route("/", name="link_list" )
     * @Method("GET")
     * @Template()
     */
    public function listAction(Request $request)
    {
        //Mongodb List all

        //$hal = new Hal($this->generateUrl('link_list'), $rootData);
        $hal = new Hal('/list');

        //foreach($templates as $t) {
        //    $data = array(
        //        'id'          => $t->getId(),
        //        'title'       => $t->getTitle(),
        //        'description' => $t->getDescription(),
        //        'sructure'    => $t->getStructure(),
        //        'owner'       => $t->getOwner()
        //    );
        //    $resource_link   = $this->generateUrl('link_view', array('id' => $t->getId()) );
        //    $resource = new Hal($resource_link, $data);
        //    $resource->addLink( 'create', $this->generateUrl('link_create') );
        //    $hal->addResource(
        //        'template',
        //        $resource 
        //    );
        //}

        $response = new Response();
        $response->headers->set('Content-Type', 'application/hal+json');
        $response->setContent( $hal->asJson() );

        return $response;
    }

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

        //$now = new \DateTime();
        //$tomorrow = $now->modify('+1 day');

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
                'publish'     => $request->request->get('publish', new \MongoDate(strtotime('+1 day'))),
            );

        }
        $m = new \MongoClient(); // connect
        $col = $m->selectCollection("distro-2015","links");
        $col->insert($data);
print_r('<pre>'); print_r($data);
die;


        $create_link = $this->generateUrl('template_create');
        $hal = new Hal($create_link);
        $data = array(
            'id'          => $template->getId(),
            'title'       => $template->getTitle(),
            'description' => $template->getDescription(),
            'sructure'    => $template->getStructure(),
            'owner'       => $template->getOwner()
        );
        $resource_link = $this->generateUrl('template_view', array('id' => $template->getId()) );
        $hal->addResource(
            'template',
            new Hal($resource_link, $data)
        );
        return array(
            'result' => static::getResponse($hal)
        );
    } 

}//LinksController
