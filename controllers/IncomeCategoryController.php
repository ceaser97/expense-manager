<?php

/**
 * @link https://github.com/mohsin-rafique/expense-manager
 * @copyright Copyright (c) 2025 Mohsin Rafique
 * @license https://opensource.org/licenses/MIT MIT License
 */

namespace app\controllers;

use Yii;
use app\models\IncomeCategory;
use app\models\IncomeCategorySearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * IncomeCategoryController implements the CRUD actions for IncomeCategory model.
 *
 * This controller handles all income category management operations including:
 * - Listing categories with search and pagination
 * - Creating new categories via AJAX modal
 * - Viewing category details
 * - Updating existing categories
 * - Deleting categories with validation
 * - Bulk operations
 *
 * ## Access Control
 *
 * All actions require authentication. Users can only access their own categories.
 *
 * ## AJAX Support
 *
 * Create, Update, View, and Delete actions support AJAX requests for modal dialogs.
 * Successful AJAX operations return JSON responses for client-side handling.
 *
 * @author Mohsin Rafique <mohsin.rafique@gmail.com>
 * @since 1.0.0
 */
class IncomeCategoryController extends Controller
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
     * Lists all IncomeCategory models for the authenticated user.
     *
     * Provides a paginated, searchable grid view of income categories.
     * Supports PJAX for seamless updates without full page reload.
     *
     * ## Features
     * - Search by category name
     * - Sortable columns
     * - Configurable pagination
     * - PJAX support
     *
     * @return string The rendered index view
     */
    public function actionIndex(): string
    {
        $searchModel = new IncomeCategorySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        // Ensure user can only see their own categories
        $dataProvider->query->andWhere(['user_id' => Yii::$app->user->id]);

        // Configure pagination
        $dataProvider->pagination->pageSize = Yii::$app->request->get('per-page', 10);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single IncomeCategory model.
     *
     * Supports both regular and AJAX requests. For AJAX requests,
     * renders the view partial for modal display.
     *
     * @param int $id The category ID
     * @return string|Response The rendered view or JSON response
     * @throws NotFoundHttpException if the model cannot be found or doesn't belong to user
     */
    public function actionView(int $id)
    {
        $model = $this->findModel($id);

        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('view', [
                'model' => $model,
            ]);
        }

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new IncomeCategory model.
     *
     * Handles both regular form submission and AJAX modal submission.
     * On successful creation via AJAX, returns JSON with success status.
     *
     * ## AJAX Response Format
     * ```json
     * {
     *     "success": true,
     *     "message": "Category created successfully",
     *     "id": 123
     * }
     * ```
     *
     * @return string|Response The rendered form or redirect/JSON response
     */
    public function actionCreate()
    {
        $model = new IncomeCategory();
        $model->user_id = Yii::$app->user->id;
        $model->status = IncomeCategory::STATUS_ACTIVE;

        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                if (Yii::$app->request->isAjax) {
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    return [
                        'success' => true,
                        'message' => Yii::t('app', 'Income category created successfully.'),
                        'id' => $model->id,
                    ];
                }

                Yii::$app->session->setFlash('success', Yii::t('app', 'Income category created successfully.'));
                return $this->redirect(['index']);
            }

            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return [
                    'success' => false,
                    'message' => Yii::t('app', 'Failed to create category.'),
                    'errors' => $model->errors,
                ];
            }
        }

        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('_form', [
                'model' => $model,
            ]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing IncomeCategory model.
     *
     * Handles both regular form submission and AJAX modal submission.
     * Validates that the category belongs to the authenticated user.
     *
     * @param int $id The category ID
     * @return string|Response The rendered form or redirect/JSON response
     * @throws NotFoundHttpException if the model cannot be found or doesn't belong to user
     */
    public function actionUpdate(int $id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                if (Yii::$app->request->isAjax) {
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    return [
                        'success' => true,
                        'message' => Yii::t('app', 'Income category updated successfully.'),
                    ];
                }

                Yii::$app->session->setFlash('success', Yii::t('app', 'Income category updated successfully.'));
                return $this->redirect(['index']);
            }

            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return [
                    'success' => false,
                    'message' => Yii::t('app', 'Failed to update category.'),
                    'errors' => $model->errors,
                ];
            }
        }

        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('_form', [
                'model' => $model,
            ]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing IncomeCategory model.
     *
     * Performs soft delete by setting status to inactive if the category
     * has associated income records. Otherwise, performs hard delete.
     *
     * @param int $id The category ID
     * @return Response Redirect to index or JSON response for AJAX
     * @throws NotFoundHttpException if the model cannot be found or doesn't belong to user
     */
    public function actionDelete(int $id)
    {
        $model = $this->findModel($id);

        try {
            // Check if category has associated income records
            $hasIncomes = $model->getIncomes()->exists();

            if ($hasIncomes) {
                // Soft delete - set to inactive
                $model->status = IncomeCategory::STATUS_INACTIVE;
                $model->save(false, ['status']);
                $message = Yii::t('app', 'Category deactivated (has associated records).');
            } else {
                // Hard delete
                $model->delete();
                $message = Yii::t('app', 'Category deleted successfully.');
            }

            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return [
                    'success' => true,
                    'message' => $message,
                ];
            }

            Yii::$app->session->setFlash('success', $message);
        } catch (\Exception $e) {
            Yii::error('Failed to delete income category: ' . $e->getMessage(), __METHOD__);

            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return [
                    'success' => false,
                    'message' => Yii::t('app', 'Failed to delete category. Please try again.'),
                ];
            }

            Yii::$app->session->setFlash('error', Yii::t('app', 'Failed to delete category.'));
        }

        return $this->redirect(['index']);
    }

    /**
     * Bulk delete multiple categories.
     *
     * Accepts an array of category IDs and deletes them.
     * Only deletes categories belonging to the authenticated user.
     *
     * @return Response JSON response with operation results
     */
    public function actionBulkDelete(): Response
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $ids = Yii::$app->request->post('ids', []);

        if (empty($ids)) {
            return [
                'success' => false,
                'message' => Yii::t('app', 'No categories selected.'),
            ];
        }

        $deleted = 0;
        $deactivated = 0;
        $failed = 0;

        foreach ($ids as $id) {
            try {
                $model = $this->findModel((int) $id);

                if ($model->getIncomes()->exists()) {
                    $model->status = IncomeCategory::STATUS_INACTIVE;
                    $model->save(false, ['status']);
                    $deactivated++;
                } else {
                    $model->delete();
                    $deleted++;
                }
            } catch (\Exception $e) {
                $failed++;
                Yii::error('Bulk delete failed for ID ' . $id . ': ' . $e->getMessage(), __METHOD__);
            }
        }

        $messages = [];
        if ($deleted > 0) {
            $messages[] = Yii::t('app', '{count} category(s) deleted.', ['count' => $deleted]);
        }
        if ($deactivated > 0) {
            $messages[] = Yii::t('app', '{count} category(s) deactivated.', ['count' => $deactivated]);
        }
        if ($failed > 0) {
            $messages[] = Yii::t('app', '{count} category(s) failed to process.', ['count' => $failed]);
        }

        return [
            'success' => $failed === 0,
            'message' => implode(' ', $messages),
            'deleted' => $deleted,
            'deactivated' => $deactivated,
            'failed' => $failed,
        ];
    }

    /**
     * Exports income categories to CSV format.
     *
     * Generates a downloadable CSV file containing all user's categories.
     *
     * @return Response The CSV file download response
     */
    public function actionExport(): Response
    {
        $categories = IncomeCategory::find()
            ->where(['user_id' => Yii::$app->user->id])
            ->orderBy(['name' => SORT_ASC])
            ->all();

        $csv = "ID,Name,Description,Status,Created At,Updated At\n";

        foreach ($categories as $category) {
            $csv .= sprintf(
                "%d,\"%s\",\"%s\",%s,%s,%s\n",
                $category->id,
                str_replace('"', '""', $category->name),
                str_replace('"', '""', $category->description ?? ''),
                $category->status ? 'Active' : 'Inactive',
                $category->created_at,
                $category->updated_at
            );
        }

        $filename = 'income-category-' . date('Y-m-d') . '.csv';

        return Yii::$app->response->sendContentAsFile(
            $csv,
            $filename,
            ['mimeType' => 'text/csv']
        );
    }

    /**
     * Finds the IncomeCategory model based on its primary key value.
     *
     * Ensures the category belongs to the authenticated user.
     *
     * @param int $id The category ID
     * @return IncomeCategory The loaded model
     * @throws NotFoundHttpException if the model cannot be found or doesn't belong to user
     */
    protected function findModel(int $id): IncomeCategory
    {
        $model = IncomeCategory::find()
            ->where([
                'id' => $id,
                'user_id' => Yii::$app->user->id,
            ])
            ->one();

        if ($model === null) {
            throw new NotFoundHttpException(Yii::t('app', 'The requested category does not exist.'));
        }

        return $model;
    }
}
