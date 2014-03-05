<?php

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Synapse\Log\Handler;

use Monolog\Handler\AbstractProcessingHandler;
use RollbarNotifier;

/**
 * Sends errors to Rollbar
 *
 * @author Paul Statezny <paulstatezny@gmail.com>
 */
class RollbarHandler extends AbstractProcessingHandler
{
    /**
     * Rollbar notifier
     *
     * @var RollbarNotifier
     */
    protected $rollbarNotifier;

    /**
     * @param string   $token       post_server_item access token for the Rollbar project
     * @param string   $environment This can be set to any string
     * @param string   $root        Directory your code is in; used for linking stack traces
     * @param integer  $level       [description]
     * @param boolean  $bubble      [description]
     */
    public function __construct($token, $environment = 'production', $root = null, $level = Logger::DEBUG, $bubble = true)
    {
        $this->rollbarNotifier = new RollbarNotifier(array(
            'access_token' => $token,
            'environment'  => $environment,
            'root'         => $root,
        ));

        parent::__construct($level, $bubble);
    }

    protected function write(array $record)
    {
        if (isset($record['context']) and isset($record['context']['exception'])) {
            $this->rollbarNotifier->report_exception($record['context']['exception']);
        } else {
            return $this->rollbarNotifier->report_message(
                $message,
                'level',
                $record['extra']
            );
        }

        $this->rollbarNotifier->flush();
    }
}
