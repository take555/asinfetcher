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
use yii\base\Exception;
use yii\base\InvalidConfigException;

class UtilComponent extends Component
{


    const kTimeScaleKeyDays    = 'days';

    const kTimeScaleKeyHours   = 'hours';

    const kTimeScaleKeyMins    = 'mins';

    const kTimeScaleKeySecs    = 'secs';



    /**
     *
     * 連想配列にリストで指定したキーが存在するかチェックし
     * 結果を返す
     *
     * @param $checkTargetKeyList 存在確認対象の連想配列キーのリスト ex ['key1', 'key2']
     * @param $assocArray チェック対象の連想配列 ex ['key1' => x, 'key3' => ['key4' => x]]
     * @return array チェック結果のリスト
     */
    public function checkAssocArrayKeys($checkTargetKeyList, $assocArray)
    {

        //チェック済み連想配列キー、結果リスト
        $checkedArrayKeyList = [];

        $assocArrayKeyList = array_keys($assocArray);


        foreach($checkTargetKeyList as $key){
            if(in_array($key, $assocArrayKeyList)){
                $checkedArrayKeyList[$key] = true;
            } else {
                $checkedArrayKeyList[$key] = false;
            }
        }

        return $checkedArrayKeyList;

    }

    /**
     * 連想配列にリストで指定したキーが存在するかどうか
     * 一つでも存在しないキーがあればfalse
     *
     * @param $checkTargetKeyList 存在確認対象の連想配列キーのリスト ex ['key1', 'key2']
     * @param $assocArray チェック対象の連想配列 ex ['key1' => x, 'key3' => ['key4' => x]]
     * @return bool
     */


    public function isAssocArrayKeysExist($checkTargetKeyList, $assocArray)
    {
        $checkedArrayKeyList = $this->checkAssocArrayKeys($checkTargetKeyList, $assocArray);

        if(in_array(true, $checkedArrayKeyList, true)){
            return true;
        }

        return false;

    }

    /**
     * 連想配列にリストで指定したキーが存在するかどうかチェックした内容を返す
     *
     * @param $checkTargetKeyList 存在確認対象の連想配列キーのリスト ex ['key1', 'key2']
     * @param $assocArray チェック対象の連想配列 ex ['key1' => x, 'key3' => ['key4' => x]]
     * @return array 'result':結果 'list':各キーのチェック結果
     */

    public function getAccocKeysInfo($checkTargetKeyList, $assocArray)
    {
        $info = [];

        $info['list'] = $this->checkAssocArrayKeys($checkTargetKeyList, $assocArray);

        if(in_array(true, $info['list'], true)){
            $info['result'] = true;
        } else {
            $info['result'] = false;
        }

        return $info;

    }


    /**
     *
     * 連想配列にリストで指定したキーが存在し、かつ値がある要素のリストを返す
     * @param $checkTargetKeyList 存在確認対象の連想配列キーのリスト ex ['key1', 'key2']
     * @param $assocArray チェック対象の連想配列 ex ['key1' => x, 'key3' => ['key4' => x]]
     * @return array
     */


    public function getValuedAssocElmList($checkTargetKeyList, $assocArray)
    {
        $checkedAssocKeyList = $this->checkAssocArrayKeys($checkTargetKeyList, $assocArray);

        $valuedAssocElmList = [];

        foreach($checkedAssocKeyList as $key => $val){
            if($val === true && !empty($assocArray[$key])){
                $valuedAssocElmList[$key] = $assocArray[$key];
            }
        }

        return $valuedAssocElmList;

    }


    /**
     * 与えられた文字列のリストから、並び順を指定して検索用文字列を作成する
     *
     * @param array $wordAssocList 検索文字列作成用文字列連想配列 ex: ['key1' => 'word1','key2' => 'word2', 'key3' => 'word3']
     * @param array $assocKeyRuleList 検索文字列の生成ルール ex: ['key1', 'key2', 'key3']
     * @param $delim デリミタ
     * @return string 検索文字列
     */

    public function getSearchWord(array $wordAssocList,array $assocKeyRuleList, $delim)
    {
        $searchWordList = [];

        foreach($assocKeyRuleList as $key){
            $searchWordList[] = $this->getValueFromMultiDimensionArray($key, $wordAssocList);
        }

        return join($delim, $searchWordList);
    }

    /**
     * 与えられた文字列のリストから、複数の並び順を指定して検索用文字列のリストを作成する
     *
     * @param array $wordAssocList 検索文字列作成用文字列連想配列 ex: ['key1' => 'word1','key2' => 'word2', 'key3' => 'word3']
     * @param array $assocKeyRuleSetList 検索文字列の生成ルールのリスト ex: [['key1', 'key2'],['key2', 'key3']]
     * @param $delim デリミタ
     * @return array 検索文字列のリスト
     */

