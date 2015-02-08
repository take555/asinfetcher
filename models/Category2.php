<?php

namespace app\models;

use Yii;


/**
 * This is the model class for table "mercury_category2".
 *
 * @property integer $id
 * @property string $name
 * @property integer $order_display
 * @property integer $active
 * @property string $updated_at
 * @property string $created_at
 *
 * @property Card[] $cards
 * @property Category2banner[] $category2banners
 * @property Deckcategory[] $deckcategories
 * @property Logaction[] $logactions
 * @property Pdfstyle $pdfstyle
 */
class Category2 extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'mercury_category2';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'name'], 'required'],
            [['id', 'order_display', 'active'], 'integer'],
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
        return $this->hasMany(Card::className(), ['category_2' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategory2banners()
    {
        return $this->hasMany(Category2banner::className(), ['category_2_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDeckcategories()
    {
        return $this->hasMany(Deckcategory::className(), ['category2_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLogactions()
    {
        return $this->hasMany(Logaction::className(), ['category2_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPdfstyle()
    {
        return $this->hasOne(Pdfstyle::className(), ['category2_id' => 'id']);
    }
}
