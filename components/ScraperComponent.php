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






    public function postMulti(array $postParamsList, $fillContent = false, $timeOut = 60)
    {

        $multiHandler = curl_multi_init();

        $channels = [];




        foreach ($postParamsList as $key => $postParams) {

            if(!isset($postParams[self::kParamsKeyUrl])){
                $logParams = [
                    \pKey::kTYPE => \pVal::kTYPE_PROCESS,
                    \pKey::kMSG    => "url not found {$key}",
                    \pKey::kPARAMS => $postParams,
                    \pKey::kFILE => __FILE__,
                    \pKey::kLINE => __LINE__,
                ];

                \Yii::$app->flog->assignerInfo($logParams);
                continue;
            }


            $tmpParams = [];

            if(isset($postParams[self::kParamsKeyParams]) && is_array($postParams[self::kParamsKeyParams])){
                $tmpParams = $postParams[self::kParamsKeyParams];
            }

            $channels[$key] = curl_init();
            curl_setopt_array($channels[$key], [
                CURLOPT_URL => $postParams[self::kParamsKeyUrl],
                CURLOPT_TIMEOUT => $timeOut,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $tmpParams,

            ]);

            curl_multi_add_handle($multiHandler, $channels[$key]);
        }

        $active = null;

        do {
            $status = curl_multi_exec($multiHandler, $active);
        }

        while ($status == CURLM_OK && $active);

        $responseList = [];
        $responseInfoList = [];

        foreach($channels as $key => $ch){
            $tmpContent = curl_multi_getcontent($ch)."\n\n";


            $responseData = [];
            $responseInfo = [];

            $responseData['content'] = $tmpContent;

            if(!empty($tmpContent)){
                if($fillContent === true){

                    $responseInfo['content'] = $tmpContent;

                } else {

                    $responseInfo['content'] = mb_strlen($tmpContent);

                }
            } else {
                $responseInfo['content'] = 0;
            }

            if(isset($postParamsList[$key][self::kParamsKeyUrl])){
                $responseData[self::kParamsKeyUrl] = $postParamsList[$key][self::kParamsKeyUrl];
                $responseInfo[self::kParamsKeyUrl] = $postParamsList[$key][self::kParamsKeyUrl];
            }

            if(isset($postParamsList[$key][self::kParamsKeyParams])){
                $responseData[self::kParamsKeyParams] = $postParamsList[$key][self::kParamsKeyParams];
                $responseInfo[self::kParamsKeyParams] = $postParamsList[$key][self::kParamsKeyParams];
            }

            $responseList[] = $responseData;
            $responseInfoList[] = $responseInfo;

            curl_multi_remove_handle($multiHandler, $ch);
            curl_close($ch);
        }

        curl_multi_close($multiHandler);


        $logParams = [
            \pKey::kTYPE => \pVal::kTYPE_PROCESS,
            \pKey::kPARAMS => $responseInfoList,
            \pKey::kFILE => __FILE__,
            \pKey::kLINE => __LINE__,
        ];

        \Yii::$app->flog->assignerInfo($logParams);

        return $responseList;

    }

}