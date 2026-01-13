<?php

/**
 * @link https://github.com/mohsin-rafique/expense-manager
 * @copyright Copyright (c) 2025 Mohsin Rafique
 * @license https://opensource.org/licenses/MIT MIT License
 */

/**
 * Dashboard View
 *
 * Main dashboard displaying financial overview, trends, and analytics.
 * Provides at-a-glance insights into income, expenses, and balance.
 *
 * @var yii\web\View $this
 *
 * @author Mohsin Rafique <mohsin.rafique@gmail.com>
 * @since 1.0.0
 */

use yii\bootstrap5\Html;
use app\widgets\CurrentMonthPanelWidget;
use app\widgets\MonthlyPerformanceWidget;
use app\widgets\ExpensesByCategoryWidget;
use app\widgets\FiscalYearExpenseSummaryByMonth;
use app\widgets\ComparativeAnalysisPanel;
use app\widgets\LifetimeOverviewWidget;

// Page configuration
$this->title = Yii::t('app', 'Dashboard');
$this->params['breadcrumbs'][] = $this->title;

// Fiscal year configuration
$fiscalYearConfig = [
    'startDate' => '2024-07-01',
    'endDate' => '2025-06-30',
    'label' => 'FY 2024-25',
];

// Currency configuration
$currencyCode = Yii::$app->params['currencyCode'] ?? 'PKR';
?>

<!-- ============================================================== -->
<!-- Dashboard Header                                               -->
<!-- ============================================================== -->
<div class="dashboard-header mb-4">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h1 class="h3 mb-1"><?= Html::encode($this->title) ?></h1>
            <p class="text-muted mb-0">
                <?= Yii::t('app', 'Welcome back! Here\'s your financial overview.') ?>
            </p>
        </div>
        <div class="col-md-6 text-md-end">
            <p class="text-muted mb-0">
                <small>
                    <i class="bi bi-clock me-1"></i>
                    <?= Yii::t('app', 'Last updated: {date}', [
                        'date' => Yii::$app->formatter->asDatetime(time(), 'medium'),
                    ]) ?>
                </small>
            </p>
        </div>
    </div>
</div>

<!-- ============================================================== -->
<!-- Current Month Panel                                            -->
<!-- Quick overview of this month's financial status                -->
<!-- ============================================================== -->
<section class="dashboard-section mb-4">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h2 class="h5 mb-0">
            <?= Yii::t('app', 'Current Month Overview') ?>
        </h2>
        <span class="badge bg-primary">
            <?= Yii::$app->formatter->asDate(time(), 'MMMM yyyy') ?>
        </span>
    </div>

    <div class="row g-4">
        <!-- Summary Stats -->
        <div class="col-xl-6">
            <?= CurrentMonthPanelWidget::widget([
                'mode' => 'summary'
            ]) ?>
        </div>

        <!-- Performance Donut Chart -->
        <div class="col-xl-6">
            <?= MonthlyPerformanceWidget::widget(); ?>
        </div>
    </div>
</section>

<!-- ============================================================== -->
<!-- Financial Trends Panel                                         -->
<!-- Month-over-month financial evolution                           -->
<!-- ============================================================== -->
<section class="dashboard-section mb-4">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h2 class="h5 mb-0">
            <?= Yii::t('app', 'Financial Trends') ?>
        </h2>
    </div>

    <div class="row g-4">
        <!-- Evolution Chart -->
        <div class="col-xl-6">
            <?= CurrentMonthPanelWidget::widget([
                'mode' => 'evolution'
            ]) ?>
        </div>

        <!-- Category Breakdown -->
        <div class="col-xl-6">
            <?= ExpensesByCategoryWidget::widget([
                'maxCategories' => 10,
            ]) ?>
        </div>
    </div>
</section>

<!-- ============================================================== -->
<!-- Fiscal Year Summary                                            -->
<!-- Year-to-date expense breakdown by month                        -->
<!-- ============================================================== -->
<?= FiscalYearExpenseSummaryByMonth::widget([
    'fiscalStartDate' => $fiscalYearConfig['startDate'],
    'fiscalEndDate' => $fiscalYearConfig['endDate'],
    'fiscalYearLabel' => $fiscalYearConfig['label'],
    'title' => Yii::t('app', 'Fiscal Year Expense Summary'),
    'subtitle' => Yii::t('app', 'Monthly breakdown by category'),
    'enableExport' => true,
    'enableFiltering' => true,
    'containerClass' => 'mb-4',
]) ?>

<!-- ============================================================== -->
<!-- Comparative Analysis Panel                                     -->
<!-- Period-over-period comparison and insights                     -->
<!-- ============================================================== -->
<?= ComparativeAnalysisPanel::widget([
    'fiscalStartDate' => $fiscalYearConfig['startDate'],
    'fiscalEndDate' => $fiscalYearConfig['endDate'],
    'fiscalYearLabel' => $fiscalYearConfig['label'],
    'showTrendIndicators' => true,
    'enablePreviousPeriodComparison' => true,
    'containerClass' => 'mb-4',
    'maxCategories' => 10,
]) ?>

<!-- ============================================================== -->
<!-- Lifetime Overview Panel                                        -->
<!-- All-time financial statistics                                  -->
<!-- ============================================================== -->
<?= LifetimeOverviewWidget::widget([
    'showTrendIndicators' => true,
    'currencyCode' => $currencyCode,
    'containerClass' => 'mb-4',
]) ?>

<?php
/**
 * Register ApexCharts library
 */
$this->registerJsFile('/libs/apexcharts/apexcharts.min.js', [
    'depends' => [\yii\web\JqueryAsset::class],
    'position' => \yii\web\View::POS_END,
]);
