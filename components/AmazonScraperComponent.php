<?php
/**
 * Created by PhpStorm.
 * User: youyaimac
 * Date: 2015/01/28
 * Time: 20:39
 */

namespace app\components;

use app\models\Amazonasin;
use app\models\Amazonitempage;
use app\models\Amazonsearchpage;
use app\models\Card;
use app\models\Category2;
use Symfony\Component\DomCrawler\Crawler;
use linslin\yii2\curl;
use Yii;
use app\components\ScraperComponent;
use yii\base\Component;
use Goutte\Client;
use yii\base\Exception;


class AmazonScraperComponent extends ScraperComponent
{

    public $fetcherList;//asin fetch server list

    public $baseUrl;

    public $category2Id;

    public $asinMaxCount;

    public $expireSearchResultPage;

    public $searchWordRuleSetList;

    public $category2;

    public $delim;

    public $rarityKanaList;

    public $rarityEnList;

    public $rarityList;




    const kAMAZON_ITEM_URL               = "http://www.amazon.co.jp//dp/";

    const kAMAZON_SEARCH_URL             = "http://www.amazon.co.jp/s/ref=nb_sb_noss?__mk_ja_JP=カタカナ&url=search-alias%3Daps&field-keywords=";

    const kAMAZON_SEARCH_URL_TAIL        = "&x=12&y=16";

    const kAMAZON_SEARCH_URL_TARGET      = "%s";

    const kAMAZON_RULE_KEY_CREDIT        = 'credit';

    const kAMAZON_RULE_KEY_WORD_LIST     = 'word_list';

    const kAMAZON_ASINRANK_LIST_KEY_ASIN = 'asin';

    const kAMAZON_ASINRANK_LIST_KEY_RANK = 'rank';

    const kAMAZON_SEARCHINFO_KEY_URL     = 'url';

    const kAMAZON_SEARCHINFO_KEY_KEYWORD = 'key_word';

    const kAMAZON_MESSAGE_SEARCH_RESULT_NOT_FOUND = '一致する商品はありませんでした。';

    public function init()
    {
        parent::init();
        $this->setCategory2();
        $this->setRarityList();
    }

    private function setRarityList()
    {
        $this->rarityKanaList = array(
            'Normal' => 'ノーマル',
            'Gold' => 'ゴールド',
            'Parallel' => 'パラレル',
            'Rare' => 'レア',
            'Secret' => 'シークレット',
            'Super' => 'スーパー',
            'Ultimate' => 'アルティメット',
            'Ultra' => 'ウルトラ',
            'Holographic' => 'ホログラフィック',
            'N-Parallel' => 'ノーマルパラレル',
            'N-Rare' => 'ノーマルレア',
            'Ul-Secret' => 'ウルトラシークレット',
            'Collectors-Rare' => 'コレクターズレア',
            'Millennium-Rare' => 'ミレニアムレア',
            'Gold-Secret-Rare' => 'ゴールドシークレットレア',
        );

        $this->rarityEnList = array(
            'Normal' => 'N',
            'Gold' => 'GR',
            'Parallel' => 'PRR',
            'Rare' => 'R',
            'Secret' => array('SI','SCR'),
            'Super' => 'SR',
            'Ultimate' => 'RR',
            'Ultra' => array('UR','UTR'),
            'Holographic' => 'HR',
            'N-Parallel' => 'NP',
            'N-Rare' => 'NR',
            'Ul-Secret' => 'SCR',
            'Collectors-Rare' => 'CR',
            'Millennium-Rare' => 'MR',
            'Gold-Secret-Rare' => 'GC',
        );


        $this->rarityList = array(
            'Normal',
            'Gold',
            'Parallel',
            'Rare',
            'Secret',
            'Super',
            'Ultimate',
            'Ultra',
            'Holographic',
            'N-Parallel',
            'N-Rare',
            'Ul-Secret',
            'Collectors-Rare',
            'Millennium-Rare',
            'Gold-Secret-Rare',
        );

    }


