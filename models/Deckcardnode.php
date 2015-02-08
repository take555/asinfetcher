<?php

namespace app\models;

use Yii;


/**
 * This is the model class for table "mercury_deckcardnode".
 *
 * @property integer $id
 * @property integer $deckcategory_id
 * @property integer $primary_card_id
 * @property integer $order_display
 * @property string $updated_at
 * @property string $created_at
 *
 * @property Deckcard[] $deckcards
 * @property Card $primaryCard
 * @property Deckcategory $deckcategory
 */
class Deckcardnode extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'mercury_deckcardnode';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['deckcategory_id', 'primary_card_id'], 'required'],
            [['deckcategory_id', 'primary_card_id', 'order_display'], 'integer'],
            [['updated_at', 'created_at'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'deckcategory_id' => 'Deckcategory ID',
            'primary_card_id' => 'Primary Card ID',
            'order_display' => 'Order Display',
            'updated_at' => 'Updated At',
            'created_at' => 'Created At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDeckcards()
    {
        return $this->hasMany(Deckcard::className(), ['deckcardnode_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPrimaryCard()
    {
        return $this->hasOne(Card::className(), ['id' => 'primary_card_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDeckcategory()
    {
        return $this->hasOne(Deckcategory::className(), ['id' => 'deckcategory_id']);
    }
}
