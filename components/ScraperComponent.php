<?php
/**
 * Created by PhpStorm.
 * User: youyaimac
 * Date: 2015/01/28
 * Time: 20:39
 */

namespace app\components;

use Yii;
use yii\base\Component;
use Goutte\Client;

class ScraperComponent extends Component
{


    public $client;

    public $clawler;

    public $userAgent;

    public $util;

    const kParamsKeyUrl = 'url';

    const kParamsKeyParams = 'params';

    public function init()
    {
        parent::init();
        $this->util = Yii::$app->util;
        $this->client = new Client();
        $this->setClientConfig();
    }

    private function setClientConfig()
    {
        $this->setUserAgent();
    }


    public function setCurlOption($curlKey, $value)
    {
        if($this->client instanceof Client){
            $this->client->getClient()->setDefaultOption('config/curl/'.$curlKey, $value);
        }
    }

    public function setUserAgent()
    {
        if(!empty($this->userAgent)){
            $this->client->setHeader('User-Agent', $this->userAgent);
        }

    }


    public function getCrawlerFromUrl($url)
    {
        $crawler = $this->client->request('GET', $url);
        $headers = $this->client->getResponse()->getHeaders();
        return $crawler;
    }

    public function getCrawlerFromHtml($html)
    {
        return $this->client->request('', '',[], [], [], $html);

    }

    public function postMulti(array $postParamsList)
    {

        $multiHandler = curl_multi_init();

        $channels = [];

        $timeout = 6 * 60 * 60;// 6 hours



        foreach ($postParamsList as $key => $postParams) {

            if(!isset($postParams[self::kParamsKeyUrl])){
                echo "url not found:{$key}\n";
                continue;
            }

            echo "URL:".$postParams['url']."\n";

            $channels[$key] = curl_init();
            curl_setopt_array($channels[$key], [
                CURLOPT_URL => $postParams[self::kParamsKeyUrl],
                CURLOPT_TIMEOUT => $timeout,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $postParams[self::kParamsKeyParams],

            ]);

            curl_multi_add_handle($multiHandler, $channels[$key]);
        }

        $active = null;

        do {
            $status = curl_multi_exec($multiHandler, $active);
        }

        while ($status == CURLM_OK && $active);

        $responseList = [];

        foreach($channels as $ch){
            $tmpContent = curl_multi_getcontent($ch)."\n\n";

            $responseList[] = $tmpContent;
            \Yii::info("RESPONSE:{$tmpContent}\n", 'cli_infos');
            curl_multi_remove_handle($multiHandler, $ch);
            curl_close($ch);
        }

        curl_multi_close($multiHandler);

        \Yii::info("###########################################", 'cli_infos');
        \Yii::info("#          ALL PROCESS FINISHED           #", 'cli_infos');
        \Yii::info("###########################################", 'cli_infos');
        \Yii::info("###########################################", 'cli_infos');
        \Yii::info("########    RESPONSE SUMMERY     ##########", 'cli_infos');
        \Yii::info("###########################################", 'cli_infos');
        \Yii::info(print_r($responseList, true), 'cli_infos');
        \Yii::info("###########################################", 'cli_infos');

    }

}