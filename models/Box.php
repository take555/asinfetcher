<?php

namespace app\models;

use Yii;


/**
 * This is the model class for table "mercury_box".
 *
 * @property integer $id
 * @property string $name
 * @property string $serial
 * @property integer $category2_id
 * @property integer $series_id
 * @property integer $order_display
 * @property string $publish
 * @property string $publish_yomi
 * @property integer $active
 * @property string $updated_at
 * @property string $created_at
 *
 * @property Boxcontent[] $boxcontents
 * @property Boxtocontainer[] $boxtocontainers
 * @property Card[] $cards
 * @property Logaction[] $logactions
 */
class Box extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'mercury_box';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'category2_id'], 'required'],
            [['category2_id', 'series_id', 'order_display', 'active'], 'integer'],
            [['publish', 'updated_at', 'created_at'], 'safe'],
            [['name'], 'string', 'max' => 100],
            [['serial', 'publish_yomi'], 'string', 'max' => 45]
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
            'serial' => 'Serial',
            'category2_id' => 'Category2 ID',
            'series_id' => 'Series ID',
            'order_display' => 'Order Display',
            'publish' => 'Publish',
            'publish_yomi' => 'Publish Yomi',
            'active' => 'Active',
            'updated_at' => 'Updated At',
            'created_at' => 'Created At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBoxcontents()
    {
        return $this->hasMany(Boxcontent::className(), ['box_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBoxtocontainers()
    {
        return $this->hasMany(Boxtocontainer::className(), ['box_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCards()
    {
        return $this->hasMany(Card::className(), ['box_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLogactions()
    {
        return $this->hasMany(Logaction::className(), ['box_id' => 'id']);
    }
}
