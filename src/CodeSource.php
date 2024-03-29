<?php

namespace Streply;

class CodeSource
{
    public function trim(string $string): string
    {
        $string = str_replace(['<', '>'], ['&lt;', '&gt;'], $string);
        $string = str_replace("\n", "", $string);
        $string = strip_tags($string);

        return $string;
    }

    public function loadFile(string $fileName): array
    {
        $file = file($fileName);
        $output = [];

        foreach ($file as $lineNumber => $line) {
            $output[$lineNumber + 1] = $this->trim($line);
        }

        return $output;
    }

    public static function load(string $fileName, int $line, int $margin): array
    {
        if (is_file($fileName)) {
            $source = new CodeSource();
            $output = [];
            $file = $source->loadFile($fileName);

            if (isset($file[$line])) {
                for ($i = $line; $i >= $line - $margin; --$i) {
                    if (isset($file[$i])) {
                        $output[$i] = $file[$i];
                    }
                }

                $output[$line] = $file[$line];

                for ($i = $line; $i <= $line + $margin; ++$i) {
                    if (isset($file[$i])) {
                        $output[$i] = $file[$i];
                    }
                }
            }

            ksort($output);

            return $output;
        }

        return [];
    }
}