    public function getSearchWordList(array $wordAssocList, array $assocKeyRuleSetList, $delim)
    {
        $searchWordList = [];
        foreach($assocKeyRuleSetList as $assocKeyRuleSet){
            $searchWordList[] = $this->getSearchWord($wordAssocList, $assocKeyRuleSet, $delim);
        }
        return $searchWordList;
    }


    /**
     * 多次元配列から指定したキーで要素の値を取得する
     * @param $propertyElm キー（次元が増えるごとに"."でキーとなる文字列を追加）ex: "a.b.c" = $array['a']['b']['c']
     * @param $assocArray 対象の多次元配列
     * @return mixed
     */


    public function getValueFromMultiDimensionArray($propertyElm, $assocArray)
    {

        if(!is_array($assocArray)){
            return $assocArray;
        }

        $propertyList = explode('.', $propertyElm);
        if(count($propertyList) > 1){
            if(isset($assocArray[$propertyList[0]])){
                $assocArray = $assocArray[$propertyList[0]];
                array_shift($propertyList);
                $propertyElm = join('.', $propertyList);
                return $this->getValueFromMultiDimensionArray($propertyElm, $assocArray);
            }
            return $assocArray;
        }

        if(isset($assocArray[$propertyList[0]])){
            return $assocArray[$propertyList[0]];
        }

        if(is_null($assocArray[$propertyList[0]])){
            return "";
        }

        return $assocArray;
    }

    /**
     * $targetDatetimeから現在に至るまで時間が$expireに指定した時間を経過したか(true)否か(false)
     * @param $startingDatetime (Y-m-d H:i:s)時間経過の起算点となる時間
     * @param $expire 経過時間
     * @return bool 経過:true 未経過:false
     */

    public function isPassedCurrentDateTime($startingDatetime, $expire)
    {
        return $this->isPassedDateTime($startingDatetime, date('Y-m-d H:i:s'), $expire);
    }

    /**
     * $startingDatetimeから$targetDatetimeに至る経過時間が$expireに指定した時間を経過したか(true)否か(false)
     * @param $startingDatetime (Y-m-d H:i:s)時間経過の起算点となる時間
     * @param $targetDatetime (Y-m-d H:i:s) $startDatetimeから$expire時間を経過したかどうか比較対象となる時間
     * @param $expire 経過時間
     * @return bool 経過:true 未経過:false
     */


    public function isPassedDateTime($startingDatetime, $targetDatetime, $expire)
    {
        $startingTimestamp = strtotime($startingDatetime);
        $targetTimestamp = strtotime($targetDatetime);
        $diffTimestamp = $targetTimestamp - $startingTimestamp;

        if($diffTimestamp > $expire){
            $passedTime = $diffTimestamp - $expire;

            //echo $this->getDefaultTimeStringFromSec($passedTime)." PASSED\n";

            return true;
        }

        $restTime = $expire - $diffTimestamp;

        //echo $this->getDefaultTimeStringFromSec($restTime)." REST\n";

        return false;
    }

    public function getDefaultTimeStringFromSec($seconds)
    {
        $formatList = [
            UtilComponent::kTimeScaleKeyDays => '%d Days',
            UtilComponent::kTimeScaleKeyHours => '%d Hours',
            UtilComponent::kTimeScaleKeyMins => '%d Mins',
            UtilComponent::kTimeScaleKeySecs => '%d Secs',
        ];

        $delim = " ";

        $timeData = $this->convertSecToTime($seconds);

        $timeString = $this->getFormattedTimeString($formatList, $timeData, $delim);

        return $timeString;
    }

    /**
     * 秒単位の時間を日時分秒に変換した連想配列で返す
     *
     * @param $seconds 変換対象秒数
     * @return array ['days' => x, 'hours' => x 'mins' => x, 'secs' => x]
     */

    public function convertSecToTime($seconds)
    {
        $daySecScale   = 24 * 60 * 60;
        $hourSecScale  = 60 * 60;
        $minSecScale   = 60;

        $days  = floor($seconds / $daySecScale);

        $daysSec = $days * $daySecScale;

        $hours = floor(($seconds - $daysSec) / $hourSecScale);

        $hoursSec = $hours * $hourSecScale;

        $mins  = floor(($seconds - $daysSec - $hoursSec) / $minSecScale);

        $minsSec = $mins * $minSecScale;

        $secs = $seconds - $daysSec - $hoursSec - $minsSec;

        $timeData = [
            self::kTimeScaleKeyDays  => $days,
            self::kTimeScaleKeyHours => $hours,
            self::kTimeScaleKeyMins  => $mins,
            self::kTimeScaleKeySecs  => $secs
        ];

        return $timeData;

    }

    /**
     * Util::convertSecToTimeで得た時間データから各日時分秒のフォーマットをリストでして
     * 指定したデリミタで１つの文字列を作る
     * 例：
     * [
     *  'days' => '%d日',
     *  'hours' => '%d時間',
     *  'mins' => '%d分',
     *  'secs' => '%d秒',
     * ]
     *
     * デリミタ： " "
     *
     * 出力： x日 x時間 x分 x秒
     *
     * @param array $timeFormatList
     * @param array $timeData
     * @param string $delim
     * @return string
     */



