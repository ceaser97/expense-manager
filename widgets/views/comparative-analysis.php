<?php

/**
 * @link https://github.com/mohsin-rafique/expense-manager
 * @copyright Copyright (c) 2025 Mohsin Rafique
 * @license https://opensource.org/licenses/MIT MIT License
 */

/**
 * Comparative Analysis Widget View
 *
 * Renders fiscal year financial metrics and expense category chart.
 *
 * @var yii\web\View $this
 * @var string $widgetId Unique widget identifier
 * @var string $title Widget title
 * @var string $subtitle Widget subtitle
 * @var string|null $containerClass Additional CSS classes
 * @var string $fiscalYearLabel Fiscal year display label
 * @var float $revenue Total revenue for fiscal year
 * @var float $expenditure Total expenditure for fiscal year
 * @var float $netPosition Net position (revenue - expenditure)
 * @var bool $isNetPositive Whether net position is positive
 * @var array $ExpenseCategory Category names => amounts
 * @var bool $showTrendIndicators Whether to show trend arrows
 * @var array $revenueTrend Revenue trend indicator config
 * @var array $expenditureTrend Expenditure trend indicator config
 * @var array $netTrend Net position trend indicator config
 * @var string $chartId Chart element ID
 * @var array $chartColors Chart color configuration
 *
 * @author Mohsin Rafique <mohsin.rafique@gmail.com>
 * @since 1.0.0
 */

use yii\helpers\Html;

// Build container classes
$containerClasses = ['comparative-analysis-widget mb-3'];
if ($containerClass) {
    $containerClasses[] = $containerClass;
}

// Format currency values
$formattedRevenue = Yii::$app->currency->format($revenue);
$formattedExpenditure = Yii::$app->currency->format($expenditure);
$formattedNetPosition = Yii::$app->currency->format(abs($netPosition));

// Net position labels
$netLabel = $isNetPositive
    ? Yii::t('app', 'Net Surplus')
    : Yii::t('app', 'Net Deficit');
$netDescription = $isNetPositive
    ? Yii::t('app', 'You earned more than you spent')
    : Yii::t('app', 'You spent more than you earned');
?>