    private function setCategory2($category2Id = null)
    {
        if(!is_null($category2Id)){
            $this->category2Id = $category2Id;
        }


        $this->category2 = Category2::find()
            ->where(['id' => $this->category2Id])
            ->one();
    }


    public function execute($params)
    {

        $cardList = Card::getCardList($params);

        $cardListCount = count($cardList);
        $firstCardId = $cardList[0]['id'];
        $lastCardId  = $cardList[$cardListCount - 1]['id'];

        $tmpSearchWordRuleSetList = null;

        $count = 0;

        foreach($cardList as $card){
            $tmpSearchWordRuleSetList = null;
            $tmpRarityJa = $this->getRarityKanaFromRarityShort($card[Card::kAttrKeyRarityShort]);
            //英略レアリティ記号から日本語レアリティ表記を取得できない場合、検索ルールリストから日本語レアリティ表記を含むルールを削除する
            if($tmpRarityJa){
                $card[Card::kAttrKeyRarityJa] = $tmpRarityJa;
                $tmpSearchWordRuleSetList = $this->searchWordRuleSetList;
            } else {
                $tmpSearchWordRuleSetList = $this->filterSearchWordRuleList();
            }

            $sortedRuleSetsList = $this->getSearchWordRuleSetListCreditOrdered($tmpSearchWordRuleSetList);

            $asinSetsList = $this->getAsinList($sortedRuleSetsList, $card);

            $amazonItemPageParams = [
                Amazonitempage::kAttrKeyCardId => $card[Card::kAttrKeyId],
            ];
            $asinRankList = $this->getAsinRankList($asinSetsList, $amazonItemPageParams);

            $asinInfoList = $this->getAsinInfoListOrderByRank($asinRankList, $this->asinMaxCount);

            Amazonasin::storeAsinInfoList($asinInfoList, $card);

            $count++;

            $restCount = $cardListCount - $count;

            \Yii::info("###########################################################\n", 'processStatus');
            \Yii::info("PROCESSED:        {$count}\n", 'processStatus');
            \Yii::info("REST:             {$restCount}\n", 'processStatus');
            \Yii::info("CURRENT CARD_ID:  {$card['id']}\n", 'processStatus');
            \Yii::info("RANGE:            {$firstCardId} ~ {$lastCardId}\n", 'processStatus');
            \Yii::info("TOTAL:            {$cardListCount}\n", 'processStatus');
            \Yii::info("###########################################################\n", 'processStatus');



        }

        \Yii::info("###########################################################\n", 'processStatus');
        \Yii::info("############         PROCESS FINISHED           ###########\n", 'processStatus');
        \Yii::info("###########################################################\n", 'processStatus');
        \Yii::info("PROCESSED:        {$count}\n", 'processStatus');
        \Yii::info("REST:             {$restCount}\n", 'processStatus');
        \Yii::info("RANGE:            {$firstCardId} ~ {$lastCardId}\n", 'processStatus');
        \Yii::info("TOTAL:            {$cardListCount}\n", 'processStatus');
        \Yii::info("###########################################################\n", 'processStatus');


    }


