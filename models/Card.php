<?php

namespace app\models;

use Yii;


/**
 * This is the model class for table "mercury_card".
 *
 * @property integer $id
 * @property integer $oldcode
 * @property string $name_short
 * @property string $name
 * @property string $yomi
 * @property string $en_name
 * @property string $image_name
 * @property string $serial
 * @property integer $rarity_id
 * @property string $rarity_short
 * @property string $rarity
 * @property string $rarity_ja
 * @property integer $type_id
 * @property integer $star
 * @property integer $attribute_id
 * @property integer $race_id
 * @property integer $attack
 * @property integer $defence
 * @property integer $series_id
 * @property integer $box_id
 * @property string $release_date
 * @property string $file_name
 * @property integer $image_exist
 * @property string $pos_sub_title
 * @property string $pos_en_name
 * @property string $search_en_name
 * @property string $pos_file_name
 * @property string $etc_1
 * @property string $etc_2
 * @property string $etc_3
 * @property string $etc_4
 * @property string $etc_5
 * @property string $etc_6
 * @property string $search_hint
 * @property integer $category_1
 * @property integer $category_2
 * @property integer $category_3
 * @property integer $pos_category_1
 * @property integer $pos_category_2
 * @property integer $pos_category_3
 * @property string $extra_1
 * @property string $extra_2
 * @property integer $buy_price
 * @property integer $sell_price
 * @property integer $itemstatus_id
 * @property integer $rank_from_user
 * @property integer $rank_from_anime
 * @property integer $rank_from_collector
 * @property string $flyer
 * @property string $updated_at
 * @property string $created_at
 *
 * @property Attribute $attribute
 * @property Box $box
 * @property Category1 $category1
 * @property Category2 $category2
 * @property Category3 $category3
 * @property Itemstatus $itemstatus
 * @property Series $series
 * @property Type $type
 * @property Deckcard[] $deckcards
 * @property Deckcardnode[] $deckcardnodes
 * @property Logaction[] $logactions
 * @property Orderitem[] $orderitems
 */
class Card extends \yii\db\ActiveRecord
{

    const kAttrKeyId              = 'id';
    const kAttrKeyName            = 'name';
    const kAttrKeyEnName          = 'en_name';
    const kAttrKeySerial          = 'serial';
    const kAttrKeyBoxId           = 'box_id';
    const kAttrKeyBox             = 'box';
    const kAttrKeyNameShort       = 'name_short';
    const kAttrKeyCat2Id          = 'category_2';
    const kAttrKeyCat2            = 'category2';
    const kAttrKeyRarity          = 'rarity';
    const kAttrKeyRarityJa        = 'rarity_ja';
    const kAttrKeyRarityShort     = 'rarity_short';
    const kAttrKeyUpdatedAt       = 'updated_at';

