<?php

namespace Differ\Formatters\Json;

function json(array $diff)
{
    return json_encode($diff);
}