    public function curlTest()
    {





        //$testUrl3 = "http://www.yahoo.co.jp";
        //$testUrl3 = "http://www.google.com";
        $testUrl3 = "http://localhost/?r=asin/test";


        $params1 = ['id' => 1, 'msg' => 'this is test'];
        $params2 = ['id' => 2, 'msg' => 'this is test'];
        $params3 = ['id' => 3, 'msg' => 'this is test'];
        $params4 = ['id' => 4, 'msg' => 'this is test'];
        $params5 = ['id' => 5, 'msg' => 'this is test'];



        $urlList = [
            ['url' => $testUrl3, 'params' => $params1],
            ['url' => $testUrl3, 'params' => $params2],
            ['url' => $testUrl3, 'params' => $params3],
            ['url' => $testUrl3, 'params' => $params4],
            ['url' => $testUrl3, 'params' => $params5],
        ];

        $params = ['test1' => 12345, 'test2' => 'this is test'];

        $multiHandler = curl_multi_init();

        $channels = [];

        $timeout = 6 * 60 * 60;// 6 hours

        foreach ($urlList as $key => $url) {

            echo "URL:".$url['url']."\n";

            $channels[$key] = curl_init();
            curl_setopt_array($channels[$key], [
                CURLOPT_URL => $url['url'],
                CURLOPT_TIMEOUT => $timeout,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $url['params'],

            ]);

            curl_multi_add_handle($multiHandler, $channels[$key]);
        }

        $active = null;

        do {
            $status = curl_multi_exec($multiHandler, $active);
        }
//        while ($status == CURLM_CALL_MULTI_PERFORM);
        while ($status == CURLM_OK && $active);


//        while ($active && $status == CURLM_OK){
//            if(curl_multi_select($multiHandler) != -1){
//                do {
//                    $status = curl_multi_exec($multiHandler, $active);
//
//                } while ($multiHandler == CURLM_CALL_MULTI_PERFORM);
//            }
//        }



        foreach($channels as $ch){
            echo "INCOMING RESULT\n";
            echo curl_multi_getcontent($ch)."\n\n";
            echo "DONE!!\n";
            curl_multi_remove_handle($multiHandler, $ch);
            curl_close($ch);
        }

        curl_multi_close($multiHandler);

        \Yii::info("###########################################", 'cli_infos');
        \Yii::info("#               DONE !!                   #", 'cli_infos');
        \Yii::info("###########################################", 'cli_infos');
    }




    public function assign($category2Id, $test = false)
    {

        $fetcherCount = count($this->fetcherList);

        if($fetcherCount === 0){
            $errMessage = "no fetcher server.you should add server to amazon_scraper config fetcherList\n";
            \Yii::error($errMessage, 'errors');
            echo $errMessage."\n";
            return 0;
        }

        \Yii::info("FETCHER COUNT:{$fetcherCount}\n", "cli_infos");


        $totalProcessCount = Card::find()
            ->where([Card::kAttrKeyCat2Id => intval($category2Id)])
            ->count();

        $baseProcessCount = floor($totalProcessCount / $fetcherCount);

        $postParamsList = [];

        foreach ($this->fetcherList as $key => $fetcherURL) {

            $params = [
                'id' => ($key + 1),
                Card::kParamsKeyCat2Id => $category2Id,
            ];

            if($test === true){
                $params[Card::kParamsKeyTest] = Card::kParamsValueTestTrue;
            }

            $params[Card::kParamsKeyLimit] = $baseProcessCount;

            if($key === ($fetcherCount - 1)){

                $params[Card::kParamsKeyLimit] = $baseProcessCount + $totalProcessCount % $fetcherCount;

            }

            $offset = $key * $baseProcessCount;
            $params[Card::kParamsKeyOffset] = $offset;


            $postParamsList[] = [
                self::kParamsKeyUrl    => $fetcherURL,
                self::kParamsKeyParams => $params,
            ];

        }

        $this->postMulti($postParamsList);

    }



    private function getAsinInfoListOrderByRank(array $asinRankList, $maxAsinCount)
    {

        $asinInfoList = [];

        foreach ($asinRankList as $credit => $asinRankSetList) {
            ksort($asinRankSetList);
            foreach($asinRankSetList as $rank => $asinRankSet){
                $asinInfoList[] = [
                    'credit' => $credit,
                    'asin'   => $asinRankSet['asin'],
                    'rank'   => $asinRankSet['rank']
                ];
            }
        }

        return $asinInfoList;

    }



