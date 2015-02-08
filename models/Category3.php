<?php

namespace app\models;

use Yii;


/**
 * This is the model class for table "mercury_category3".
 *
 * @property integer $id
 * @property string $name
 * @property integer $category2_id
 * @property integer $order_display
 * @property integer $active
 * @property string $updated_at
 * @property string $created_at
 *
 * @property Card[] $cards
 */
class Category3 extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'mercury_category3';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'name'], 'required'],
            [['id', 'category2_id', 'order_display', 'active'], 'integer'],
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
            'category2_id' => 'Category2 ID',
            'order_display' => 'Order Display',
            'active' => 'Active',
            'updated_at' => 'Updated At',
            'created_at' => 'Created At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCards()
    {
        return $this->hasMany(Card::className(), ['category_3' => 'id']);
    }
}
