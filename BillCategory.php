<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%bill_category}}".
 *
 * @property int $id
 * @property string $code
 * @property string $name
 * @property int $sort
 */
class BillCategory extends \yii\db\ActiveRecord
{
    const CODE_UTILITY_BILLS      = 'UTILITY_BILLS';
    const CODE_CLEANING           = 'CLEANING';
    const CODE_DRY_CLEANING       = 'DRY_CLEANING';
    const CODE_TECHNICAL_SERVICES = 'TECHNICAL_SERVICES';
    const CODE_PARKING            = 'PARKING';
    const CODE_OTHER            = 'OTHER';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%bill_category}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['sort'], 'integer'],
            [['name'], 'string', 'max' => 100],
            [['code'], 'string', 'max' => 36],
            [['code'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'   => 'ID',
            'code' => 'Code',
            'name' => 'Name',
            'sort' => 'Sort',
        ];
    }

    /**
     * @return array
     */
    public static function getList()
    {
        return self::find()
            ->orderBy(['sort' => SORT_ASC])
            ->asArray()
            ->all()
        ;
    }

    /**
     * @param string $code
     * @return BillCategory|null
     */
    public static function getCategoryByCode($code)
    {
        return self::findOne([
            'code' => $code
        ]);
    }
}
