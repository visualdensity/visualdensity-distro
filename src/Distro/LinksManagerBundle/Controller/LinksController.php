<?php
namespace Distro\LinksManagerBundle\Controller;

use Nocarrier\Hal;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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

        return array(
            'result' => $hal->asJson()
        );
    }

}//LinksController
