<?php namespace SoapBox\Raven\ChangeLog;

trait FormatTrait
{
	private function formatLine($text, $indent = false)
	{
		if ($indent) {
			$indent = '   ';
		}

		$result = '';
		foreach (explode("\r\n", $text) as $line) {
			$result = sprintf("%s%s%s\r\n", $result, $indent, $line);
		}
		return $result;
	}
}
