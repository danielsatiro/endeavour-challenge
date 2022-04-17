<?php
namespace App\Services;

Class CSVReader
{
    private static function getFirstLine($file)
    {
        $handle     = fopen($file, "r");
        $firstLine  = fgets($handle);
        fclose($handle);

        return $firstLine;
    }

    private static function getHeader($file, $delimiter)
    {
        $firstLine = self::getFirstLine($file);

        return str_getcsv($firstLine, $delimiter);
    }

    private static function detectDelimiter($file)
    {
        $delimiters = array(
            ';'   => 0,
            ','   => 0,
            "\t"  => 0,
            "|"   => 0
        );

        $firstLine  = self::getFirstLine($file);

        foreach($delimiters as $delimiter => &$count)
        {
            $count = count(str_getcsv($firstLine, $delimiter));
        }

        return array_search(max($delimiters), $delimiters);
    }

    public static function read($file)
    {
        $delimiter = self::detectDelimiter($file);
        $data = array_map(function($v) use ($delimiter){
                return str_getcsv($v, $delimiter);
            }, file($file));

        return $data;
    }

    public static function readWithHead($file)
    {
        $delimiter = self::detectDelimiter($file);
        $header = self::getHeader($file, $delimiter);

        $data = array_map(function($v) use ($delimiter, $header){
                $line = str_getcsv($v, $delimiter);
                
                $combined = array_combine($header, $line);
                $combined['credit_card'] = [
                    'type' => $combined['credit_card/type'],
                    'number' => $combined['credit_card/number'],
                    'name' => $combined['credit_card/name'],
                    'expirationDate' => $combined['credit_card/expirationDate'],
                ];
                unset(
                        $combined['credit_card/type'],
                        $combined['credit_card/number'],
                        $combined['credit_card/name'],
                        $combined['credit_card/expirationDate']
                );

                return $combined;
            }, array_filter(file($file), function ($line) use ($delimiter, $header) {
                $line = str_getcsv($line, $delimiter);
            return $header !== $line;
        }));

        return $data;
    }
}
