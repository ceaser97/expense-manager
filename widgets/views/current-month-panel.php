<?php

/**
 * @link https://github.com/mohsin-rafique/expense-manager
 * @copyright Copyright (c) 2025 Mohsin Rafique
 * @license https://opensource.org/licenses/MIT MIT License
 */

/**
 * Current Month Panel Widget View (Summary Mode)
 *
 * Displays current month income, expense, and profit/loss metrics
 * in a card-based layout with icons and trend indicators.
 *
 * @var yii\web\View $this
 * @var string $widgetId Unique widget identifier
 * @var string $title Widget title
 * @var string|null $containerClass Additional CSS classes
 * @var float $income Current month income
 * @var float $expense Current month expense
 * @var float $profitLoss Current month profit/loss
 * @var array $icons Icon classes for each metric
 * @var string $currentMonthName Formatted current month name
 * @var bool $isProfit Whether profit/loss is positive
 *
 * @author Mohsin Rafique <mohsin.rafique@gmail.com>
 * @since 1.0.0
 */

use yii\helpers\Html;

// Build container classes
$containerClasses = ['current-month-panel-widget'];
if ($containerClass) {
    $containerClasses[] = $containerClass;
}

// Determine profit/loss styling
$profitLossClass = $isProfit ? 'text-success' : 'text-danger';
$profitLossIcon = $isProfit ? 'bi-arrow-up-circle-fill' : 'bi-arrow-down-circle-fill';
$profitLossLabel = $isProfit ? Yii::t('app', 'Profit') : Yii::t('app', 'Loss');
?>

<!-- ============================================================== -->
<!-- Current Month Panel Widget (Summary Mode)                      -->
<!-- ============================================================== -->
<div class="<?= implode(' ', $containerClasses) ?>" id="<?= Html::encode($widgetId) ?>">
    <div class="card h-100 shadow-sm">
        <div class="card-body p-0">
            <div class="row g-0">

                <!-- Income Metric -->
                <div class="col-12 border-bottom">
                    <div class="p-4">
                        <div class="d-flex align-items-start justify-content-between mb-2">
                            <h6 class="text-muted text-uppercase fw-semibold mb-0" style="font-size: 0.75rem; letter-spacing: 0.05em;">
                                <?= Yii::t('app', 'Current Month Income') ?>
                            </h6>
                            <span class="text-success">
                                <i class="bi bi-arrow-up-circle-fill"></i>
                            </span>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 me-3">
                                <span class="d-inline-flex align-items-center justify-content-center bg-success bg-opacity-10 rounded"
                                    style="width: 48px; height: 48px;">
                                    <i class="<?= Html::encode($icons['income']) ?> text-success fs-4"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <h3 class="mb-0 text-success" style="font-size: 1.5rem; font-weight: 600;">
                                    <?= Yii::$app->currency->format($income) ?>
                                </h3>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Expense Metric -->
                <div class="col-12 border-bottom">
                    <div class="p-4">
                        <div class="d-flex align-items-start justify-content-between mb-2">
                            <h6 class="text-muted text-uppercase fw-semibold mb-0" style="font-size: 0.75rem; letter-spacing: 0.05em;">
                                <?= Yii::t('app', 'Current Month Expenses') ?>
                            </h6>
                            <span class="text-danger">
                                <i class="bi bi-arrow-down-circle-fill"></i>
                            </span>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 me-3">
                                <span class="d-inline-flex align-items-center justify-content-center bg-danger bg-opacity-10 rounded"
                                    style="width: 48px; height: 48px;">
                                    <i class="<?= Html::encode($icons['expense']) ?> text-danger fs-4"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <h3 class="mb-0 text-danger" style="font-size: 1.5rem; font-weight: 600;">
                                    <?= Yii::$app->currency->format($expense) ?>
                                </h3>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Profit/Loss Metric -->
                <div class="col-12">
                    <div class="p-4">
                        <div class="d-flex align-items-start justify-content-between mb-2">
                            <h6 class="text-muted text-uppercase fw-semibold mb-0" style="font-size: 0.75rem; letter-spacing: 0.05em;">
                                <?= Yii::t('app', 'Net {status}', ['status' => $profitLossLabel]) ?>
                            </h6>
                            <span class="<?= $profitLossClass ?>">
                                <i class="<?= $profitLossIcon ?>"></i>
                            </span>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 me-3">
                                <span class="d-inline-flex align-items-center justify-content-center <?= $isProfit ? 'bg-success' : 'bg-danger' ?> bg-opacity-10 rounded"
                                    style="width: 48px; height: 48px;">
                                    <i class="<?= Html::encode($icons['profit']) ?> <?= $profitLossClass ?> fs-4"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <h3 class="mb-0 <?= $profitLossClass ?>" style="font-size: 1.5rem; font-weight: 600;">
                                    <?php if (!$isProfit): ?>
                                        <span>-</span>
                                    <?php endif ?>
                                    <?= Yii::$app->currency->format(abs($profitLoss)) ?>
                                </h3>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Card Footer -->
        <div class="card-footer bg-transparent py-2">
            <div class="d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    <i class="bi bi-calendar3 me-1"></i>
                    <?= Html::encode($currentMonthName) ?>
                </small>
                <small class="text-muted">
                    <i class="bi bi-clock-history me-1"></i>
                    <?= Yii::t('app', 'Live') ?>
                </small>
            </div>
        </div>
    </div>
</div>
<!-- End Current Month Panel Widget -->
