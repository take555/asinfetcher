<?php

namespace app\commands;

use app\components\UtilComponent;
use Symfony\Component\DomCrawler\Crawler;
use yii\console\controller;
use app\models\Category2;
use app\models\Card;

class AsinController extends Controller
{

    public $targetCategory2;

    public $offset;

    public $limit;


    public function behaviors()
    {
        return [
        ];
    }

    const FETCHER_ACTION_QUERY_PART = "/?r=asin/fetch";

    public function actionFetch($category2Id = 271, $offset, $limit, $id)
    {


        if(is_null($offset)){
            echo "second parameter not specified. process terminated.\n";
            return 0;
        }

        if(is_null($limit)){
            echo "third parameter not specified. process terminated.\n";
            return 0;
        }

        if(is_null($id)){
            echo "4th parameter not specified. process terminated.\n";
            return 0;
        }

        $params = [
            Card::kParamsKeyCat2Id => intval($category2Id),
            Card::kParamsKeyOffset  => intval($offset),
            Card::kParamsKeyLimit  => intval($limit),
        ];

        \Yii::$app->amazon_scraper->execute($params);

        \Yii::info("###########################################\n", 'infos');
        \Yii::info("#                   DONE                  #\n", 'infos');
        \Yii::info("###########################################\n", 'infos');

        return 0;

    }



    public function actionOrchestrate($category2Id = 271)
    {

        \Yii::$app->amazon_scraper->assign($category2Id);
        return 0;

    }



