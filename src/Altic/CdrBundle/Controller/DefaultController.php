<?php

namespace Altic\CdrBundle\Controller;

use FChandy\Chart;

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

            $chart = new Chart("Comportamiento", $field, 'numero de llamadas');
            foreach ($result as $label => $value){
                $chart->addSet($value, $label);
            }
        }

        return $this->render('AlticCdrBundle:Default:single.html.twig', compact('chart'));
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
            $chart = new Chart("Comportamiento", "$field vs $fieldTwo", 'numero de llamadas');
            $seriesNames = $categories = array();
            foreach( $result as $labelOne => $data ){
                $categories[$labelOne] = 1;
                foreach( $data as $labelTwo => $value ){
                    if( !isset($seriesNames[$labelTwo]) ){
                        $seriesNames[$labelTwo] = array($value);
                    }else{
                        $seriesNames[$labelTwo][] = $value;
                    }
                }
            }
            foreach (array_keys($categories) as $category){
                $chart->addCategory($category);
            }
            foreach ($seriesNames as $serieName => $values){
                $chart->addDataset($serieName, $values);
            }

        }

        return $this->render('AlticCdrBundle:Default:multiple.html.twig', compact('chart'));
    }



    /**
     * @return \Altic\CdrBundle\Mongo\Cdr
     */
    private function getMongoCdr(){
        return $this->container->get('altic_cdr.mongo_cdr');
    }
}
