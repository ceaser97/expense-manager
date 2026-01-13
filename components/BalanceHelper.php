<?php

namespace app\components;

use Yii;

class BalanceHelper
{
    public static function getBalance($userId)
    {
        // Get Total Incomes
        $totalIncomes = Yii::$app->db->createCommand('SELECT SUM(amount) FROM {{%incomes}} WHERE user_id = :user_id')
            ->bindValue(':user_id', $userId)
            ->queryScalar();

        // Get Total Expenses
        $totalExpenses = Yii::$app->db->createCommand('SELECT SUM(amount) FROM {{%expenses}} WHERE user_id = :user_id')
            ->bindValue(':user_id', $userId)
            ->queryScalar();

        // Calculate Total Profit/Loss
        return $totalIncomes - $totalExpenses;
    }
}