    public function actionTest()
    {
        \Yii::info("###########################################\n", 'infos');
        \Yii::info("#             START TEST                  #\n", 'infos');
        \Yii::info("###########################################\n", 'infos');
        $params = [
            Card::kParamsKeyCat2Id => 271,
            Card::kParamsKeyOffset  => 690,
            Card::kParamsKeyLimit  => 2,
        ];

        \Yii::$app->amazon_scraper->curlTest();

//        $list = [
//            'a' => 111,
//            'b' => 111,
//            'c' => 111,
//            'd' => 111,
//            'e' => 111,
//            'f' => 111,
//        ];
//
//        $key = \Yii::$app->util->getBackwardNeighborArrayKey('a', $list);
//
//        print_r($key);

//        \Yii::$app->amazon_scraper->execute($params);





//        $testList = [
//            'a' => ['b' => ['c' => 2000]],
//            'd' => 800,
//            'e' => ['f' => 4321]
//        ];
//
//
//        $result = \Yii::$app->util->getValueFromMultiDimensionArray('a.b.c', $testList);
//        $result1 = \Yii::$app->util->getValueFromMultiDimensionArray('d', $testList);
//        $result2 = \Yii::$app->util->getValueFromMultiDimensionArray('e.f', $testList);
//        $result3 = \Yii::$app->util->getValueFromMultiDimensionArray('a.b.d', $testList);
//        $result4 = \Yii::$app->util->getValueFromMultiDimensionArray('', $testList);
//        $result5 = \Yii::$app->util->getValueFromMultiDimensionArray('x', $testList);

//        $wordList = [
//            'id' => 7106662,
//            'name' => 'ハネクリボー',
//            'rarity_short' => 'UR',
//            'serial' => 'GX1-JP002',
//            'box' => ['name' => 'ゲーム付属'],
//        ];
//
//        $scraper = \Yii::$app->amazon_scraper;
//        $util = \Yii::$app->util;

//Time function test
//        $checkTimeDataList = [
//            ['expire' => 2 * 24 * 60 * 60, 'from' => '2015-01-28 00:00:00', 'target' => '2015-01-28 00:00:00'],
//            ['expire' => 1 * 24 * 60 * 60, 'from' => '2015-01-29 00:00:00', 'target' => '2015-01-30 00:00:01'],
//            ['expire' => 1 * 24 * 60 * 60, 'from' => '2015-01-29 00:00:00', 'target' => '2015-01-29 23:55:55'],
//            ['expire' => 2 * 60 * 60,      'from' => '2015-01-28 00:00:00', 'target' => '2015-01-28 02:00:00'],
//            ['expire' => 2 * 60 * 60,      'from' => '2015-01-28 00:00:00', 'target' => '2015-01-28 02:00:01'],
//            ['expire' => 2 * 60 * 60,      'from' => '2015-01-28 00:00:00', 'target' => '2015-01-28 01:59:59'],
//            ['expire' => 30 * 60,          'from' => '2015-01-28 00:00:00', 'target' => '2015-01-28 00:30:01'],
//            ['expire' => 30 * 60,          'from' => '2015-01-28 00:00:00', 'target' => '2015-01-28 00:29:59'],
//
//        ];
//
//        foreach ($checkTimeDataList as $checkTimeData) {
//            $tmpExpireString = $util->getDefaultTimeStringFromSec($checkTimeData['expire']);
//            if($util->isPassedDateTime($checkTimeData['from'], $checkTimeData['target'], $checkTimeData['expire']))
//            {
//                echo "PASSED FROM {$checkTimeData['from']} TO {$checkTimeData['target']} EXPIRE {$tmpExpireString}       \n\n";
//            } else {
//                echo "NOT PASSED FROM {$checkTimeData['from']} TO {$checkTimeData['target']} EXPIRE {$tmpExpireString}       \n\n";
//            }
//        }
//
//
//        // 2 day 6 hour 3 min 30 sec
//        $timeData1 = $util->convertSecToTime((2 * 24 * 60 * 60) + (6 * 60 * 60) + (3 * 60) + 30);
//        print_r($timeData1);
//        // 3 hour 40 min 10 sec
//        $timeData2 = $util->convertSecToTime((3 * 60 * 60) + (40 * 60) + 10);
//        print_r($timeData2);
//        // 30 min 10 sec
//        $timeData3 = $util->convertSecToTime((30 * 60) + 10);
//        print_r($timeData3);
//        // 14 day 3 min 30 sec
//        $timeData4 = $util->convertSecToTime((14 * 24 * 60 * 60) + (3 * 60) + 30);
//        print_r($timeData4);
//
//
//        $formatList = [
//            UtilComponent::kTimeScaleKeyDays => '%d日',
//            UtilComponent::kTimeScaleKeyHours => '%d時間',
//            UtilComponent::kTimeScaleKeyMins => '%d分',
//            UtilComponent::kTimeScaleKeySecs => '%d秒',
//        ];
//
//        $delim = " ";
//
//        $timeString1 = $util->getFormattedTimeString($formatList, $timeData1, $delim);
//        echo $timeString1."\n";
//        $timeString2 = $util->getFormattedTimeString($formatList, $timeData2, $delim);
//        echo $timeString2."\n";
//        $timeString3 = $util->getFormattedTimeString($formatList, $timeData3, $delim);
//        echo $timeString3."\n";
//        $timeString4 = $util->getFormattedTimeString($formatList, $timeData4, $delim);
//        echo $timeString4."\n";
//
//        return;

//        $tmpRarityJa = $scraper->getRarityKanaFromRarityShort($wordList['rarity_short']);
//
//        $ruleList = [
//            ['name','rarity_short','serial'],
//            ['name','rarity','box.name'],
//            ['name','rarity_ja','box.name'],
//        ];
//
//        if($tmpRarityJa){
//            $wordList['rarity_ja'] = $tmpRarityJa;
//            $ruleList = [
//                ['name','rarity_short','serial'],
//                ['name','rarity_ja','serial'],
//                ['name','rarity_short','box.name'],
//                ['name','rarity_ja','box.name'],
//            ];
//        }
//
//
//
//        $searchWordList = $util->getSearchWordList($wordList, $ruleList, " ");
//
//        $urlList = $scraper->makeSearchURLList($searchWordList);
//
//        $asinList = [];
//
//        foreach($urlList as $key => $url){
//            $asinList[$key] = [];
//            $tmpClawler = $scraper->getCrawlerFromUrl($url);
//            $asinList[$key][] = $tmpClawler->filter('ul[id=s-results-list-atf] li')->each(function (Crawler $node, $i){
//                 return $node->attr('data-asin');
//            });
//        }



//        $asin = 'B004543T82';
//        $asin = 'B00NOHUZOM';
//        $asin = 'B008J7QVVI';
//
//        $card = [
//            'card_id' => 7106662,
//            'category2_id' => 271,
//            'asin' => $asin,
//        ];
//
//        $url = $scraper->getItemPageUrl($asin);
//
//        $crawler = $scraper->getAmazonItemPageCrawler($url, $card);
//
//        $rank = $scraper->getRankFromItemPage($crawler);



        return 0;

    }

}
