<?php

namespace app\modules\imports\controllers;

use app\components\Controller;
use app\components\jobs\Job;
use app\controllers\actions\OptionsAction;
use app\controllers\actions\TemplateAction;
use app\controllers\actions\UpdateAction;
use app\modules\imports\models\Import;
use Box\Spout\Common\Exception\IOException;
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Yii;
use yii\data\ActiveDataFilter;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use yii\rest\CreateAction;
use yii\rest\DeleteAction;
use yii\rest\IndexAction;
use yii\rest\ViewAction;
use yii\web\BadRequestHttpException;
use yii\web\HttpException;

class ManageController extends Controller
{
    /**
     * @return array
     */
    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator']['except'] = ['file'];
        $behaviors = array_merge($behaviors, [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['file'],
                        'roles' => ['?'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['template', 'options', 'index', 'view', 'create', 'update', 'delete'],
                        'roles' => ['@'],
                    ],
                ],
            ],
        ]);
        return $behaviors;
    }

    public function actions(): array
    {
        return [
            'template' => [
                'class' => TemplateAction::class,
                'entity' => 'import',
                'modelClass' => Import::class,
            ],
            'options' => [
                'class' => OptionsAction::class,
                'entity' => 'import',
                'modelClass' => Import::class,
            ],
            'index' => [
                'class' => IndexAction::class,
                'modelClass' => Import::class,
                'dataFilter' => [
                    'class' => ActiveDataFilter::class,
                    'searchModel' => Import::class
                ],
                'checkAccess' => [$this, 'checkAccess'],
            ],
            'create' => [
                'class' => CreateAction::class,
                'modelClass' => Import::class,
                'checkAccess' => [$this, 'checkAccess'],
                'scenario' => 'create',
            ],
            'update' => [
                'class' => UpdateAction::class,
                'modelClass' => Import::class,
                'checkAccess' => [$this, 'checkAccess'],
                'scenario' => 'create',
            ],
            'delete' => [
                'class' => DeleteAction::class,
                'modelClass' => Import::class,
                'checkAccess' => [$this, 'checkAccess'],
            ],
            'view' => [
                'class' => ViewAction::class,
                'modelClass' => Import::class,
                'checkAccess' => [$this, 'checkAccess'],
            ]
        ];
    }

    public function checkAccess($action, $model = null, $params = [])
    {
        // TODO implement checkAccess
    }

    public function actionFile()
    {
        $entity = Yii::$app->request->get('entity');
        if ($entity != '') {
            $module = Job::getModuleNameByEntity($entity);
            $model = "\app\modules\\$module\models\\" . ucfirst($entity);
            if (class_exists($model) && method_exists($model, 'entityFields')) {
                $path = Yii::getAlias("@app/web/data/import/");
                FileHelper::createDirectory($path);
                $file = $entity . '.xlsx';
                $writer = WriterEntityFactory::createXLSXWriter();
                try {
                    $writer->openToFile($path . $file);
                    $fields = $model::entityFields();
                    $writer->addRow(WriterEntityFactory::createRow([
                        WriterEntityFactory::createCell('Внимание!!! Не менять порядок столбцов и вводить данные строго по стандарту (пример в третьей строке). Импорт будет выполняться начиная с четвертой строки.')
                    ]));
                    $labels = [];
                    $examples = [];
                    foreach ($fields as $field => $fieldData) {
                        if (!ArrayHelper::keyExists('import', $fieldData) || $fieldData['import']) {
                            $labels[] = WriterEntityFactory::createCell($fieldData['title']);
                            $example = $fieldData['type'];
                            if (!ArrayHelper::keyExists('value', $fieldData) && $fieldData['type'] === 'array' && ArrayHelper::isIn($fieldData['format'], ['radio', 'select'])) {
                                $map = strtoupper($field) . '_MAP';
                                if (!ArrayHelper::keyExists('value', $fieldData) && is_array(constant("$model::$map"))) {
                                    $fieldData['value'] = constant("$model::$map");
                                }
                            }
                            if ($fieldData['type'] === 'array' && ArrayHelper::isIn($fieldData['format'], ['radio', 'select'])) {
                                $example = implode('|', array_keys($fieldData['value']));
                            }
                            $examples[] = WriterEntityFactory::createCell($example);
                        }
                    }
                    $writer->addRow(WriterEntityFactory::createRow($labels));
                    $writer->addRow(WriterEntityFactory::createRow($examples));
                    $writer->close();
                    return \Yii::$app->response->sendFile($path . $file);
                } catch (IOException $e) {
                    return ['error' => 1, 'message' => 'file not found'];
                }
            }
        }
        return ['error' => 1, 'message' => 'entity not found'];
    }
}
