<?php

namespace app\models;

use Yii;


/**
 * This is the model class for table "mercury_logaction".
 *
 * @property integer $id
 * @property integer $client_id
 * @property integer $action_type
 * @property string $raw_data
 * @property integer $category2_id
 * @property string $name
 * @property integer $box_id
 * @property integer $series_id
 * @property integer $type_id
 * @property integer $race_id
 * @property integer $attribute_id
 * @property string $serial
 * @property integer $card_id
 * @property string $created_at
 *
 * @property Attribute $attribute
 * @property Box $box
 * @property Card $card
 * @property Category2 $category2
 * @property Clients $client
 * @property Race $race
 * @property Series $series
 * @property Type $type
 */
class Logaction extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'mercury_logaction';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['client_id', 'action_type', 'created_at'], 'required'],
            [['client_id', 'action_type', 'category2_id', 'box_id', 'series_id', 'type_id', 'race_id', 'attribute_id', 'card_id'], 'integer'],
            [['raw_data'], 'string'],
            [['created_at'], 'safe'],
            [['name', 'serial'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'client_id' => 'Client ID',
            'action_type' => 'Action Type',
            'raw_data' => 'Raw Data',
            'category2_id' => 'Category2 ID',
            'name' => 'Name',
            'box_id' => 'Box ID',
            'series_id' => 'Series ID',
            'type_id' => 'Type ID',
            'race_id' => 'Race ID',
            'attribute_id' => 'Attribute ID',
            'serial' => 'Serial',
            'card_id' => 'Card ID',
            'created_at' => 'Created At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAttributeObj()
    {
        return $this->hasOne(Attribute::className(), ['id' => 'attribute_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBox()
    {
        return $this->hasOne(Box::className(), ['id' => 'box_id']);
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
    public function getCategory2()
    {
        return $this->hasOne(Category2::className(), ['id' => 'category2_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClient()
    {
        return $this->hasOne(Clients::className(), ['id' => 'client_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRace()
    {
        return $this->hasOne(Race::className(), ['id' => 'race_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSeries()
    {
        return $this->hasOne(Series::className(), ['id' => 'series_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getType()
    {
        return $this->hasOne(Type::className(), ['id' => 'type_id']);
    }
}
