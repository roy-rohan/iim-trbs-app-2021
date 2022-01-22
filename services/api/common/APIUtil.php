<?php
include_once __DIR__ . '/../../vendor/autoload.php';
include_once __DIR__ . "/../../models/Common/CustomException.php";
include_once __DIR__ . '/../../utilities/logger.php';
include_once __DIR__ . '/../../config/Database.php';


$STATUS_PENDING = "pending";
$STATUS_SUCCESS = "success";
$STATUS_FAILED = "failed";


function dataDecode($encoded)
{
    $encoded = base64_decode($encoded);
    $decoded = "";
    for ($i = 0; $i < strlen($encoded); $i++) {
        $b = ord($encoded[$i]);
        $a = $b ^ 10;
        $decoded .= chr($a);
    }
    return base64_decode(base64_decode($decoded));
}


function array_push_assoc($array, $key, $value)
{
    $array[$key] = $value;
    return $array;
}


function generateResponseArray($model, $ignoreList)
{
    $tableProperty = "table";
    $connProperty = "conn";
    $reflection = new ReflectionClass($model);
    $properties = $reflection->getProperties();
    $ouput = array();
    for ($index = 0; $index < count($properties); $index++) {
        $property = $properties[$index];
        $propertyName = $property->name;
        if ($propertyName == $tableProperty || $propertyName == $connProperty || in_array($propertyName, $ignoreList))
            continue;
        $ouput = array_push_assoc($ouput, $propertyName, $model->$propertyName);
    }

    return $ouput;
}

function copyArray($arr, $ignoreList)
{
    $ouput = array();

    foreach ($arr as $key => $value) {
        if (in_array($key, $ignoreList))
            continue;
        $ouput = array_push_assoc($ouput, $key, $value);
    }

    return $ouput;
}

function copyObject($copyFrom, $copyTo, $ignoreList)
{
    $tableProperty = "table";
    $connProperty = "conn";
    $reflection = new ReflectionClass($copyTo);
    $properties = $reflection->getProperties();
    for ($index = 0; $index < count($properties); $index++) {
        $property = $properties[$index];
        $propertyName = $property->name;
        if ($propertyName == $tableProperty || $propertyName == $connProperty || in_array($propertyName, $ignoreList))
            continue;
        $copyTo->$propertyName = $copyFrom->$propertyName;
    }

    return $copyTo;
}