    public function getFormattedTimeString(array $timeFormatList, array $timeData, $delim = " ")
    {
        $timeScaleKeyList = [
            self::kTimeScaleKeyDays,
            self::kTimeScaleKeyHours,
            self::kTimeScaleKeyMins,
            self::kTimeScaleKeySecs
        ];

        $filteredTimeFormatList = $this->getValuedAssocElmList($timeScaleKeyList, $timeFormatList);

        $filteredTimeData = $this->getValuedAssocElmList($timeScaleKeyList, $timeData);


        $formattedTimeStringList = [];

        foreach($filteredTimeFormatList as $timeScaleKey => $timeFormat){
            if(isset($filteredTimeData[$timeScaleKey])){
                $formattedTimeStringList[] = sprintf($timeFormat, $filteredTimeData[$timeScaleKey]);
            }
        }

        $formattedTimeString = join($delim, $formattedTimeStringList);

        return $formattedTimeString;

    }

    /**
     * 指定したデータを更新する
     * @param string $modelObj モデルのインスタンス
     * @param array $params モデルのプロパティをキーとするデータの連想配列
     * @return bool 保存成功:true 保存失敗:false
     */
    public function updateModelData($modelObj, array $params)
    {

        foreach ($params as $attr => $val) {
            if($modelObj->className()->hasAttribute($attr)){
                $modelObj->$attr = $val;
            }
        }
        $modelObj->updated_at = date('Y-m-d H:i:s');

        if(!$modelObj->update()){
            return false;
        }

        return true;

    }


    /**
     * HTMLのコメントを削除する
     * @param $html
     * @return mixed
     */

    public function removeHtmlComments($html)
    {
        return preg_replace( '/<!--(.|\s)*?-->/' , '' , $html);
    }


    /**
     * 指定したモデルのデータを保存する
     * @param string $modelName モデル名
     * @param array $params モデルのプロパティをキーとするデータの連想配列
     * @return bool 保存成功:true 保存失敗:false
     */

    public function saveModelData($modelName, array $params)
    {
        $modelObj = new $modelName();

        foreach ($params as $attr => $val) {
            if($modelObj->hasAttribute($attr)){
                $modelObj->$attr = $val;
            }
        }

        $datetime = date("Y-m-d H:i:s");
        $modelObj->updated_at = $datetime;
        $modelObj->created_at = $datetime;

        try {

            if(!$modelObj->save()){

                $errorList = $modelObj->getErrors();
                $attributes = $modelObj->getAttributes();

                $title = "\n############# SAVE ERROR: {$modelName} model ###############\n";
                $modelParams = "MODEL PARAMS:\n".print_r($attributes)."\n";
                $errorInfo = "ERROR INFO:\n".print_r($errorList)."\n";
                $line = "############################################################\n\n";

                $errMessage = $title.$modelParams.$errorInfo.$line;

                Yii::error($errMessage, 'dbErrors');


                return false;
            }
        } catch (Exception $e){

            $attributes = $modelObj->getAttributes();
            $title = "\n############# DB ERROR: {$modelName} model ###############\n";
            $modelParams = "MODEL PARAMS:\n".print_r($attributes)."\n";
            $er = "ERROR MSG:".print_r($e->getMessage())."\n";
            $ec = "ERROR CODE:".print_r($e->getCode())."\n";
            $el = "ERROR LINE:".print_r($e->getLine())."\n";
            $line = "############################################################\n\n";

            $errMessage = $title.$modelParams.$er.$ec.$el.$line;

            Yii::error($errMessage, 'dbErrors');
            return fasle;
        }



        return true;

    }

    /**
     * 連想配列の指定したキー（$baseKey）の要素から前方に隣の要素のキーを返す、ない場合はfalse
     * @param $baseKey 基点となる要素のキー
     * @param $targetList 対象の連勝配列
     * @return bool|int|string
     */

    public function getForwardNeighborArrayKey($baseKey, $targetList)
    {

        $reachBaseKey = false;
        foreach($targetList as $key => $val){
            if($reachBaseKey === true){
                return $key;
            }
            if($baseKey === $key){
                $reachBaseKey = true;
            }
        }

        return false;
    }

    /**
     * 連想配列の指定したキー（$baseKey）の要素から後方に隣の要素のキーを返す、ない場合はfalse
     * @param $baseKey 基点となる要素のキー
     * @param $targetList 対象の連勝配列
     * @return bool|int|string
     */
    public function getBackwardNeighborArrayKey($baseKey, $targetList)
    {
        $reverseList = array_reverse($targetList, true);
        $reachBaseKey = false;
        foreach($reverseList as $key => $val){
            if($reachBaseKey === true){
                return $key;
            }
            if($baseKey === $key){
                $reachBaseKey = true;
            }
        }

        return false;
    }


}