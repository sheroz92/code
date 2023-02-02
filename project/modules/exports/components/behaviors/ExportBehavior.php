<?php

namespace app\modules\exports\components\behaviors;

use app\components\jobs\ExportEntityJob;
use app\modules\exports\models\Export;
use Yii;
use yii\base\Behavior;
use yii\base\Event;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;

/**
 * Class ExportBehavior
 */
class ExportBehavior extends Behavior
{
    public $type = 'Export';

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
        if ($this->owner->type == Export::TYPE_ENTITY) {
            $job = Yii::createObject([
                'class' => ExportEntityJob::class,
                'taskId' => $event->sender->id,
                'entity' => $event->sender->entity,
                'filter' => $event->sender->filter,
            ]);
        }
        $this->owner->name = $this->type . ": " . gmdate("Y-m-d H:i:s") . " {$event->sender->entity}";
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