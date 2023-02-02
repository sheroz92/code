<?php

namespace app\components\jobs;

use Box\Spout\Common\Type;
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Box\Spout\Writer\CSV\Writer as CSVWriter;
use Box\Spout\Writer\XLSX\Writer as XLSXWriter;
use app\modules\exports\models\Export;
use Yii;
use yii\base\BaseObject;
use yii\base\Exception;
use yii\db\ActiveQuery;
use yii\helpers\FileHelper;
use yii\queue\JobInterface;
use yii\queue\Queue;

/**
 * Class ReportJob
 * @package common\components\jobs
 *
 * @property-read string $fileName
 * @property-read string $filePath
 * @property-read string $fileUrl
 */
class Job extends BaseObject implements JobInterface
{
    /** @var string */
    public $type = 'analytics';

    /** @var string */
    public $format;

    /** @var string */
    public $entity;

    /** @var array */
    public $filter;

    /** @var string */
    public $taskId;

    /** @var ActiveQuery */
    public $query;

    /** @var Export */
    public $task;

    /** @var string */
    public $model;


    /** @var CSVWriter|XLSXWriter $writer */
    protected $writer;

    /**
     * @param $attributes array
     * @return int
     */
    public function updateTask($attributes)
    {
        return $this->task->updateAttributes($attributes);
    }

    /**
     * @return string
     * @throws Exception
     */
    public function createFile()
    {
        FileHelper::createDirectory($this->getFilePath());
        return Yii::getAlias($this->getFilePath() . '/' . $this->getFileName());
    }

    /**
     * @param int $percent
     * @param int $status
     * @return int
     */
    public function taskCompleted($percent = 100, $status = Export::STATUS_COMPLETED)
    {
        $attributes = [
            'status' => $status,
            'file' => $this->getFileUrl(),
            'completed_at' => gmdate("Y-m-d H:i:s")
        ];
        if ($percent) {
            $attributes['percent'] = $percent;
        }
        return $this->updateTask($attributes);
    }

    /**
     * @return string
     */
    public function getFilePath(): string
    {
        $folder = $this->type == 'report' ? 'reports' : $this->entity;
        return Yii::getAlias("@app/web/data/{$folder}");
    }

    /**
     * @return string
     */
    public function getFileUrl(): string
    {
        $folder = $this->type == 'report' ? 'reports' : $this->entity;
        return "/data/{$folder}/{$this->getFileName()}";
    }

    /**
     * @return string
     */
    public function getFileName(): string
    {
        static $filename;
        if (!$filename) {
            if ($this->type == 'report') {
                $filename = "{$this->entity}_" . uniqid('', false) . '.xlsx';
            } else {
                $filename = uniqid('', false) . '.' . $this->format;
            }
        }
        return $filename;
    }

    /**
     * @param Queue $queue which pushed and is handling the job
     * @return void result of the job execution
     */
    public function execute($queue)
    {
        $this->task = Export::findOne($this->taskId);
        $this->task->status = Export::STATUS_PROGRESS;
        $this->task->save();
        $module = self::getModuleNameByEntity($this->entity);
        $this->model = "\app\modules\\$module\models\\".ucfirst($this->entity);
        $this->format = $this->task->format;
        $this->writer = $this->format == Type::CSV ? WriterEntityFactory::createCSVWriter() : WriterEntityFactory::createXLSXWriter();
    }

    public static function getModuleNameByEntity($entity){
        $singular = $entity;
        $last_letter = strtolower($singular[strlen($singular)-1]);
        switch($last_letter) {
            case 'y':
                $module = substr($singular,0,-1).'ies';
                break;
            case 's':
                $module = $singular.'es';
                break;
            default:
                $module =  $singular.'s';
                break;
        }
        return $module;
    }

}
