<?php

/**
 * @link https://github.com/mohsin-rafique/expense-manager
 * @copyright Copyright (c) 2025 Mohsin Rafique
 * @license https://opensource.org/licenses/MIT MIT License
 */

/**
 * Expenses by Category Widget View
 *
 * Renders a horizontal bar chart showing expense breakdown by category.
 *
 * @var yii\web\View $this
 * @var string $widgetId Unique widget identifier
 * @var string $title Widget title
 * @var string|null $containerClass Additional CSS classes
 * @var string $periodLabel Period label for display
 * @var array $categories Category data [name => amount]
 * @var float $totalExpense Total expense amount
 * @var bool $hasData Whether there is data to display
 * @var int $chartHeight Chart height in pixels
 *
 * @author Mohsin Rafique <mohsin.rafique@gmail.com>
 * @since 1.0.0
 */

use yii\helpers\Html;

// Build container classes
$containerClasses = ['expenses-by-category-widget'];
if ($containerClass) {
    $containerClasses[] = $containerClass;
}

$chartId = 'categoryBarChart_' . $widgetId;
$categoryCount = count($categories);
?>

<!-- ============================================================== -->
<!-- Expenses by Category Widget                                    -->
<!-- ============================================================== -->
<div class="<?= implode(' ', $containerClasses) ?>" id="<?= Html::encode($widgetId) ?>">
    <div class="card h-100 shadow-sm">

        <!-- Card Header -->
        <div class="card-header d-flex align-items-center justify-content-between">
            <h3 class="card-title h6 mb-0">
                <?= Html::encode($title) ?>
            </h3>
            <span class="text-muted small">
                <?= Html::encode($periodLabel) ?>
            </span>
        </div>

        <!-- Card Body -->
        <div class="card-body">
            <?php if ($hasData): ?>
                <!-- Chart Container -->
                <div id="<?= Html::encode($chartId) ?>"
                    class="chart-container"
                    style="min-height: <?= $chartHeight ?>px;"
                    role="img"
                    aria-label="<?= Yii::t('app', 'Horizontal bar chart showing expenses by category') ?>">
                </div>
            <?php else: ?>
                <!-- Empty State -->
                <div class="text-center py-5">
                    <i class="bi bi-bar-chart-horizontal text-muted" style="font-size: 3rem;"></i>
                    <p class="text-muted mt-3 mb-0">
                        <?= Yii::t('app', 'No expense data for this period') ?>
                    </p>
                    <p class="text-muted small">
                        <?= Yii::t('app', 'Expenses will appear here once recorded') ?>
                    </p>
                </div>
            <?php endif ?>
        </div>

        <?php if ($hasData): ?>
            <!-- Card Footer with Summary -->
            <div class="card-footer bg-transparent py-2">
                <div class="d-flex justify-content-between align-items-center small">
                    <span class="text-muted">
                        <i class="bi bi-tags me-1"></i>
                        <?= Yii::t('app', '{count} categories', ['count' => $categoryCount]) ?>
                    </span>
                    <span class="fw-semibold">
                        <?= Yii::t('app', 'Total') ?>:
                        <span class="text-danger">
                            <?= Yii::$app->formatter->asDecimal($totalExpense, 0) ?>
                        </span>
                    </span>
                </div>
            </div>
        <?php endif ?>

    </div>
</div>
<!-- End Expenses by Category Widget -->
