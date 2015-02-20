<?php

namespace app\models;

use Yii;


/**
 * This is the model class for table "mercury_amazonasin".
 *
 * @property integer $id
 * @property integer $card_id
 * @property integer $amazonitempage_id
 * @property string $asin
 * @property integer $rank
 * @property integer $new_itemcount
 * @property integer $used_itemcount
 * @property integer $credit
 * @property string $updated_at
 * @property string $created_at
 *
 * @property Card $card
 * @property Amazonitempage $amazonitempage
 */
class Amazonasin extends \yii\db\ActiveRecord
{

    const kAttrKeyId              = 'id';
    const kAttrKeyCardId          = 'card_id';
    const kAttrKeyAsin            = 'asin';
    const kAttrKeyUpdatedAt       = 'updated_at';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'mercury_amazonasin';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['card_id', 'amazonitempage_id', 'asin', 'credit', 'updated_at', 'created_at'], 'required'],
            [['card_id', 'amazonitempage_id', 'rank', 'new_itemcount', 'used_itemcount', 'credit'], 'integer'],
            [['updated_at', 'created_at'], 'safe'],
            [['asin'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'card_id' => 'Card ID',
            'amazonitempage_id' => 'Amazonitempage ID',
            'asin' => 'Asin',
            'rank' => 'Rank',
            'new_itemcount' => 'New Itemcount',
            'used_itemcount' => 'Used Itemcount',
            'credit' => 'Credit',
            'updated_at' => 'Updated At',
            'created_at' => 'Created At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCard()
    {
        return $this->hasOne(Card::className(), ['id' => 'card_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAmazonitempage()
    {
        return $this->hasOne(Amazonitempage::className(), ['id' => 'amazonitempage_id']);
    }

    public static function storeAsinInfoList(array $asinInfoList, array $card)
    {
        foreach ($asinInfoList as $asinInfo) {

            $asinInfoObj = Amazonasin::find()
                ->where(['asin' => $asinInfo['asin'], 'card_id' => $card[Card::kAttrKeyId]])
                ->one();

            if($asinInfoObj && $asinInfoObj instanceof Amazonasin){


                if(isset($asinInfo['rank'])){
                    $asinInfoObj->rank = $asinInfo['rank'];
                }

                if(isset($asinInfo['used'])){
                    $asinInfoObj->used_itemcount = $asinInfo['used'];
                }

                if(isset($asinInfo['new'])){
                    $asinInfoObj->new_itemcount = $asinInfo['new'];
                }

                $datetime = date('Y-m-d H:i:s');
                $asinInfoObj->updated_at = $datetime;
                if(!$asinInfoObj->update()){
                    $errorList = $asinInfoObj->getErrors();
                    $attributes = $asinInfoObj->getAttributes();
                    $modelName = $asinInfoObj->className();

                    $title = "\n############# SAVE ERROR: {$modelName} model ###############\n";
                    $modelParams = "MODEL PARAMS:\n".print_r($attributes)."\n";
                    $errorInfo = "ERROR INFO:\n".print_r($errorList)."\n";
                    $line = "############################################################\n\n";

                    $errMessage = $title.$modelParams.$errorInfo.$line;

                    Yii::error($errMessage, 'dbErrors');
                }
            } else {

                $amazonItemPage = Amazonitempage::find()
                    ->where(['asin' => $asinInfo['asin']])
                    ->one();

                $asinInfoObj = new Amazonasin();
                $asinInfoObj->card_id    = $card[Card::kAttrKeyId];
                $asinInfoObj->asin       = $asinInfo['asin'];

                if(isset($asinInfo['rank'])){
                    $asinInfoObj->rank = $asinInfo['rank'];
                }

                if(isset($asinInfo['used'])){
                    $asinInfoObj->used_itemcount = $asinInfo['used'];
                }

                if(isset($asinInfo['new'])){
                    $asinInfoObj->new_itemcount = $asinInfo['new'];
                }

                $asinInfoObj->credit     = $asinInfo['credit'];
                if($amazonItemPage instanceof Amazonitempage){
                    $asinInfoObj->amazonitempage_id = $amazonItemPage->id;
                }
                $datetime = date('Y-m-d H:i:s');
                $asinInfoObj->updated_at = $datetime;
                $asinInfoObj->created_at = $datetime;
                if(!$asinInfoObj->save()){

                    $errorList = $asinInfoObj->getErrors();
                    $attributes = $asinInfoObj->getAttributes();
                    $modelName = $asinInfoObj->className();

                    $title = "\n############# SAVE ERROR: {$modelName} model ###############\n";
                    $modelParams = "MODEL PARAMS:\n".print_r($attributes)."\n";
                    $errorInfo = "ERROR INFO:\n".print_r($errorList)."\n";
                    $line = "############################################################\n\n";

                    $errMessage = $title.$modelParams.$errorInfo.$line;

                    Yii::error($errMessage, 'dbErrors');



                }

            }
        }

    }

}
