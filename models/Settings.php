<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%settings}}".
 *
 * @property int $id
 * @property int $user_id
 * @property string $company_name
 * @property string|null $site_title
 * @property string|null $phone
 * @property string|null $email
 * @property string|null $timezone
 * @property string|null $date_format
 * @property string|null $time_format
 * @property string|null $currency
 * @property string|null $currency_position
 * @property string|null $thousand_separator
 * @property string|null $decimal_separator
 * @property int|null $decimal_places
 * @property string|null $logo
 * @property string|null $favicon
 *
 * @property User $user
 */
class Settings extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%settings}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'company_name'], 'required'],
            [['user_id', 'decimal_places'], 'integer'],
            [['company_name', 'site_title', 'phone', 'email', 'timezone', 'date_format', 'time_format', 'logo', 'favicon'], 'string', 'max' => 191],
            [['currency'], 'string', 'max' => 5],
            [['currency_position'], 'string', 'max' => 16],
            [['thousand_separator', 'decimal_separator'], 'string', 'max' => 1],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'user_id' => Yii::t('app', 'User ID'),
            'company_name' => Yii::t('app', 'Company Name'),
            'site_title' => Yii::t('app', 'Site Title'),
            'phone' => Yii::t('app', 'Phone'),
            'email' => Yii::t('app', 'Email'),
            'timezone' => Yii::t('app', 'Timezone'),
            'date_format' => Yii::t('app', 'Date Format'),
            'time_format' => Yii::t('app', 'Time Format'),
            'currency' => Yii::t('app', 'Currency'),
            'currency_position' => Yii::t('app', 'Currency Position'),
            'thousand_separator' => Yii::t('app', 'Thousand Separator'),
            'decimal_separator' => Yii::t('app', 'Decimal Separator'),
            'decimal_places' => Yii::t('app', 'Decimal Places'),
            'logo' => Yii::t('app', 'Logo'),
            'favicon' => Yii::t('app', 'Favicon'),
        ];
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}