    public function getAsinRankList($asinSetsList, $params = [])
    {
        $asinRankSetsList = [];

        foreach ($asinSetsList as $credit =>  $asinSets) {

            $tmpAsinRankSets = [];

            foreach($asinSets as $asin){
                $itemPageUrl = $this->getItemPageUrl($asin);
                $params[Amazonitempage::kAttrKeyAsin] = $asin;
                $crawler = $this->getAmazonItemPageCrawler($itemPageUrl, $params);
                $rank = $this->getRankFromItemPage($crawler);
                if($rank){
                    $tmpAsinRankSets[intval($rank)] = [
                        self::kAMAZON_ASINRANK_LIST_KEY_RANK => $rank,
                        self::kAMAZON_ASINRANK_LIST_KEY_ASIN => $asin,
                    ];
                }
            }

            ksort($tmpAsinRankSets);
            if(count($tmpAsinRankSets) > 0){
                $asinRankSetsList[$credit] = $tmpAsinRankSets;
            }
        }

        return $asinRankSetsList;
    }

    public function getRankFromItemPage(\Symfony\Component\DomCrawler\Crawler $clawler)
    {

        $rankList = $clawler->filter('#SalesRank li')->each(function (Crawler $node ,$i){
            $pattern = "/([0-9]+)位.+(ホビー).+(トレーディングカード).+(シングルカード)/s";
            $tmpText = trim($node->text());
            if(preg_match_all($pattern, $tmpText, $matches)){
                if(count($matches) === 5 && intval($matches[1][0]) > 0){
                    return intval($matches[1][0]);
                }
            }
            return false;
        });

        foreach ($rankList as $value) {
            if(!empty($value)){
                return $value;
            }
        }

        return false;
    }


    public function getItemPageUrl($asin)
    {
        return self::kAMAZON_ITEM_URL.$asin;
    }


    private function getAsinList($ruleSetsList, $card)
    {
        $asinList = [];

        foreach ($ruleSetsList as $credit => $ruleSets) {


            if(!is_array($ruleSets)){
                continue;
            }

            $searchWordList = $this->util->getSearchWordList($card, $ruleSets, $this->delim);
            $searchInfoList = $this->makeSearchKeyWordURLList($searchWordList);
            //searchInfoList [['url' => 'http://xxx', 'key_word' => 'xxx'],[~],[~]]
            $tmpAsinList = $this->processAmazonPageForAsin($searchInfoList, $card);
            if(is_array($tmpAsinList)){
                $asinList[$credit] = $tmpAsinList;
            }
        }

        //重複ASINの削除

        $asinList = $this->removeDuplicateAsinFromAsinList($asinList);

        return $asinList;
    }

    /**
     * 以下の構造のASINリストで最初に登場した以外の重複したASINを削除して、再構築したリストを返す
     *
     * before
     * [
     *      credit => [abc, def, efg, his],
     *      credit => [mnl, abc, opq, efg],
     *      credit => [xyz, cdf, mnl, efg],
     *      credit => [abc, mnl],
     *      credit => [efg, abc, kmj, rfv],
     * ]
     *
     * after
     * [
     *      credit => [abc, def, efg, his],
     *      credit => [mnl, opq],
     *      credit => [xyz, cdf],
     *      credit => [kmj, rfv],
     * ]
     *
     * @param array $asinList
     * @return array
     */



