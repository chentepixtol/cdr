<?php

namespace Altic\CdrBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        $request = $this->getRequest();
        $records = array();
        if( $request->isMethod("POST")){
            $records = $this->getMongoCdr()->getCollection()->find();
        }
        return $this->render('AlticCdrBundle:Default:index.html.twig', array('records' => $records));
    }

    /**
     * @return \Altic\CdrBundle\Mongo\Cdr
     */
    private function getMongoCdr(){
        return $this->container->get('altic_cdr.mongo_cdr');
    }
}
