<?php

namespace app\models;

use Yii;


/**
 * This is the model class for table "mercury_poscategory1".
 *
 * @property integer $id
 * @property string $name
 * @property integer $order_display
 * @property string $updated_at
 * @property string $created_at
 *
 * @property Card[] $cards
 */
class Poscategory1 extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'mercury_poscategory1';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'name'], 'required'],
            [['id', 'order_display'], 'integer'],
            [['updated_at', 'created_at'], 'safe'],
            [['name'], 'string', 'max' => 120]
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
            'order_display' => 'Order Display',
            'updated_at' => 'Updated At',
            'created_at' => 'Created At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCards()
    {
        return $this->hasMany(Card::className(), ['pos_category_1' => 'id']);
    }
}
