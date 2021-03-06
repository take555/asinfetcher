<?php

namespace app\models;

use Yii;


/**
 * This is the model class for table "mercury_series".
 *
 * @property integer $id
 * @property string $name
 * @property integer $category2_id
 * @property integer $order_display
 * @property integer $active
 * @property string $created_at
 *
 * @property Card[] $cards
 * @property Logaction[] $logactions
 */
class Series extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'mercury_series';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'category2_id'], 'required'],
            [['category2_id', 'order_display', 'active'], 'integer'],
            [['name'], 'string', 'max' => 100],
            [['created_at'], 'string', 'max' => 45]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'category2_id' => 'Category2 ID',
            'order_display' => 'Order Display',
            'active' => 'Active',
            'created_at' => 'Created At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCards()
    {
        return $this->hasMany(Card::className(), ['series_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLogactions()
    {
        return $this->hasMany(Logaction::className(), ['series_id' => 'id']);
    }
}
