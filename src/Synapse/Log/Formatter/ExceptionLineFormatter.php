<?php

namespace Synapse\Log\Formatter;

use Monolog\Formatter\LineFormatter;
use Exception;

class ExceptionLineFormatter extends LineFormatter
{
    /**
     * {@inheritdoc}
     */
    public function format(array $record)
    {
        $vars = $this->normalize($record);

        $output = $this->format;
        foreach ($vars['extra'] as $var => $val) {
            if (false !== strpos($output, '%extra.'.$var.'%')) {
                $output = str_replace('%extra.'.$var.'%', $this->convertToString($val), $output);
                unset($vars['extra'][$var]);
            }
        }
        foreach ($vars as $var => $val) {
            if (false !== strpos($output, '%'.$var.'%')) {
                $output = str_replace('%'.$var.'%', $this->convertToString($val), $output);
            }

            if ($var === 'context') {
                $output = str_replace('%context.stacktrace%', ($val['exception']), $output);
            }
        }

        return $output;
    }

    protected function normalize($data)
    {
        if ($data instanceof Exception) {
            return $this->normalizeException($data);
        }

        return parent::normalize($data);
    }

    protected function normalizeException(Exception $e)
    {
        return 'Stack Trace: '.$e->getTraceAsString().PHP_EOL;
    }
}
