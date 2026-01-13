<?php

/**
 * @link https://github.com/mohsin-rafique/expense-manager
 * @copyright Copyright (c) 2025 Mohsin Rafique
 * @license https://opensource.org/licenses/MIT MIT License
 */

/**
 * Monthly Performance Widget View
 *
 * Renders a donut chart showing balance vs expense ratio for the current month.
 *
 * @var yii\web\View $this
 * @var string $widgetId Unique widget identifier
 * @var string $title Widget title
 * @var string|null $containerClass Additional CSS classes
 * @var string $currentMonthName Formatted current month name
 * @var float $income Current month income
 * @var float $expense Current month expense
 * @var float $balance Current month balance (savings)
 * @var bool $hasData Whether there is data to display
 * @var int $chartHeight Chart height in pixels
 *
 * @author Mohsin Rafique <mohsin.rafique@gmail.com>
 * @since 1.0.0
 */

use yii\helpers\Html;

// Build container classes
$containerClasses = ['monthly-performance-widget'];
if ($containerClass) {
    $containerClasses[] = $containerClass;
}

$chartId = 'performanceDonutChart_' . $widgetId;
?>

<!-- ============================================================== -->
<!-- Monthly Performance Widget                                     -->
<!-- ============================================================== -->
<div class="<?= implode(' ', $containerClasses) ?>" id="<?= Html::encode($widgetId) ?>">
    <div class="card h-100 shadow-sm">

        <!-- Card Header -->
        <div class="card-header d-flex align-items-center justify-content-between">
            <h3 class="card-title h6 mb-0">
                <?= Html::encode($title) ?>
            </h3>
            <span class="badge bg-light text-dark">
                <?= Html::encode($currentMonthName) ?>
            </span>
        </div>

        <!-- Card Body -->
        <div class="card-body d-flex align-items-center justify-content-center">
            <?php if ($hasData): ?>
                <!-- Chart Container -->
                <div id="<?= Html::encode($chartId) ?>"
                    class="chart-container w-100"
                    style="min-height: <?= $chartHeight ?>px;"
                    role="img"
                    aria-label="<?= Yii::t('app', 'Donut chart showing balance vs expense ratio') ?>">
                </div>
            <?php else: ?>
                <!-- Empty State -->
                <div class="text-center py-5">
                    <i class="bi bi-pie-chart text-muted" style="font-size: 3rem;"></i>
                    <p class="text-muted mt-3 mb-0">
                        <?= Yii::t('app', 'No financial data for this month yet') ?>
                    </p>
                    <p class="text-muted small">
                        <?= Yii::t('app', 'Start by adding income or expenses') ?>
                    </p>
                </div>
            <?php endif ?>
        </div>

        <?php if ($hasData): ?>
            <!-- Card Footer with Summary -->
            <div class="card-footer bg-transparent py-2">
                <div class="row text-center small">
                    <div class="col-4">
                        <span class="text-muted d-block"><?= Yii::t('app', 'Income') ?></span>
                        <span class="fw-semibold text-success">
                            <?= Yii::$app->currency->format($income) ?>
                        </span>
                    </div>
                    <div class="col-4">
                        <span class="text-muted d-block"><?= Yii::t('app', 'Expenses') ?></span>
                        <span class="fw-semibold text-danger">
                            <?= Yii::$app->currency->format($expense) ?>
                        </span>
                    </div>
                    <div class="col-4">
                        <span class="text-muted d-block"><?= Yii::t('app', 'Savings') ?></span>
                        <span class="fw-semibold <?= $balance >= 0 ? 'text-success' : 'text-danger' ?>">
                            <?= Yii::$app->currency->format($balance) ?>
                        </span>
                    </div>
                </div>
            </div>
        <?php endif ?>

    </div>
</div>
<!-- End Monthly Performance Widget -->
