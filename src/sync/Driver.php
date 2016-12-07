<?php

namespace zhuravljov\yii\queue\sync;

use Yii;
use yii\base\Application;
use zhuravljov\yii\queue\Driver as BaseDriver;

/**
 * Class SyncDriver
 *
 * @author Roman Zhuravlev <zhuravljov@gmail.com>
 */
class Driver extends BaseDriver
{
    /**
     * @var boolean
     */
    public $handle = false;
    /**
     * @var array
     */
    private $_messages = [];

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if ($this->handle) {
            Yii::$app->on(Application::EVENT_AFTER_REQUEST, function () {
                ob_start();
                while (($message = array_shift($this->_messages)) !== null) {
                    $this->getQueue()->run($this->unserialize($message));
                }
                ob_clean();
            });
        }
    }

    /**
     * @inheritdoc
     */
    public function push($job)
    {
        $this->_messages[] = $this->serialize($job);
    }
}