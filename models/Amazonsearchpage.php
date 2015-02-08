<?php

namespace app\models;

use Yii;


/**
 * This is the model class for table "mercury_amazonsearchpage".
 *
 * @property integer $id
 * @property integer $card_id
 * @property integer $category2_id
 * @property string $search_key
 * @property string $url
 * @property string $html
 * @property string $updated_at
 * @property string $created_at
 *
 * @property Card $card
 */
class Amazonsearchpage extends \yii\db\ActiveRecord
{
    const kAttrKeyCardId             = 'card_id';
    const kAttrKeyCategory2Id        = 'category2_id';
    const kAttrKeySearchKey          = 'search_key';
    const kAttrKeyUrl                = 'url';
    const kAttrKeyHtml               = 'html';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'mercury_amazonsearchpage';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['card_id', 'category2_id', 'search_key', 'url', 'updated_at', 'created_at'], 'required'],
            [['card_id', 'category2_id'], 'integer'],
            [['search_key', 'html', 'url'], 'string'],
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
            'card_id' => 'Card ID',
            'category2_id' => 'Category2 ID',
            'search_key' => 'Search Key',
            'url' => 'Url',
            'html' => 'Html',
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
}
