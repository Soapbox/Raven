<?php namespace SoapBox\Raven\ChangeLog;

trait FormatTrait
{
    private function formatLine($text, $indent = '')
    {
        $result = '';
        foreach (explode("\r\n", $text) as $line) {
            $result = sprintf("%s%s%s\r\n", $indent, $result, $line);
        }
        return $result;
    }
}
