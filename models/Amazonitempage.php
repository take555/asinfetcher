<?php

namespace app\models;

use Yii;


/**
 * This is the model class for table "mercury_amazonitempage".
 *
 * @property integer $id
 * @property integer $card_id
 * @property string $asin
 * @property string $url
 * @property string $html
 * @property string $updated_at
 * @property string $created_at
 *
 * @property Amazonasin[] $amazonasins
 * @property Card $card
 */
class Amazonitempage extends \yii\db\ActiveRecord
{

    const kAttrKeyCardId             = 'card_id';
    const kAttrKeyAsin               = 'asin';
    const kAttrKeyUrl                = 'url';
    const kAttrKeyHtml               = 'html';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'mercury_amazonitempage';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['card_id', 'asin', 'url', 'updated_at', 'created_at'], 'required'],
            [['card_id'], 'integer'],
            [['url', 'html'], 'string'],
            [['updated_at', 'created_at'], 'safe'],
            [['asin'], 'string', 'max' => 255],
            [['asin'], 'unique']
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
            'asin' => 'Asin',
            'url' => 'Url',
            'html' => 'Html',
            'updated_at' => 'Updated At',
            'created_at' => 'Created At',
        ];
    }


    public function beforeSave($insert)
    {
        if(parent::beforeSave($insert)){

            //$this->html = Yii::$app->util->removeHtmlComments($this->html);
            return true;
        } else {
            return false;
        }
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAmazonasins()
    {
        return $this->hasMany(Amazonasin::className(), ['amazonitempage_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCard()
    {
        return $this->hasOne(Card::className(), ['id' => 'card_id']);
    }
}
