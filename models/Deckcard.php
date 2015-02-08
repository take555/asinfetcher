<?php

namespace app\models;

use Yii;


/**
 * This is the model class for table "mercury_deckcard".
 *
 * @property integer $id
 * @property integer $deckcardnode_id
 * @property integer $card_id
 * @property integer $order_display
 * @property string $updated_at
 * @property string $created_at
 *
 * @property Card $card
 * @property Deckcardnode $deckcardnode
 */
class Deckcard extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'mercury_deckcard';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['deckcardnode_id', 'card_id'], 'required'],
            [['deckcardnode_id', 'card_id', 'order_display'], 'integer'],
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
            'deckcardnode_id' => 'Deckcardnode ID',
            'card_id' => 'Card ID',
            'order_display' => 'Order Display',
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
    public function getDeckcardnode()
    {
        return $this->hasOne(Deckcardnode::className(), ['id' => 'deckcardnode_id']);
    }
}
