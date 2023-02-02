<?php

namespace app\modules\imports\components\behaviors;

use app\components\jobs\ImportEntityJob;
use Yii;
use yii\base\Behavior;
use yii\base\Event;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;

/**
 * Class ImportBehavior
 */
class ImportBehavior extends Behavior
{
    public $type = 'Import';

    /**
     * @inheritdoc
     */
    public function events(): array
    {
        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'putQueue',
            ActiveRecord::EVENT_BEFORE_DELETE => 'delQueue',
        ];
    }


    /**
     * @param Event $event
     * @throws InvalidConfigException
     */
    public function putQueue(Event $event)
    {
        $job = Yii::createObject([
            'class' => ImportEntityJob::class,
            'taskId' => $event->sender->id,
            'entity' => $event->sender->entity
        ]);
        $this->owner->save();
        Yii::$app->queue->push($job);
    }

    /**
     * @param Event $event
     */
    public function delQueue(Event $event)
    {
        //TODO:: удалить
    }

}