<?php

/**
 * @link https://github.com/mohsin-rafique/expense-manager
 * @copyright Copyright (c) 2025 Mohsin Rafique
 * @license https://opensource.org/licenses/MIT MIT License
 */

/**
 * Fiscal Year Expense Summary Widget View
 *
 * Renders a monthly expense breakdown table with category filtering
 * and Excel export capabilities.
 *
 * @var yii\web\View $this
 * @var string $widgetId Unique widget identifier
 * @var string $title Widget title
 * @var string $subtitle Widget subtitle
 * @var string $fiscalYearLabel Fiscal year display label
 * @var string|null $containerClass Additional CSS classes
 * @var array $months Month range (ym => label)
 * @var array $categories Ordered categories (id => name)
 * @var array $pivot Expense data (month => category => amount)
 * @var array $totals Category totals
 * @var float $grandTotal Grand total amount
 * @var bool $enableExport Whether export is enabled
 * @var bool $enableFiltering Whether filtering is enabled
 * @var string $exportUrl Export URL
 *
 * @author Mohsin Rafique <mohsin.rafique@gmail.com>
 * @since 1.0.0
 */

use yii\helpers\Html;

// Build container classes
$containerClasses = ['fiscal-year-expense-widget'];
if ($containerClass) {
    $containerClasses[] = $containerClass;
}

// Count categories for colspan
$categoryCount = count($categories);
?>

