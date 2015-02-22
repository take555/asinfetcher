<?php

namespace app\controllers;

use app\models\Amazonitempage;
use Yii;
use app\components\UtilComponent;
use Symfony\Component\DomCrawler\Crawler;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use app\models\Category2;
use app\models\Card;


class AsinController extends Controller
{


    public function beforeAction($action)
    {
        return parent::beforeAction($action);
    }



    public function actionFetch()
    {

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $params = Yii::$app->request->post();

        $logParams = [
            \pKey::kTYPE => \pVal::kTYPE_PROCESS,
            \pKey::kMSG  => 'starting asin controller fetch action...',
            \pKey::kPARAMS => $params,
            \pKey::kFILE => __FILE__,
            \pKey::kLINE => __LINE__,
        ];

        \Yii::$app->flog->fetcherInfo($logParams);

        if(isset($params[Card::kParamsKeyTest]) && $params[Card::kParamsKeyTest] === Card::kParamsValueTestTrue){
            $logParams = [
                \pKey::kTYPE => \pVal::kTYPE_PROCESS,
                \pKey::kMSG  => 'THIS IS TEST CALL',
                \pKey::kPARAMS => $params,
                \pKey::kFILE => __FILE__,
                \pKey::kLINE => __LINE__,
            ];

            \Yii::$app->flog->fetcherInfo($logParams);

            $params[Card::kParamsKeyLimit] = 5;
        }



        if(!isset($params[Card::kParamsKeyCat2Id])){
            $logParams = [
                \pKey::kTYPE => \pVal::kTYPE_ERR,
                \pKey::kMSG  => 'post parameter category2Id not found.',
                \pKey::kPARAMS => $params,
                \pKey::kFILE => __FILE__,
                \pKey::kLINE => __LINE__,
            ];

            \Yii::$app->flog->fetcherErr($logParams);
            return $logParams;
        }

        if(!isset($params[Card::kParamsKeyLimit])){
            $logParams = [
                \pKey::kTYPE => \pVal::kTYPE_ERR,
                \pKey::kMSG  => 'post parameter limit not found.',
                \pKey::kPARAMS => $params,
                \pKey::kFILE => __FILE__,
                \pKey::kLINE => __LINE__,
            ];

            \Yii::$app->flog->fetcherErr($logParams);
            return $logParams;
        }

        if(!isset($params[Card::kParamsKeyOffset])){
            $logParams = [
                \pKey::kTYPE => \pVal::kTYPE_ERR,
                \pKey::kMSG  => 'post parameter offset not found.',
                \pKey::kPARAMS => $params,
                \pKey::kFILE => __FILE__,
                \pKey::kLINE => __LINE__,
            ];

            \Yii::$app->flog->fetcherErr($logParams);
            return $logParams;
        }

        if(isset($params['id'])){
            $infoMessage = "ID:".$params['id']." fetcher executing ...\n";
            $logParams = [
                \pKey::kTYPE => \pVal::kTYPE_PROCESS,
                \pKey::kMSG  => $infoMessage,
                \pKey::kPARAMS => $params,
                \pKey::kFILE => __FILE__,
                \pKey::kLINE => __LINE__,
            ];

            \Yii::$app->flog->fetcherInfo($logParams);
        }

        $logParams = [
            \pKey::kTYPE => \pVal::kTYPE_PROCESS,
            \pKey::kMSG  => 'amazon scraper executing...',
            \pKey::kPARAMS => $params,
            \pKey::kFILE => __FILE__,
            \pKey::kLINE => __LINE__,
        ];

        \Yii::$app->flog->fetcherInfo($logParams);

        return \Yii::$app->amazon_scraper->execute($params);


    }


    public function actionTest()
    {
        $params = Yii::$app->request->post();

        $logParams = [
            \pKey::kTYPE => \pVal::kTYPE_PROCESS,
            \pKey::kPARAMS => $params,
            \pKey::kFILE => __FILE__,
            \pKey::kLINE => __LINE__,
        ];

        \Yii::$app->flog->assignerInfo($logParams);

        $result =  \Yii::$app->amazon_scraper->executeAssignJobs(271, 2000, 10, 101);
//        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
//        $itemPageList = Amazonitempage::find()
//            ->limit(5)
//            ->asArray()
//            ->all();
//
//        foreach($itemPageList as $itemPage){
//            $crawler = new Crawler($itemPage['html']);
//            $itemCountDataList = \Yii::$app->amazon_scraper->getNumberOfExhibits($crawler);
//        }

//        $asinNoRankSetList1 = [
//            'ABCDE' => [
//                'new' => 2,
//                'used' => 2,
//                'asin' => 'ABCDE',
//            ],
//            'XYZ' => [
//                'new' => 5,
//                'used' => 5,
//                'asin' => 'XYZ',
//            ],
//            'OPQ' => [
//                'new' => 3,
//                'used' => 0,
//                'asin' => 'OPQ',
//            ],
//            'LMN' => [
//                'new' => 5,
//                'used' => 10,
//                'asin' => 'LMN',
//            ],
//        ];
//        //LMN , XYZ, ABCDE, OPQ
//
//
//
//        $asinNoRankSetList2 = [
//            'ABCDE' => [
//                'new' => 0,
//                'used' => 5,
//                'asin' => 'ABCDE',
//            ],
//            'XYZ' => [
//                'new' => 5,
//                'used' => 5,
//                'asin' => 'XYZ',
//            ],
//            'OPQ' => [
//                'new' => 3,
//                'used' => 5,
//                'asin' => 'OPQ',
//            ],
//            'LMN' => [
//                'new' => 0,
//                'used' => 10,
//                'asin' => 'LMN',
//            ],
//        ];
//
//        //LMN , XYZ, OPQ, ABCDE
//
//        $asinNoRankSetList3 = [
//            'ABCDE' => [
//                'new' => 0,
//                'used' => 0,
//                'asin' => 'ABCDE',
//            ],
//            'XYZ' => [
//                'new' => 0,
//                'used' => 0,
//                'asin' => 'XYZ',
//            ],
//            'OPQ' => [
//                'new' => 0,
//                'used' => 0,
//                'asin' => 'OPQ',
//            ],
//            'LMN' => [
//                'new' => 0,
//                'used' => 0,
//                'asin' => 'LMN',
//            ],
//        ];
//
//        $result1 = \Yii::$app->amazon_scraper->getSortedAsinNoRankSetList($asinNoRankSetList1);
//        $result2 = \Yii::$app->amazon_scraper->getSortedAsinNoRankSetList($asinNoRankSetList2);
//        $result3 = \Yii::$app->amazon_scraper->getSortedAsinNoRankSetList($asinNoRankSetList3);
//        $test = 0;
//        \Yii::info("POST PARAMS:".print_r($params,true)."\n", "infos");

        //$result =  \Yii::$app->amazon_scraper->assignChunkJob(271, 2000, 10, 101);

        return ['code' => 200, 'params' => $params];

    }


}