    private function removeDuplicateAsinFromAsinList(array $asinList)
    {
        $asinListHeadIndexKeyList = [];

        $removeTargetAsinIndexKeyList = [];

        $sequentialAsinList = [];

        $indexCount = 0;

        foreach ($asinList as $credit => $asinSets) {
            $asinListHeadIndexKeyList[$indexCount] = $credit;
            foreach($asinSets as $asin){
                $sequentialAsinList[] = $asin;
                $indexCount++;
            }
        }

        //重複チェック、削除フラグセット
        foreach($sequentialAsinList as $indexKey => $asin){
            if(isset($removeTargetAsinIndexKeyList[$indexKey])){
                continue;
            }

            foreach($sequentialAsinList as $ik => $as){
                if($indexKey === $ik || isset($removeTargetAsinIndexKeyList[$ik])){
                    continue;
                }
                if($asin === $as){
                    $removeTargetAsinIndexKeyList[$ik] = true;
                }
            }
        }

        //リスト再構築
        $restructedAsinList = [];

        $tmpAsinList = [];
        $indexKeyStore = null;


        foreach ($sequentialAsinList as $indexKey => $asin) {
            if(isset($asinListHeadIndexKeyList[$indexKey])){
                $tmpAsinList = ['credit' => $asinListHeadIndexKeyList[$indexKey], 'list' => []];
                $tailIndexKey = $this->getTailIndexKeySeqAsinList($indexKey, $sequentialAsinList, $asinListHeadIndexKeyList);
            }

            if(!isset($removeTargetAsinIndexKeyList[$indexKey])){
                $tmpAsinList['list'][] = $asin;
            }

            if($tailIndexKey === $indexKey && count($tmpAsinList['list']) > 0){
                $restructedAsinList[$tmpAsinList['credit']] = $tmpAsinList['list'];
            }
        }

        return $restructedAsinList;

    }

    private function getTailIndexKeySeqAsinList($currentHeadIndexKey, $sequentialAsinList, $asinListHeadIndexKeyList)
    {
        $nextHeadIndexKey = $this->util->getForwardNeighborArrayKey($currentHeadIndexKey, $asinListHeadIndexKeyList);
        if($nextHeadIndexKey === false){
            end($sequentialAsinList);
            $tailIndexKey = key($sequentialAsinList);
        } else {
            $tailIndexKey = $this->util->getBackwardNeighborArrayKey($nextHeadIndexKey, $sequentialAsinList);
        }
        return $tailIndexKey;
    }


    private function processAmazonPageForAsin(array $searchInfoList,array $card)
    {

        $asinKeyStoreList = [];

        $tmpAsinList = [];

        $asinList = false;

        foreach($searchInfoList as $key => $searchInfo){
            $amazonSearchPageParams = [
                Amazonsearchpage::kAttrKeyCardId      => $card[Card::kAttrKeyId],
                Amazonsearchpage::kAttrKeyCategory2Id => $card[Card::kAttrKeyCat2Id],
                Amazonsearchpage::kAttrKeyUrl         => $searchInfo[self::kAMAZON_SEARCHINFO_KEY_URL],
                Amazonsearchpage::kAttrKeySearchKey   => $searchInfo[self::kAMAZON_SEARCHINFO_KEY_KEYWORD],
            ];
            $tmpCrawler = $this->getAmazonSearchPageCrawler($searchInfo['url'], $amazonSearchPageParams);

            try {

                $noResultFound = $tmpCrawler->filter('h1#noResultsTitle')->first()->text();

            } catch (\InvalidArgumentException $e) {
                $tmpAsinList[] = $tmpCrawler->filter('ul[id=s-results-list-atf] li')->each(function (Crawler $node, $i){
                    $tmpAsin = $node->attr('data-asin');
                    if($tmpAsin){
                        return $tmpAsin;
                    }
                });
            }
        }

        if(count($tmpAsinList) > 0 && count($tmpAsinList[0]) > 0){
            foreach ($tmpAsinList as $asinSets) {
                foreach($asinSets as $asinVal){
                    $asinKeyStoreList[$asinVal] = true;
                }
            }

            $asinList = array_keys($asinKeyStoreList);

        }

        return $asinList;
    }


    /**
     * 同じcreditの重みをもつルールセットをまとめ、creditが重い順にソートしたリストを返す
     * @param $searchWordRuleSetList
     * @return array
     * 返されるルールセットリストの形式は以下のような形になる
     * [
     *      100(credit) => [
     *          [rule],
     *          [rule],
     *      ],
     *      90 => [
     *          [rule],
     *          [rule],
     *          [rule],
     *      ],
     *      60 => [
     *          [rule],
     *      ],
     *
     * ]
     *
     *
     *
     */