<!-- ============================================================== -->
<!-- Comparative Analysis Widget                                    -->
<!-- ============================================================== -->
<div class="<?= implode(' ', $containerClasses) ?>" id="<?= Html::encode($widgetId) ?>">

    <!-- Widget Header -->
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h2 class="h5 mb-1"><?= Html::encode($title) ?></h2>
            <p class="text-muted small mb-0"><?= Html::encode($subtitle) ?></p>
        </div>
        <div class="text-end">
            <span class="badge bg-primary">
                <?= Html::encode($fiscalYearLabel) ?>
            </span>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row g-4">

        <!-- Financial Metrics Card -->
        <div class="col-xl-6">
            <div class="card h-100 shadow-sm">
                <div class="card-body p-0">

                    <!-- Revenue Metric -->
                    <div class="p-4 border-bottom">
                        <div class="d-flex align-items-start justify-content-between mb-2">
                            <h6 class="text-muted text-uppercase fw-semibold mb-0" style="font-size: 0.75rem; letter-spacing: 0.05em;">
                                <?= Yii::t('app', 'Fiscal Year Revenue') ?>
                            </h6>
                            <?php if ($showTrendIndicators): ?>
                                <span class="<?= $revenueTrend['class'] ?>"
                                    title="<?= Html::encode($revenueTrend['label']) ?>"
                                    data-bs-toggle="tooltip">
                                    <i class="<?= $revenueTrend['icon'] ?>"></i>
                                    <?php if ($revenueTrend['percent'] !== null): ?>
                                        <small class="ms-1"><?= $revenueTrend['percent'] > 0 ? '+' : '' ?><?= $revenueTrend['percent'] ?>%</small>
                                    <?php endif ?>
                                </span>
                            <?php endif ?>
                        </div>
                        <p class="text-muted small mb-2">
                            <?= Yii::t('app', 'Total income earned during this fiscal year') ?>
                        </p>
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 me-3">
                                <span class="d-inline-flex align-items-center justify-content-center bg-success bg-opacity-10 rounded"
                                    style="width: 48px; height: 48px;">
                                    <i class="bi bi-graph-up-arrow text-success fs-4"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <h3 class="mb-0 text-success" style="font-size: 1.5rem; font-weight: 600;">
                                    <?= Html::encode($formattedRevenue) ?>
                                </h3>
                            </div>
                        </div>
                    </div>

                    <!-- Expenditure Metric -->
                    <div class="p-4 border-bottom">
                        <div class="d-flex align-items-start justify-content-between mb-2">
                            <h6 class="text-muted text-uppercase fw-semibold mb-0" style="font-size: 0.75rem; letter-spacing: 0.05em;">
                                <?= Yii::t('app', 'Fiscal Year Expenditure') ?>
                            </h6>
                            <?php if ($showTrendIndicators): ?>
                                <span class="<?= $expenditureTrend['class'] ?>"
                                    title="<?= Html::encode($expenditureTrend['label']) ?>"
                                    data-bs-toggle="tooltip">
                                    <i class="<?= $expenditureTrend['icon'] ?>"></i>
                                    <?php if ($expenditureTrend['percent'] !== null): ?>
                                        <small class="ms-1"><?= $expenditureTrend['percent'] > 0 ? '+' : '' ?><?= $expenditureTrend['percent'] ?>%</small>
                                    <?php endif ?>
                                </span>
                            <?php endif ?>
                        </div>
                        <p class="text-muted small mb-2">
                            <?= Yii::t('app', 'Total expenses incurred during this fiscal year') ?>
                        </p>
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 me-3">
                                <span class="d-inline-flex align-items-center justify-content-center bg-danger bg-opacity-10 rounded"
                                    style="width: 48px; height: 48px;">
                                    <i class="bi bi-graph-down-arrow text-danger fs-4"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <h3 class="mb-0 text-danger" style="font-size: 1.5rem; font-weight: 600;">
                                    <?= Html::encode($formattedExpenditure) ?>
                                </h3>
                            </div>
                        </div>
                    </div>

                    <!-- Net Position Metric -->
                    <div class="p-4">
                        <div class="d-flex align-items-start justify-content-between mb-2">
                            <h6 class="text-muted text-uppercase fw-semibold mb-0" style="font-size: 0.75rem; letter-spacing: 0.05em;">
                                <?= Html::encode($netLabel) ?>
                            </h6>
                            <?php if ($showTrendIndicators): ?>
                                <span class="<?= $netTrend['class'] ?>"
                                    title="<?= Html::encode($netTrend['label']) ?>"
                                    data-bs-toggle="tooltip">
                                    <i class="<?= $netTrend['icon'] ?>"></i>
                                </span>
                            <?php endif ?>
                        </div>
                        <p class="text-muted small mb-2">
                            <?= Html::encode($netDescription) ?>
                        </p>
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 me-3">
                                <span class="d-inline-flex align-items-center justify-content-center <?= $isNetPositive ? 'bg-success' : 'bg-danger' ?> bg-opacity-10 rounded"
                                    style="width: 48px; height: 48px;">
                                    <i class="bi bi-wallet2 <?= $isNetPositive ? 'text-success' : 'text-danger' ?> fs-4"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <h3 class="mb-0 <?= $isNetPositive ? 'text-success' : 'text-danger' ?>" style="font-size: 1.5rem; font-weight: 600;">
                                    <?php if (!$isNetPositive): ?>
                                        <span>-</span>
                                    <?php endif ?>
                                    <?= Html::encode($formattedNetPosition) ?>
                                </h3>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- Category Chart Card -->
        <div class="col-xl-6">
            <div class="card h-100 shadow-sm">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h3 class="card-title h6 mb-0">
                        <?= Yii::t('app', 'Expenditure by Category') ?>
                    </h3>
                    <span class="badge bg-light text-dark">
                        <?= Html::encode($fiscalYearLabel) ?>
                    </span>
                </div>
                <div class="card-body">
                    <?php if (empty($ExpenseCategory)): ?>
                        <!-- Empty State -->
                        <div class="text-center py-5">
                            <i class="bi bi-pie-chart text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-3 mb-0">
                                <?= Yii::t('app', 'No expense data available for this period') ?>
                            </p>
                        </div>
                    <?php else: ?>
                        <!-- Chart Container -->
                        <div id="<?= Html::encode($chartId) ?>"
                            style="min-height: 350px;"
                            role="img"
                            aria-label="<?= Yii::t('app', 'Horizontal bar chart showing expenses by category') ?>">
                        </div>
                    <?php endif ?>
                </div>
            </div>
        </div>

    </div>

</div>
<!-- End Comparative Analysis Widget -->
