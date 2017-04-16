<?php

namespace AltoLabs\Snappic\Model;

use Magento\Framework\Logger\Handler\Base;
use Monolog\Logger as MonologLogger;

class Logger extends Base
{
    /**
     * File to write to
     *
     * @var string
     */
    protected $fileName = '/var/log/snappic.log';

    /**
     * The (PSR-3) level to start logging at
     *
     * @var int
     */
    protected $loggerType = MonologLogger::DEBUG;
}
