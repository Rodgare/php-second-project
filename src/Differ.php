<?php

namespace Differ\Differ;

$autoloadPath1 = __DIR__ . '/../../../autoload.php';
$autoloadPath2 = __DIR__ . '/../vendor/autoload.php';
if (file_exists($autoloadPath1)) {
    require_once $autoloadPath1;
} else {
    require_once $autoloadPath2;
}

use function Differ\Parsers\decoder;

function genDiff(string $firstFilePath, string $secondFilePath): string
{
    if (empty($firstFilePath) || empty($secondFilePath)) {
        return '';
    }
    if (!is_file($firstFilePath) || !is_file($secondFilePath)) {
        return 'Wrong path';
    }
    $file1 = json_decode(file_get_contents($firstFilePath), true);
    $file2 = json_decode(file_get_contents($secondFilePath), true);
    $result = implode("\n", array_map(fn($item) => "  $item", combine($file1, $file2)));

    return "\n{\n" . $result . "\n}\n";
}

function combine(array $file1, array $file2): array
{
    $sorted = sorting(
        array_merge(
            setSign(array_diff($file1, $file2), '-'),
            setSign(array_diff($file2, $file1), '+'),
            setSign(array_intersect($file1, $file2))
        )
    );

    return array_map(function ($item) {
        [$key, $val, $sign] = $item;
        return empty($sign) ? "  $key: $val" : "$sign $key: $val";
    }, $sorted);
}

function sorting(array $arr): array
{
    usort($arr, fn($a, $b) => $a[0] <=> $b[0]);

    return $arr;
}

function setSign(array $arr, string $sign = ''): array
{
    return array_map(function ($key, $val) use ($sign) {
        if ($val === true) {
            $val = 'true';
        } elseif ($val === false) {
            $val = 'false';
        }
        return [$key, $val, $sign];
    }, array_keys($arr), $arr);
}