<!-- ============================================================== -->
<!-- Fiscal Year Expense Summary Widget                             -->
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

    <!-- Main Card -->
    <div class="card shadow-sm">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h3 class="card-title h6 mb-0">
                <?= Yii::t('app', 'Expense Summary') ?>
                <small class="text-muted fw-normal">(<?= Html::encode($fiscalYearLabel) ?>)</small>
            </h3>

            <!-- Action Buttons -->
            <div class="d-flex gap-2 align-items-center">

                <?php if ($enableExport): ?>
                    <!-- Export Button -->
                    <?= Html::a(
                        '<i class="bi bi-file-earmark-excel me-1"></i>' . Yii::t('app', 'Export'),
                        $exportUrl,
                        ['class' => 'btn btn-sm btn-outline-success']
                    ) ?>
                <?php endif ?>

                <?php if ($enableFiltering && !empty($categories)): ?>
                    <!-- Category Filter Dropdown -->
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle"
                            type="button"
                            id="categoryFilterDropdown-<?= $widgetId ?>"
                            data-bs-toggle="dropdown"
                            data-bs-auto-close="outside"
                            aria-expanded="false">
                            <i class="bi bi-funnel me-1"></i>
                            <?= Yii::t('app', 'Filter') ?>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end p-3"
                            style="width: 280px; max-height: 350px; overflow-y: auto;">

                            <!-- Select/Deselect All -->
                            <div class="d-flex justify-content-between mb-2 pb-2 border-bottom">
                                <a href="#"
                                    class="text-primary small text-decoration-none"
                                    id="selectAllCategories-<?= $widgetId ?>">
                                    <?= Yii::t('app', 'Select All') ?>
                                </a>
                                <a href="#"
                                    class="text-danger small text-decoration-none"
                                    id="deselectAllCategories-<?= $widgetId ?>">
                                    <?= Yii::t('app', 'Deselect All') ?>
                                </a>
                            </div>

                            <!-- Category Checkboxes -->
                            <?php foreach ($categories as $catId => $catName): ?>
                                <div class="form-check mb-1">
                                    <input class="form-check-input category-checkbox"
                                        type="checkbox"
                                        value="<?= Html::encode($catId) ?>"
                                        id="cat_<?= $widgetId ?>_<?= $catId ?>"
                                        checked>
                                    <label class="form-check-label small"
                                        for="cat_<?= $widgetId ?>_<?= $catId ?>">
                                        <?= Html::encode($catName) ?>
                                    </label>
                                </div>
                            <?php endforeach ?>

                        </div>
                    </div>
                <?php endif ?>

            </div>
        </div>

        <div class="card-body">
            <?php if (empty($categories)): ?>
                <!-- Empty State -->
                <div class="text-center py-5">
                    <i class="bi bi-table text-muted" style="font-size: 3rem;"></i>
                    <p class="text-muted mt-3 mb-0">
                        <?= Yii::t('app', 'No expense data available for this fiscal year') ?>
                    </p>
                </div>
            <?php else: ?>
                <!-- Expense Table -->
                <div class="table-responsive">
                    <table class="table table-bordered table-hover text-center align-middle expense-summary-table mb-0">

                        <!-- Table Header -->
                        <thead class="table-light">
                            <tr>
                                <th class="text-start" style="min-width: 120px;">
                                    <?= Yii::t('app', 'Month') ?>
                                </th>
                                <?php foreach ($categories as $catId => $catName): ?>
                                    <th class="category-col" data-cat-id="<?= Html::encode($catId) ?>">
                                        <?= Html::encode($catName) ?>
                                    </th>
                                <?php endforeach ?>
                            </tr>
                        </thead>

                        <!-- Table Body -->
                        <tbody>
                            <?php foreach ($months as $ym => $label): ?>
                                <tr>
                                    <td class="text-start fw-medium">
                                        <?= Html::encode($label) ?>
                                    </td>
                                    <?php foreach ($categories as $catId => $catName): ?>
                                        <?php $value = $pivot[$ym][$catId] ?? 0; ?>
                                        <td class="category-col <?= $value > 0 ? '' : 'text-muted' ?>"
                                            data-cat-id="<?= Html::encode($catId) ?>">
                                            <?= $value > 0 ? Yii::$app->formatter->asDecimal($value, 0) : '-' ?>
                                        </td>
                                    <?php endforeach ?>
                                </tr>
                            <?php endforeach ?>
                        </tbody>

                        <!-- Table Footer -->
                        <tfoot>
                            <!-- Category Totals Row -->
                            <tr class="table-light fw-semibold">
                                <th class="text-start">
                                    <?= Yii::t('app', 'Category Totals') ?>
                                </th>
                                <?php foreach ($categories as $catId => $catName): ?>
                                    <th class="category-col" data-cat-id="<?= Html::encode($catId) ?>">
                                        <?= isset($totals[$catId]) ? Yii::$app->formatter->asDecimal($totals[$catId], 0) : '-' ?>
                                    </th>
                                <?php endforeach ?>
                            </tr>

                            <!-- Grand Total Row -->
                            <tr class="table-success fw-bold">
                                <th class="text-start">
                                    <?= Yii::t('app', 'Grand Total') ?>
                                </th>
                                <th class="text-start" colspan="<?= $categoryCount ?>">
                                    <?= Yii::$app->currency->format($grandTotal) ?>
                                </th>
                            </tr>
                        </tfoot>

                    </table>
                </div>

                <!-- Summary Footer -->
                <div class="mt-3 pt-3 border-top">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <small class="text-muted">
                                <i class="bi bi-calendar3 me-1"></i>
                                <?= Yii::t('app', '{count} months', ['count' => count($months)]) ?>
                            </small>
                        </div>
                        <div class="col-auto">
                            <small class="text-muted">
                                <i class="bi bi-tags me-1"></i>
                                <?= Yii::t('app', '{count} categories', ['count' => $categoryCount]) ?>
                            </small>
                        </div>
                        <div class="col text-end">
                            <small class="text-muted">
                                <i class="bi bi-clock-history me-1"></i>
                                <?= Yii::t('app', 'Updated: {time}', [
                                    'time' => Yii::$app->formatter->asDatetime(time(), 'short'),
                                ]) ?>
                            </small>
                        </div>
                    </div>
                </div>
            <?php endif ?>
        </div>
    </div>
</div>
<!-- End Fiscal Year Expense Summary Widget -->
