<?php

namespace app\components;

use Yii;
use yii\base\BootstrapInterface;
use app\models\Settings;

/**
 * Component for Detecting language automatically
 * Idea from https://github.com/samdark/yii2-cookbook/blob/master/book/i18n-selecting-application-language.md#detecting-language-automatically
 */

class LanguageSelector implements BootstrapInterface
{
    public $supportedLanguages = [];

    public function bootstrap($app)
    {
        if (!Yii::$app->user->isGuest) {
            $userId = Yii::$app->user->identity->id;

            // Query the 'settings' table based on the user ID
            $settings = Settings::findOne(['user_id' => $userId]);

            Yii::$app->language = 'en';
            Yii::$app->formatter->currencyCode = $settings->currency;
            Yii::$app->formatter->thousandSeparator = $settings->thousand_separator;
            Yii::$app->formatter->decimalSeparator = $settings->decimal_separator;

            Yii::$app->formatter->dateFormat = 'php:' . $settings->date_format;
            Yii::$app->formatter->datetimeFormat = 'php:d/m/Y H:i:s';
        } else {
            Yii::$app->language = Yii::$app->request->getPreferredLanguage($this->supportedLanguages);
            Yii::$app->formatter->currencyCode = 'PKR';
            Yii::$app->formatter->decimalSeparator = '.';
            Yii::$app->formatter->thousandSeparator = ',';

            Yii::$app->formatter->dateFormat = 'php:d/m/Y';
            Yii::$app->formatter->datetimeFormat = 'php:d/m/Y H:i:s';
        }
    }
}
