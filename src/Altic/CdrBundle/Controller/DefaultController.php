<?php

namespace Altic\CdrBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    /**
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $request = $this->getRequest();
        $records = array();
        if( $request->isMethod("POST") ){
            $filter = array();
            $startDate = $request->request->get('start_date');
            $endDate = $request->request->get('end_date');

            if( $startDate ){
                $filter = array_merge($filter, array('starttime' => array('$gte' => $startDate)));
            }
            if( $endDate ){
                $filter = array_merge($filter, array('starttime' => array('$lte' => $endDate)));
            }
            if( $startDate  && $endDate ){
                $filter = array_merge($filter, array('starttime' => array('$gte' => $startDate, '$lte' => $endDate)));
            }

            $records = $this->getMongoCdr()->getCollection()->find($filter);
        }
        return $this->render('AlticCdrBundle:Default:index.html.twig', array('records' => $records, 'params' => $request->request->all()));
    }

    /**
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function singleSeriesChartAction(){

        $params = $this->getRequest()->request;

        if( $this->getRequest()->isMethod("POST") ){
            $field = $params->get('field');

            $result = $this->getMongoCdr()->getCountByField($field);
            echo "<pre>";print_r($result);die();

        }


        return $this->render('AlticCdrBundle:Default:single.html.twig', array());
    }

    /**
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function multipleSeriesChartAction(){

        $params = $this->getRequest()->request;

        if( $this->getRequest()->isMethod("POST") ){
            $field = $params->get('field');
            $fieldTwo = $params->get('fieldTwo');

            $result = $this->getMongoCdr()->getCountByFields($field, $fieldTwo);
            echo "<pre>";print_r($result);die();

        }

        return $this->render('AlticCdrBundle:Default:multiple.html.twig', array());
    }



    /**
     * @return \Altic\CdrBundle\Mongo\Cdr
     */
    private function getMongoCdr(){
        return $this->container->get('altic_cdr.mongo_cdr');
    }
}
