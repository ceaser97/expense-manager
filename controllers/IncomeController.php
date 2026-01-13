<?php

/**
 * @link https://github.com/mohsin-rafique/expense-manager
 * @copyright Copyright (c) 2025 Mohsin Rafique
 * @license https://opensource.org/licenses/MIT MIT License
 */

namespace app\controllers;

use Yii;
use app\models\Income;
use app\models\IncomeSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

/**
 * IncomeController implements the CRUD actions for Income model.
 *
 * Provides comprehensive income management including:
 * - List view with advanced filtering
 * - AJAX-based CRUD operations via modals
 * - File attachment support
 * - Excel export with formatting
 * - Statistics and summary views
 *
 * @author Mohsin Rafique <mohsin.rafique@gmail.com>
 * @since 1.0.0
 */
class IncomeController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                    'bulk-delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all incomes for the current user
     *
     * @return string
     */
    public function actionIndex(): string
    {
        $searchModel = new IncomeSearch();

        // Set default date range to current month if not specified
        $params = Yii::$app->request->queryParams;
        if (empty($params['IncomeSearch']['start_date']) && empty($params['IncomeSearch']['end_date'])) {
            $searchModel->start_date = date('Y-m-01');
            $searchModel->end_date = date('Y-m-t');
        }

        $dataProvider = $searchModel->search($params);

        // Get statistics for the current filter
        $statistics = $searchModel->getStatistics($dataProvider);

        // Get category breakdown for chart
        $categoryBreakdown = $searchModel->getCategoryBreakdown();

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'statistics' => $statistics,
            'categoryBreakdown' => $categoryBreakdown,
        ]);
    }

    /**
     * Displays a single expense
     *
     * @param int $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView(int $id): string
    {
        $model = $this->findModel($id);

        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('view', ['model' => $model]);
        }

        return $this->render('view', ['model' => $model]);
    }

    /**
     * Creates a new income.
     *
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new Income();
        $model->user_id = Yii::$app->user->id;
        $model->entry_date = date('Y-m-d');

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            return $this->processForm($model);
        }

        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('_form', ['model' => $model]);
        }
    }

    /**
     *  Updates an existing income
     *
     * @param int $id
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate(int $id)
    {
        $model = $this->findModel($id);
        $oldFile = $model->getAbsoluteFilePath();
        $oldFileName = $model->filename;
        $oldFilePath = $model->filepath;

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            return $this->processForm($model, $oldFile, $oldFileName, $oldFilePath);
        }

        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('_form', ['model' => $model]);
        }

        return $this->renderAjax('update', ['model' => $model]);
    }

    /**
     * Process form submission for create/update
     *
     * @param Income $model
     * @param string|null $oldFile Old file path for update
     * @param string|null $oldFileName Old file name for update
     * @param string|null $oldFilePath Old file path for update
     *
     * @return array
     */
    protected function processForm(
        Income $model,
        ?string $oldFile = null,
        ?string $oldFileName = null,
        ?string $oldFilePath = null
    ) {
        Yii::$app->response->format = Response::FORMAT_JSON;

        // Handle file upload
        $file = UploadedFile::getInstance($model, 'myFile');

        if ($file !== null) {
            $model->filename = $file->name;
            $ext = $file->extension;
            $uploadPath = Yii::$app->params['uploadPath'] ?? 'uploads/incomes/';
            $model->filepath = $uploadPath . Yii::$app->security->generateRandomString(16) . '.' . $ext;
        } elseif ($oldFileName !== null) {
            // Keep old file if no new file uploaded
            $model->filename = $oldFileName;
            $model->filepath = $oldFilePath;
        }

        if ($model->validate() && $model->save(false)) {
            // Save uploaded file
            if ($file !== null) {
                $savePath = Yii::getAlias('@webroot/' . $model->filepath);
                $saveDir = dirname($savePath);

                if (!is_dir($saveDir)) {
                    mkdir($saveDir, 0755, true);
                }

                $file->saveAs($savePath);

                // Delete old file if exists
                if ($oldFile !== null && file_exists($oldFile)) {
                    @unlink($oldFile);
                }
            }

            return [
                'status' => 'success',
                'type' => 'success',
                'id' => $model->id,
                'message' => $model->isNewRecord
                    ? Yii::t('app', 'Income created successfully.')
                    : Yii::t('app', 'Income updated successfully.'),
            ];
        }

        return [
            'status' => 'error',
            'type' => 'danger',
            'message' => Yii::t('app', 'Failed to save income.'),
            'errors' => $model->errors,
        ];
    }

    /**
     * Deletes an existing Income model.
     *
     * @param int $id
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionDelete(int $id): Response
    {
        $model = $this->findModel($id);

        try {
            // Delete attachment file
            $model->deleteAttachment();

            if ($model->delete()) {
                if (Yii::$app->request->isAjax) {
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    return $this->asJson([
                        'success' => true,
                        'message' => Yii::t('app', 'Income deleted successfully.'),
                    ]);
                }
                Yii::$app->session->setFlash('success', Yii::t('app', 'Income deleted successfully.'));
            }
        } catch (\Exception $e) {
            Yii::error('Failed to delete income: ' . $e->getMessage(), __METHOD__);

            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return $this->asJson([
                    'success' => false,
                    'message' => Yii::t('app', 'Failed to delete income.'),
                ]);
            }
            Yii::$app->session->setFlash('error', Yii::t('app', 'Failed to delete income.'));
        }

        return $this->redirect(['index']);
    }

    /**
     * Bulk delete multiple income records.
     *
     * @return Response
     */
    public function actionBulkDelete(): Response
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $ids = Yii::$app->request->post('ids', []);

        if (empty($ids)) {
            return $this->asJson([
                'success' => false,
                'message' => Yii::t('app', 'No records selected.'),
            ]);
        }

        $deleted = 0;
        $failed = 0;

        foreach ($ids as $id) {
            try {
                $model = $this->findModel((int) $id);
                $model->deleteAttachment();
                if ($model->delete()) {
                    $deleted++;
                } else {
                    $failed++;
                }
            } catch (\Exception $e) {
                $failed++;
                Yii::error('Bulk delete failed for ID ' . $id . ': ' . $e->getMessage(), __METHOD__);
            }
        }

        return $this->asJson([
            'success' => $failed === 0,
            'message' => Yii::t('app', '{deleted} record(s) deleted, {failed} failed.', [
                'deleted' => $deleted,
                'failed' => $failed,
            ]),
        ]);
    }

    /**
     * Exports income data to Excel.
     *
     * @return Response
     */
    public function actionExport(): Response
    {
        $params = Yii::$app->request->queryParams['IncomeSearch'] ?? [];

        $searchModel = new IncomeSearch();
        $dataProvider = $searchModel->search(['IncomeSearch' => $params], true);
        $incomes = $dataProvider->getModels();

        return $this->exportToExcel($incomes, $searchModel);
    }

    /**
     * Generate Excel file from income data.
     *
     * @param array $incomes Income models to export
     * @param IncomeSearch $searchModel Search model with filter info
     * @return Response
     */
    protected function exportToExcel(array $incomes, IncomeSearch $searchModel): Response
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Income Report');

        // Report header
        $dateRange = '';
        if ($searchModel->start_date && $searchModel->end_date) {
            $dateRange = Yii::$app->formatter->asDate($searchModel->start_date) . ' - ' .
                Yii::$app->formatter->asDate($searchModel->end_date);
        }

        $sheet->setCellValue('A1', 'Income Report');
        $sheet->setCellValue('A2', 'Generated: ' . Yii::$app->formatter->asDatetime(time()));
        if ($dateRange) {
            $sheet->setCellValue('A3', 'Period: ' . $dateRange);
        }

        $sheet->mergeCells('A1:F1');
        $sheet->mergeCells('A2:F2');
        $sheet->mergeCells('A3:F3');

        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A2:A3')->getFont()->setItalic(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('666666'));

        // Column headers (row 5)
        $headerRow = 5;
        $headers = ['#', 'Category', 'Date', 'Reference', 'Description', 'Amount'];

        foreach ($headers as $col => $header) {
            $sheet->setCellValueByColumnAndRow($col + 1, $headerRow, $header);
        }

        // Header styling
        $headerStyle = [
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'color' => ['argb' => '16a34a'],
            ],
            'font' => [
                'bold' => true,
                'color' => ['argb' => 'FFFFFF'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ];
        $sheet->getStyle('A' . $headerRow . ':F' . $headerRow)->applyFromArray($headerStyle);

        // Data rows
        $row = $headerRow + 1;
        $totalAmount = 0;

        foreach ($incomes as $index => $income) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $income->incomeCategory->name ?? 'N/A');
            $sheet->setCellValue('C' . $row, $income->entry_date);
            $sheet->setCellValue('D' . $row, $income->reference ?? '');
            $sheet->setCellValue('E' . $row, $income->description ?? '');
            $sheet->setCellValue('F' . $row, (float) $income->amount);

            $totalAmount += (float) $income->amount;
            $row++;
        }

        // Total row
        $sheet->setCellValue('E' . $row, 'Total:');
        $sheet->setCellValue('F' . $row, $totalAmount);
        $sheet->getStyle('E' . $row . ':F' . $row)->getFont()->setBold(true);
        $sheet->getStyle('E' . $row . ':F' . $row)->getBorders()->getTop()->setBorderStyle(Border::BORDER_THIN);

        // Format amount column
        $sheet->getStyle('F' . ($headerRow + 1) . ':F' . $row)
            ->getNumberFormat()
            ->setFormatCode('#,##0.00');
        $sheet->getStyle('F' . ($headerRow + 1) . ':F' . $row)
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        // Center ID column
        $sheet->getStyle('A' . $headerRow . ':A' . $row)
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Auto-fit columns
        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Output
        $filename = 'income-report-' . date('Y-m-d-His') . '.xlsx';

        Yii::$app->response->format = Response::FORMAT_RAW;
        Yii::$app->response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        Yii::$app->response->headers->set('Content-Disposition', 'attachment;filename="' . $filename . '"');
        Yii::$app->response->headers->set('Cache-Control', 'max-age=0');

        $writer = new Xlsx($spreadsheet);

        ob_start();
        $writer->save('php://output');
        Yii::$app->response->content = ob_get_clean();

        return Yii::$app->response;
    }

    /**
     * Get summary data for dashboard widgets.
     *
     * @return Response
     */
    public function actionSummary(): Response
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $currentMonth = date('Y-m');
        $lastMonth = date('Y-m', strtotime('-1 month'));

        $currentMonthTotal = Income::getTotalIncome(
            null,
            date('Y-m-01'),
            date('Y-m-t')
        );

        $lastMonthTotal = Income::getTotalIncome(
            null,
            date('Y-m-01', strtotime('-1 month')),
            date('Y-m-t', strtotime('-1 month'))
        );

        $change = $lastMonthTotal > 0
            ? (($currentMonthTotal - $lastMonthTotal) / $lastMonthTotal) * 100
            : 0;

        return $this->asJson([
            'currentMonth' => $currentMonthTotal,
            'lastMonth' => $lastMonthTotal,
            'change' => round($change, 1),
            'count' => Income::getIncomeCount(null, date('Y-m-01'), date('Y-m-t')),
        ]);
    }

    /**
     * Finds the Income model based on its primary key value.
     *
     * @param int $id
     * @return Income
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel(int $id): Income
    {
        $model = Income::find()
            ->where([
                'id' => $id,
                'user_id' => Yii::$app->user->id,
            ])
            ->one();

        if ($model === null) {
            throw new NotFoundHttpException(Yii::t('app', 'The requested income record does not exist.'));
        }

        return $model;
    }
}