    const kParamsKeyCat2Id        = 'category2Id';
    const kParamsKeyLimit         = 'limit';
    const kParamsKeyOffset        = 'offset';
    const kParamsKeySelect        = 'select';
    const kParamsKeyTest          = 'test';
    const kParamsValueTestTrue    = 'true';


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'mercury_card';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'name'], 'required'],
            [['id', 'oldcode', 'rarity_id', 'type_id', 'star', 'attribute_id', 'race_id', 'attack', 'defence', 'series_id', 'box_id', 'image_exist', 'category_1', 'category_2', 'category_3', 'pos_category_1', 'pos_category_2', 'pos_category_3', 'buy_price', 'sell_price', 'itemstatus_id', 'rank_from_user', 'rank_from_anime', 'rank_from_collector'], 'integer'],
            [['release_date', 'updated_at', 'created_at'], 'safe'],
            [['search_hint'], 'string'],
            [['name_short', 'name', 'yomi'], 'string', 'max' => 50],
            [['en_name', 'serial'], 'string', 'max' => 60],
            [['image_name'], 'string', 'max' => 100],
            [['rarity_short'], 'string', 'max' => 12],
            [['rarity', 'rarity_ja'], 'string', 'max' => 45],
            [['file_name', 'pos_sub_title', 'pos_en_name', 'flyer'], 'string', 'max' => 120],
            [['search_en_name', 'pos_file_name'], 'string', 'max' => 160],
            [['etc_1', 'etc_2', 'etc_3', 'etc_4', 'etc_5', 'etc_6', 'extra_1', 'extra_2'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'oldcode' => 'Oldcode',
            'name_short' => 'Name Short',
            'name' => 'Name',
            'yomi' => 'Yomi',
            'en_name' => 'En Name',
            'image_name' => 'Image Name',
            'serial' => 'Serial',
            'rarity_id' => 'Rarity ID',
            'rarity_short' => 'Rarity Short',
            'rarity' => 'Rarity',
            'rarity_ja' => 'Rarity Ja',
            'type_id' => 'Type ID',
            'star' => 'Star',
            'attribute_id' => 'Attribute ID',
            'race_id' => 'Race ID',
            'attack' => 'Attack',
            'defence' => 'Defence',
            'series_id' => 'Series ID',
            'box_id' => 'Box ID',
            'release_date' => 'Release Date',
            'file_name' => 'File Name',
            'image_exist' => 'Image Exist',
            'pos_sub_title' => 'Pos Sub Title',
            'pos_en_name' => 'Pos En Name',
            'search_en_name' => 'Search En Name',
            'pos_file_name' => 'Pos File Name',
            'etc_1' => 'Etc 1',
            'etc_2' => 'Etc 2',
            'etc_3' => 'Etc 3',
            'etc_4' => 'Etc 4',
            'etc_5' => 'Etc 5',
            'etc_6' => 'Etc 6',
            'search_hint' => 'Search Hint',
            'category_1' => 'Category 1',
            'category_2' => 'Category 2',
            'category_3' => 'Category 3',
            'pos_category_1' => 'Pos Category 1',
            'pos_category_2' => 'Pos Category 2',
            'pos_category_3' => 'Pos Category 3',
            'extra_1' => 'Extra 1',
            'extra_2' => 'Extra 2',
            'buy_price' => 'Buy Price',
            'sell_price' => 'Sell Price',
            'itemstatus_id' => 'Itemstatus ID',
            'rank_from_user' => 'Rank From User',
            'rank_from_anime' => 'Rank From Anime',
            'rank_from_collector' => 'Rank From Collector',
            'flyer' => 'Flyer',
            'updated_at' => 'Updated At',
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
    public function getCategory1()
    {
        return $this->hasOne(Category1::className(), ['id' => 'category_1']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategory2()
    {
        return $this->hasOne(Category2::className(), ['id' => 'category_2']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategory3()
    {
        return $this->hasOne(Category3::className(), ['id' => 'category_3']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItemstatus()
    {
        return $this->hasOne(Itemstatus::className(), ['id' => 'itemstatus_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPosCategory1()
    {
        return $this->hasOne(Poscategory1::className(), ['id' => 'pos_category_1']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPosCategory2()
    {
        return $this->hasOne(Poscategory2::className(), ['id' => 'pos_category_2']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPosCategory3()
    {
        return $this->hasOne(Poscategory3::className(), ['id' => 'pos_category_3']);
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
    public function getRarity0()
    {
        return $this->hasOne(Rarity::className(), ['id' => 'rarity_id']);
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

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDeckcards()
    {
        return $this->hasMany(Deckcard::className(), ['card_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDeckcardnodes()
    {
        return $this->hasMany(Deckcardnode::className(), ['primary_card_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLogactions()
    {
        return $this->hasMany(Logaction::className(), ['card_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrderitems()
    {
        return $this->hasMany(Orderitem::className(), ['card_id' => 'id']);
    }

    public static function getCardList($params = array()){

        $checkTargetKeyList = [
            self::kParamsKeyCat2Id,
            self::kParamsKeyOffset,
            self::kParamsKeyLimit,
        ];


        if(!Yii::$app->util->isAssocArrayKeysExist($checkTargetKeyList, $params)){
            return false;
        }


        $select = [
            self::kAttrKeyId,
            self::kAttrKeyName,
            self::kAttrKeyNameShort,
            self::kAttrKeyEnName,
            self::kAttrKeyBoxId,
            self::kAttrKeySerial,
            self::kAttrKeyCat2Id,
            self::kAttrKeyRarity,
            self::kAttrKeyRarityShort,
        ];

        if(isset($params[self::kParamsKeySelect]) && is_array($params[self::kParamsKeySelect])){
            $select = $params[self::kParamsKeySelect];
        }


        $cardList = Card::find()
            ->select($select)
            ->with(self::kAttrKeyBox, self::kAttrKeyCat2)
            ->where([self::kAttrKeyCat2Id => intval($params[self::kParamsKeyCat2Id])])
            ->orderBy(self::kAttrKeyId)
            ->offset(intval($params[self::kParamsKeyOffset]))
            ->limit(intval($params[self::kParamsKeyLimit]))
            ->asArray()
            ->all();



        return $cardList;

    }



}
