<?php

namespace app\models;

use Yii;


/**
 * This is the model class for table "mercury_orderitem".
 *
 * @property integer $id
 * @property integer $order_id
 * @property integer $card_id
 * @property integer $quantity
 * @property integer $grade
 * @property integer $sell_price
 * @property string $tax
 * @property integer $active
 * @property integer $processed
 * @property string $updated_at
 * @property string $created_at
 *
 * @property Card $card
 * @property Order $order
 */
class Orderitem extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'mercury_orderitem';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id', 'card_id'], 'required'],
            [['order_id', 'card_id', 'quantity', 'grade', 'sell_price', 'active', 'processed'], 'integer'],
            [['tax', 'updated_at', 'created_at'], 'string', 'max' => 45]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_id' => 'Order ID',
            'card_id' => 'Card ID',
            'quantity' => 'Quantity',
            'grade' => 'Grade',
            'sell_price' => 'Sell Price',
            'tax' => 'Tax',
            'active' => 'Active',
            'processed' => 'Processed',
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
    public function getOrder()
    {
        return $this->hasOne(Order::className(), ['id' => 'order_id']);
    }
}