    private function getSearchWordRuleSetListCreditOrdered($searchWordRuleSetList)
    {
        $creditKeyList = [];
        foreach ($searchWordRuleSetList as $searchWordRuleSet) {
            if(isset($searchWordRuleSet[self::kAMAZON_RULE_KEY_CREDIT])){
                if(isset($creditKeyList[$searchWordRuleSet[self::kAMAZON_RULE_KEY_CREDIT]])){
                    $creditKeyList[$searchWordRuleSet[self::kAMAZON_RULE_KEY_CREDIT]][] = $searchWordRuleSet[self::kAMAZON_RULE_KEY_WORD_LIST];
                } else {
                    $creditKeyList[$searchWordRuleSet[self::kAMAZON_RULE_KEY_CREDIT]] = [];
                    $creditKeyList[$searchWordRuleSet[self::kAMAZON_RULE_KEY_CREDIT]][] = $searchWordRuleSet[self::kAMAZON_RULE_KEY_WORD_LIST];
                }
            }
        }

//        $creditDescKeyList = array_keys($creditKeyList);
//        arsort($creditDescKeyList);
//        $sortedSearchWordRuleList = [];
//        foreach ($creditDescKeyList as $creditKey) {
//            if(isset($creditKeyList[$creditKey])){
//                $sortedSearchWordRuleList[] = $creditKeyList[$creditKey];
//            }
//        }

        krsort($creditKeyList);

        return $creditKeyList;

    }


    /**
     * 検索ワードルールリストから特定のワード要素($filterKey)を含むルールを削除したリストを取得する
     * @param string $filterKey 削除したいルールが含むワード要素
     * @return array
     */

    private function filterSearchWordRuleList($filterKey = Card::kAttrKeyRarityJa)
    {
        $filteredSearchWordRuleSetList = [];

        foreach ($this->searchWordRuleSetList as $searchWordRuleSet) {
            if(isset($searchWordRuleSet[self::kAMAZON_RULE_KEY_WORD_LIST]) && is_array($searchWordRuleSet[self::kAMAZON_RULE_KEY_WORD_LIST])){
                if(!in_array($filterKey, $searchWordRuleSet[self::kAMAZON_RULE_KEY_WORD_LIST])){
                    $filteredSearchWordRuleSetList[] =  $searchWordRuleSet;
                }
            }
        }

        return $filteredSearchWordRuleSetList;
    }



    /**
     * レアリティ英略字からレアリティ英表記を取得
     * @param $rarityShort ex: N , U, UR
     * @return bool|string レアリティフル英表記文字列
     */


    public function getRarityEnFromRarityShort($rarityShort)
    {
        foreach ($this->rarityEnList as $rarityEn => $rarityShortTmp) {
            if(is_array($rarityShortTmp)){
                foreach ($rarityShortTmp as $rst) {
                    if($rarityShort === $rst){
                        return $rarityEn;
                    }
                }

            } else {
                if($rarityShort === $rarityShortTmp){
                    return $rarityEn;
                }
            }
        }
        return false;
    }

    /**
     * レアリティ英略字からレアリティ日本語表記を取得
     * @param $rarityShort ex: N , U, UR
     * @return bool|string レアリティフル日本語表記文字列
     */
    public function getRarityKanaFromRarityShort($rarityShort)
    {
        $rarityEn = $this->getRarityEnFromRarityShort($rarityShort);
        if(!$rarityEn){
            return false;
        }

        if(!isset($this->rarityKanaList[$rarityEn])){
            return false;
        }

        return $this->rarityKanaList[$rarityEn];
    }


