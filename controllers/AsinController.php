<?php

namespace app\controllers;

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


        $params = Yii::$app->request->post();

        \Yii::info("POST PARAMS:".print_r($params,true)."\n", "infos");

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        if(!isset($params[Card::kParamsKeyCat2Id])){
            $errorJson = ['err' => 'post parameter category2Id not found.'];
            return $errorJson;
        }

        if(!isset($params[Card::kParamsKeyLimit])){
            $errorJson = ['err' => 'post parameter limit not found.'];
            return $errorJson;
        }

        if(!isset($params[Card::kParamsKeyOffset])){
            $errorJson = ['err' => 'post parameter offset not found.'];
            return $errorJson;
        }

        if(isset($params['id'])){
            $infoMessage = "ID:".$params['id']." fetcher executing ...\n";
            \Yii::info($infoMessage, 'infos');
        }

        \Yii::$app->amazon_scraper->execute($params);


    }


    public function actionTest()
    {
        $params = Yii::$app->request->post();

        \Yii::info("POST PARAMS:".print_r($params,true)."\n", "infos");

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        return ['code' => 200, 'params' => $params];

    }


}
