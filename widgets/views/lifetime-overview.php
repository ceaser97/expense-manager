<?php

/**
 * @link https://github.com/mohsin-rafique/expense-manager
 * @copyright Copyright (c) 2025 Mohsin Rafique
 * @license https://opensource.org/licenses/MIT MIT License
 */

/**
 * Lifetime Overview Widget View
 *
 * Renders the lifetime financial overview panel with three key metrics:
 * - Cumulative Gross Revenue
 * - Aggregate Operating Expenditure
 * - Net Financial Position
 *
 * @var yii\web\View $this
 * @var array $metrics Financial metrics data
 * @var array $config Metric configuration
 * @var bool $showTrendIndicators Whether to show trend arrows
 * @var string|null $containerClass Additional CSS classes
 * @var string $title Widget title
 * @var string $subtitle Widget subtitle
 * @var string $widgetId Unique widget identifier
 *
 * @author Mohsin Rafique <mohsin.rafique@gmail.com>
 * @since 1.0.0
 */

use app\widgets\LifetimeOverviewWidget;
use yii\helpers\Html;

// Build container classes
$containerClasses = ['lifetime-overview-widget'];
if ($containerClass) {
    $containerClasses[] = $containerClass;
}
?>

<!-- ============================================================== -->
<!-- Lifetime Financial Overview Widget                             -->
<!-- ============================================================== -->
<div class="<?= implode(' ', $containerClasses) ?>" id="<?= Html::encode($widgetId) ?>">

    <!-- Widget Header -->
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h2 class="h5 mb-1"><?= Html::encode($title) ?></h2>
            <p class="text-muted small mb-0"><?= Html::encode($subtitle) ?></p>
        </div>
        <div class="text-end">
            <span class="badge bg-light text-dark">
                <i class="bi bi-clock me-1"></i>
                <?= Yii::t('app', 'All Time') ?>
            </span>
        </div>
    </div>

    <!-- Metrics Cards -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="row g-0">

                <?php foreach ($metrics as $key => $metric): ?>
                    <?php
                    $metricConf = $config[$key];
                    $trendIndicator = LifetimeOverviewWidget::getTrendIndicator($metric['trend']);
                    $isNetNegative = ($key === 'netPosition' && ($metric['isNegative'] ?? false));
                    $isLastItem = ($key === array_key_last($metrics));
                    ?>

                    <div class="col-md-4 <?= !$isLastItem ? 'border-end' : '' ?>">
                        <div class="p-4 h-100">

                            <!-- Metric Header -->
                            <div class="d-flex align-items-start justify-content-between mb-3">
                                <h6 class="text-muted text-uppercase fw-semibold mb-0" style="font-size: 0.75rem; letter-spacing: 0.05em;">
                                    <?= Html::encode($metricConf['label']) ?>
                                </h6>
                                <?php if ($showTrendIndicators): ?>
                                    <span class="<?= $trendIndicator['class'] ?>"
                                        title="<?= Html::encode($trendIndicator['label']) ?>"
                                        data-bs-toggle="tooltip">
                                        <i class="<?= $trendIndicator['icon'] ?>"></i>
                                    </span>
                                <?php endif ?>
                            </div>

                            <!-- Metric Value -->
                            <div class="d-flex align-items-center">
                                <!-- Icon -->
                                <div class="flex-shrink-0 me-3">
                                    <span class="d-inline-flex align-items-center justify-content-center rounded-circle <?= $metricConf['iconColor'] ?>"
                                        style="width: 48px; height: 48px; background-color: currentColor; opacity: 0.1;">
                                    </span>
                                    <i class="<?= Html::encode($metricConf['icon']) ?> <?= $metricConf['iconColor'] ?> position-absolute"
                                        style="font-size: 1.5rem; margin-left: -36px; margin-top: 4px;"></i>
                                </div>

                                <!-- Value Display -->
                                <div class="flex-grow-1">
                                    <h3 class="mb-0 <?= $isNetNegative ? 'text-danger' : '' ?>" style="font-size: 1.5rem; font-weight: 600;">
                                        <?php if ($isNetNegative): ?>
                                            <span class="text-danger">-</span>
                                        <?php endif ?>
                                        <span class="metric-value" data-value="<?= Html::encode($metric['value']) ?>">
                                            <?= Html::encode($metric['formatted']) ?>
                                        </span>
                                    </h3>

                                    <!-- Profit Margin (only for Net Position) -->
                                    <?php if ($key === 'netPosition' && isset($metric['profitMargin'])): ?>
                                        <div class="mt-1">
                                            <small class="text-muted">
                                                <?= Yii::t('app', 'Profit Margin') ?>:
                                                <span class="fw-semibold <?= $metric['profitMargin'] >= 0 ? 'text-success' : 'text-danger' ?>">
                                                    <?= $metric['profitMargin'] >= 0 ? '+' : '' ?><?= $metric['profitMargin'] ?>%
                                                </span>
                                            </small>
                                        </div>
                                    <?php endif ?>
                                </div>
                            </div>

                            <!-- Description (hidden on mobile) -->
                            <p class="text-muted small mb-0 mt-3 d-none d-lg-block">
                                <?= Html::encode($metricConf['description']) ?>
                            </p>
                        </div>
                    </div>
                <?php endforeach ?>
            </div>
        </div>

        <!-- Card Footer -->
        <div class="card-footer bg-transparent py-2">
            <div class="row align-items-center">
                <div class="col-auto">
                    <small class="text-muted">
                        <i class="bi bi-clock-history me-1"></i>
                        <?= Yii::t('app', 'Last updated: {time}', [
                            'time' => Yii::$app->formatter->asDatetime(time(), 'short'),
                        ]) ?>
                    </small>
                </div>
                <div class="col text-end">
                    <small class="text-muted">
                        <i class="bi bi-person me-1"></i>
                        <?= Yii::t('app', 'Account Metrics') ?>
                    </small>
                </div>
            </div>
        </div>

    </div>
</div>
<!-- End Lifetime Overview Widget -->