    /**
     * 検索キーワードのリストからAmazonの検索結果ページのurlのリストを生成
     * @param $searchWordList 検索キーワード
     * @param bool $urlEncode 検索キーワードをURLエンコードするかどうか、デフォルトはしない
     * @return array AMAZON検索結果ページURLのリスト
     */

    public function makeSearchURLList($searchWordList, $urlEncode = false)
    {
        $searchURLList = [];
        foreach ($searchWordList as $searchWord) {
            $searchURLList[] = $this->makeSearchURL($searchWord, $urlEncode);
        }

        return $searchURLList;

    }

    /**
     * 検索キーワードのリストからAmazonの検索結果ページのurlとキーワードのデータのリストを生成
     * @param $searchWordList 検索キーワード
     * @param bool $urlEncode 検索キーワードをURLエンコードするかどうか、デフォルトはしない
     * @return array AMAZON検索結果ページURLのリスト
     */

    public function makeSearchKeyWordURLList($searchWordList, $urlEncode = false)
    {
        $searchURLList = [];
        foreach ($searchWordList as $searchWord) {

            $tmpURL = $this->makeSearchURL($searchWord, $urlEncode);

            $searchURLList[] = [
                self::kAMAZON_SEARCHINFO_KEY_URL => $tmpURL,
                self::kAMAZON_SEARCHINFO_KEY_KEYWORD => $searchWord,
            ];
        }

        return $searchURLList;

    }

    /**
     * 検索キーワードからAmazonの検索結果ページのurlを生成
     * @param $searchWord 検索キーワード
     * @param bool $urlEncode 検索キーワードをURLエンコードするかどうか、デフォルトはしない
     * @return string AMAZON検索結果ページURL
     */

    public function makeSearchURL($searchWord,$urlEncode = false){
        $urlLeft  = preg_replace("|%|","%%", self::kAMAZON_SEARCH_URL);
        $urlRight = preg_replace("|%|","%%", self::kAMAZON_SEARCH_URL_TAIL);

        $format      = $urlLeft.self::kAMAZON_SEARCH_URL_TARGET.$urlRight;

        if($urlEncode){
            $searchWord = urlencode($searchWord);
        }

        $url = sprintf($format, $searchWord);

        return $url;

    }

    public function getHTML(\Symfony\Component\DomCrawler\Crawler $clawler)
    {

        $html = '';
        foreach($clawler as $domElm){
            $html .= $domElm->ownerDocument->saveHTML($domElm);
        }

        return $html;
    }


    public function getAmazonSearchPageCrawler($url, $params)
    {
        return $this->getCrawler($url, "app\models\Amazonsearchpage", $params);
    }

    public function getAmazonItemPageCrawler($url, $params)
    {
        return $this->getCrawler($url, "app\models\Amazonitempage", $params);
    }


    /**
     * 指定urlのページのdomクローラを取得する
     *
     * @param $url
     * @param $modelName
     * @param array $modelParams
     * @return Crawler
     */


    public function getCrawler($url, $modelName, $modelParams = [])
    {

        $crawler = null;

        $searchPage = $modelName::find()
            ->where(['url' => $url])
            ->one();

        if($searchPage && $searchPage instanceof $modelName){
        //DBに検索結果ページのデータあり
            if($this->util->isPassedCurrentDateTime($searchPage->updated_at, $this->expireSearchResultPage)){
            //expire経過 ネットから取得
                $crawler = $this->getCrawlerFromUrl($url);
                $html = $this->getHTML($crawler);
                $this->util->updateModelData($searchPage, ['html' => $html]);

            } else {
                $html = $searchPage->html;
                $crawler = new Crawler($html);
            }
        } else {
        //DBに検索結果ページのデータなし
            $crawler = $this->getCrawlerFromUrl($url);
            $html = $this->getHTML($crawler);

            $modelParams['url']  = $url;
            $modelParams['html'] = $html;
            $this->util->saveModelData($modelName, $modelParams);

        }

        return $crawler;

    }

}