<?php namespace SoapBox\Raven\Commands\Util;

use RuntimeException;
use SoapBox\Raven\DataStructures\HashTable;
use SoapBox\Raven\Utils\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FindDuplicatesCommand extends Command {
	protected $command = 'duplicates';
	protected $description = 'Find duplicate lines in a file';
	protected $defaultFormat = '{ln}: {line}';
	private $existingLines;
	private $input;
	private $output;
	private $inputFile;
	private $outputFile;

	protected function addArguments() {
		$this->makeArgument('input-file')
			->setDescription('The input file')
			->required();
	}

	protected function addOptions() {
		$this->makeOption('format')
			->addShortcut('f')
			->setDescription('The output format string')
			->setDefault($this->defaultFormat)
			->required();

		$this->makeOption('output-file')
			->addShortcut('o')
			->setDescription('The output file')
			->required();
	}

	/**
	 * Write a line to the output
	 *
	 * @param string $line The line to write to the output
	 */
	private function write($line) {
		if (empty($this->outputFile)) {
			$this->output->writeLn($line);
		} else {
			fwrite($this->outputFile, $line . "\n");
		}
	}

	/**
	 * Open the output file and set the handle
	 */
	private function openOutputFile() {
		$outputFile = $this->input->getOption('output-file');

		if (!empty($outputFile)) {
			if ($outputFile == $this->input->getArgument('input-file')) {
				$this->closeInputFile();
				throw new RuntimeException('The input file and output file cannot be the same.');
			}

			$this->outputFile = fopen($outputFile, 'w');
		}
	}

	/**
	 * Close the output file handle
	 */
	private function closeOutputFile() {
		if (!empty($this->outputFile)) {
			fclose($this->outputFile);
		}
	}

	/**
	 * Open the input file and set the handle
	 */
	private function openInputFile() {
		if (!file_exists($this->input->getArgument('input-file'))) {
			throw new RuntimeException(sprintf('The file "%s" does not exist.', $this->input->getArgument('input-file')));
		}
		$this->inputFile = fopen($this->input->getArgument('input-file'), 'r');
	}

	/**
	 * Close the input file handle
	 */
	private function closeInputFile() {
		if (!empty($this->inputFile)) {
			fclose($this->inputFile);
		}
	}

	/**
	 * This method determines whether or not the current line should be written to
	 * the output.
	 *
	 * @param string $line The current line read from the input file
	 * @return bool Whether or not the line should be written to output
	 */
	protected function shouldWrite($line) {
		return $this->existingLines->contains($line);
	}

	/**
	 * Parse the input file and write the results to the output
	 *
	 * @param string $outputFormat The output format to apply to each output line
	 */
	private function parseFile($outputFormat) {
		$lineNumber = 0;
		$uniqueLines = 0;
		$duplicateLines = 0;
		$outputLines = 0;

		while ($line = fgets($this->inputFile)) {
			$lineNumber++;
			$line = str_replace("\n", '', $line);

			if ($this->shouldWrite($line)) {
				$this->write(sprintf($outputFormat, $lineNumber, $line));
				$outputLines++;
			}

			if (!$this->existingLines->contains($line)) {
				$this->existingLines->add($line);
				$uniqueLines++;
			} else {
				$duplicateLines++;
			}
		}

		// Print result metadata
		if (!empty($this->outputFile)) {
			$biggestNumber = max($lineNumber, $uniqueLines, $duplicateLines, $outputLines);
			$numberOfDigits = strlen($biggestNumber);
			$this->output->writeLn(sprintf('Input file length: %s%s', str_repeat('0', $numberOfDigits - strlen($lineNumber)), $lineNumber));
			$this->output->writeLn(sprintf('Unique lines:      %s%s', str_repeat('0', $numberOfDigits - strlen($uniqueLines)), $uniqueLines));
			$this->output->writeLn(sprintf('Duplicated lines:  %s%s', str_repeat('0', $numberOfDigits - strlen($duplicateLines)), $duplicateLines));
			$this->output->writeLn(sprintf('Outputed lines:    %s%s', str_repeat('0', $numberOfDigits - strlen($outputLines)), $outputLines));
		}
	}

	public function execute(InputInterface $input, OutputInterface $output)
	{
		$this->input = $input;
		$this->output = $output;

		$this->openInputFile();
		$this->openOutputFile();

		$this->existingLines = new HashTable();

		$outputFormat = $input->getOption('format');
		$outputFormat = str_replace('{ln}', '%1$s', $outputFormat);
		$outputFormat = str_replace('{line}', '%2$s', $outputFormat);

		$this->parseFile($outputFormat);

		$this->closeOutputFile();
		$this->closeInputFile();
	}
}