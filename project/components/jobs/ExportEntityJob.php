<?php

namespace app\components\jobs;

use app\modules\exports\models\Export;
use Exception;
use Yii;
use yii\base\InvalidConfigException;
use yii\data\ActiveDataFilter;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\queue\Queue;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;


/**
 * Class ExportEntityJob
 *
 * @property-read string $fileName
 * @property-read string $filePath
 * @property-read string $fileUrl
 * @property-read string $modelSchema
 * @property-read array|string[] $fields
 * @property-read array|string[] $labelsCells
 * @property-read array|string[] $labels
 */
class ExportEntityJob extends Job
{
    private const BATCH_SIZE = 1000;

    /** @var string */
    public $searchQueryMethod = 'searchQuery';

    /**
     * @inheritDoc
     */
    public static function recordFilter($record, $fields)
    {
        $newRecord = [];
        foreach ($fields as $field) {
            $newRecord[$field] = ArrayHelper::keyExists($field, $record) ? $record[$field] : '';
        }
        return $newRecord;
    }

    /**
     * @param Queue $queue
     * @return void
     * @throws Exception
     */
    public function execute($queue)
    {
        parent::execute($queue);

        $fields = $this->getFields();
        try {
            $this->query = $this->prepareQuery();
            $this->writer->openToFile($this->createFile());
            if ($this->query->count()) {
                $this->writer->addRow(WriterEntityFactory::createRow($this->getLabelsCells()));
                $i = 0;
                foreach ($this->query->batch(self::BATCH_SIZE) as $batch) {
                    /** @var ActiveRecord $record */
                    $rows = [];
                    foreach ($batch as $record) {
                        $record->scenario = 'export';
                        $rows[] = WriterEntityFactory::createRowFromArray(self::recordFilter($record->toArray(), $fields));
                        $i++;
                    }
                    $this->writer->addRows($rows);
                    $this->updateTask(['percent' => round($i / $this->query->count() * 100)]);
                }
            }
            $this->writer->close();
            $this->taskCompleted(0);
        } catch (Exception $e) {
            Yii::warning($e->getTraceAsString());
            $this->updateTask(['status' => Export::STATUS_ERROR]);
        }
    }

    /**
     * @return ActiveQuery
     * @throws InvalidConfigException
     */
    private function prepareQuery(): ActiveQuery
    {
        /** @var ActiveRecord $model */
        $model = $this->model;
        $query = $model::find();
        if (!empty($this->filter)) {
            $dataFilter = Yii::createObject(ActiveDataFilter::class);
            $dataFilter->filter = $this->filter;
            $dataFilter->setSearchModel($model);
            $filter = $dataFilter->build();
            if ($filter) {
                $query->andWhere($filter);
            }
        }
        if (method_exists($model, $this->searchQueryMethod)) {
            $query = $model::{$this->searchQueryMethod}($query, ['filter' => $this->filter]);
        }

        return $query;
    }

    /**
     * @return string[]
     * @throws Exception
     */
    private function getLabelsCells(): array
    {
        $properties = $this->getModelSchema();
        $labels = [];
        foreach (array_keys($properties) as $fieldName) {
            $labels[] = WriterEntityFactory::createCell(
                ArrayHelper::getValue($properties, $fieldName . '.title')
            );
        }
        return $labels;
    }

    /**
     * @return string[]
     * @throws Exception
     */
    private function getFields(): array
    {
        $properties = $this->getModelSchema();
        $fields = [];
        foreach (array_keys($properties) as $fieldName) {
            $fields[] = $fieldName;
        }
        return $fields;
    }

    /**
     * @return array
     * @throws Exception
     */
    private function getModelSchema(): array
    {echo $this->model;
        return $this->model::entityFields();
    }

}
