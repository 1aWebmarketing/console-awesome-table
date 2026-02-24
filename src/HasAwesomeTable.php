<?php

namespace AwesomeTable;

use Symfony\Component\Console\Terminal;

trait HasAwesomeTable
{
    private function title($title)
    {
        echo PHP_EOL;
        echo str_repeat('#', strlen($title) + 20) . PHP_EOL;
        echo '#' . str_repeat(' ', strlen($title) + 18) . '#' . PHP_EOL;
        echo '#' . str_repeat(' ', 9) . $title . str_repeat(' ', 9) . '#' . PHP_EOL;
        echo '#' . str_repeat(' ', strlen($title) + 18) . '#' . PHP_EOL;
        echo str_repeat('#', strlen($title) + 20) . PHP_EOL . PHP_EOL;
    }

    private function awesomeTable(array $headings = [], array $rows = [], bool $showUnusedKeys = true): void
    {
        if ($rows === []) {
            $this->table($headings, []);

            return;
        }

        if ($headings === []) {
            $firstRow = $rows[0];
            $headings = array_keys((array)$firstRow);
            $undisplayedKeys = [];
        } else {
            $firstRow = (array)$rows[0];
            $allKeys = array_keys($firstRow);
            $undisplayedKeys = array_diff($allKeys, $headings);
        }

        // Normalise rows into a table-friendly structure.
        $data = [];
        foreach ($rows as $row) {
            $line = [];
            foreach ($headings as $heading) {
                $value = $row[$heading] ?? null;
                $line[$heading] = $value === null ? '' : (is_array($value) ? '+ Array' : $value);
            }
            $data[] = $line;
        }

        // Estimate if the table will fit into the current console width.
        $terminalWidth = (new Terminal)->getWidth();

        $headerLine = implode(' | ', $headings);
        $firstRowLine = '';
        if ($data !== []) {
            $firstRowValues = [];
            foreach ($headings as $heading) {
                $firstRowValues[] = (string)($data[0][$heading] ?? '');
            }
            $firstRowLine = implode(' | ', $firstRowValues);
        }

        $approxWidth = max(strlen($headerLine), strlen($firstRowLine)) + 4;

        if ($approxWidth <= $terminalWidth) {
            $this->table($headings, $data);
            if ($showUnusedKeys) $this->infoUnusedKeys($undisplayedKeys);
            return;
        }

        $this->dataBlock($headings, $data);
        if ($showUnusedKeys) $this->infoUnusedKeys($undisplayedKeys);
    }

    /**
     * Display rows as framed blocks: key (padded) .... value, one block per row.
     * Same signature as table($headings, $rows).
     *
     * @param array<int, string> $headings
     * @param array<int, array<string, mixed>> $rows
     */
    private function dataBlock(array $headings, array $rows): void
    {
        $maxKeyLength = 0;
        foreach ($headings as $heading) {
            $maxKeyLength = max($maxKeyLength, strlen($heading));
        }

        $separator = '....';

        foreach ($rows as $index => $rowData) {
            $lines = [];
            foreach ($headings as $heading) {
                $value = (string)($rowData[$heading] ?? '');
                $lines[] = str_pad($heading, $maxKeyLength, '.') . $separator . $value;
            }

            $maxLineLength = 0;
            foreach ($lines as $line) {
                $maxLineLength = max($maxLineLength, strlen($line));
            }

            $border = '+' . str_repeat('-', $maxLineLength + 2) . '+';

            $this->line($border);
            foreach ($lines as $line) {
                $this->line('| ' . str_pad($line, $maxLineLength, ' ') . ' |');
            }
            $this->line($border);

            if ($index < count($rows) - 1) {
                $this->line('');
            }
        }
    }

    private function infoUnusedKeys($keys): void
    {
        if (empty($keys)) {
            return;
        }

        asort($keys);
        
        $this->info('Undisplayed fields: ' . implode(', ', $keys));
    }
}