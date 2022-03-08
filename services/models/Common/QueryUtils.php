<?php

include_once __DIR__ . "/CustomException.php";
include_once __DIR__ . '/../../utilities/logger.php';

trait QueryUtils
{
    public function processFiltersAndOrderBy($query, $data)
    {
        $query = $this->processFilters($query, $data);
        $query = $this->processOrderBy($query, $data);

        return $query;
    }

    public function processFilters($query, $data)
    {
        $filters = $data->filters ? $data->filters : array();

        if ($filters != null && count($filters) != 0) {
            $filterQuery = "";
            $filterCount = 0;
            foreach ($filters as $filter) {
                if ($filterCount != 0) {
                    $filterQuery .= " $data->filter_op ";
                } else {
                    $filterQuery .= " WHERE ";
                }
                switch (gettype($filter->value)) {
                    case "string":
                        if($filter->op == "IN") {
                            $filterQuery .= " $filter->field_name $filter->op $filter->value ";
                        } else {
                            $filterQuery .= " $filter->field_name $filter->op '$filter->value' ";
                        }
                        break;
                    case "integer":
                        $filterQuery .= " $filter->field_name $filter->op $filter->value ";
                        break;
                    default:
                        $filterQuery .= " $filter->field_name $filter->op $filter->value ";
                }
                $filterCount++;
            }
            $query .= $filterQuery;
        }

        return $query;
    }

    public function processOrderBy($query, $data)
    {
        $sort = $data->sort ? $data->sort : array();

        if (
            $sort != null && count($sort) != 0
        ) {
            $sortQuery = "";
            $sortCount = 0;
            foreach ($sort as $sortItem) {
                if ($sortCount != 0) {
                    $sortQuery .= " , ";
                } else {
                    $sortQuery .= " ORDER BY ";
                }

                $sortQuery .= " $sortItem->field_name $sortItem->op ";
                $sortCount++;
            }
            $query .= $sortQuery;
        }

        return $query;
    }


    function executeQuery($stmt)
    {
        try {
            if (
                $stmt->execute()
            ) {
                return true;
            } else {
                return false;
            }
        } catch (PDOException $exp) {
            message_logger($exp->getMessage());
            $custom_exp = null;
            if (ERROR_HANDLING_STATUS == "DEV")
                $custom_exp = new CustomException($exp->getMessage());
            else
                $custom_exp = new CustomException("Database exception occured.");
            $custom_exp->sendBadRequest();
            exit(1);
        }
    }

    function generateInsertQuery($ignoreList)
    {
        $tableProperty = "table";
        $connProperty = "conn";
        $reflection = new ReflectionClass($this);
        $properties = $reflection->getProperties();
        $query = "INSERT INTO " .  $this->$tableProperty . " SET ";
        for ($index = 0; $index < count($properties); $index++) {
            $property = $properties[$index];
            $propertyName = $property->name;
            if ($propertyName == $tableProperty || $propertyName == $connProperty || in_array($propertyName, $ignoreList))
                continue;
            $query .= $propertyName . " = :" . $propertyName . ", ";
        }
        if (strpos($query, ", ")) {
            if (substr($query, -2) == ", ") {
                $query = substr($query, 0, strlen($query) - 2);
            }
        }
        return $query;
    }

    function bindParams($stmt, $ignoreList)
    {
        $tableProperty = "table";
        $connProperty = "conn";
        $reflection = new ReflectionClass($this);
        $properties = $reflection->getProperties();
        for ($index = 0; $index < count($properties); $index++) {
            $property = $properties[$index];
            $propertyName = $property->name;
            if ($propertyName == $tableProperty || $propertyName == $connProperty || in_array($propertyName, $ignoreList))
                continue;
            $stmt->bindParam(':' . $propertyName, $this->$propertyName);
        }
        return $stmt;
    }

    function generateUpdateQuery($clause, $ignoreList)
    {
        $reflection = new ReflectionClass($this);
        $properties = $reflection->getProperties();
        $tableProperty = "table";
        $connProperty = "conn";
        $query = 'UPDATE ' . $this->$tableProperty . ' SET ';
        for ($index = 0; $index < count($properties); $index++) {
            $property = $properties[$index];
            $propertyName = $property->name;
            if ($propertyName == $tableProperty || $propertyName == $connProperty || in_array($propertyName, $ignoreList))
                continue;
            $query .= $propertyName . ' = :' . $propertyName . ', ';
        }
        if (strpos($query, ', ')) {
            if (substr($query, -2) == ', ') {
                $query = substr($query, 0, strlen($query) - 2);
            }
        }
        $query .= ' ' . $clause;
        return $query;
    }

    function sanitizeInputs($ignoreList)
    {
        $tableProperty = "table";
        $connProperty = "conn";
        $reflection = new ReflectionClass($this);
        $properties = $reflection->getProperties();
        for ($index = 0; $index < count($properties); $index++) {
            $property = $properties[$index];
            $propertyName = $property->name;
            if ($propertyName == $tableProperty || $propertyName == $connProperty || in_array($propertyName, $ignoreList))
                continue;
            $this->propertyName = htmlspecialchars(strip_tags($this->propertyName));
        }
    }

    function populateModelFields($row, $ignoreList)
    {
        $tableProperty = "table";
        $connProperty = "conn";
        $reflection = new ReflectionClass($this);
        $properties = $reflection->getProperties();
        for ($index = 0; $index < count($properties); $index++) {
            $property = $properties[$index];
            $propertyName = $property->name;
            if ($propertyName == $tableProperty || $propertyName == $connProperty || in_array($propertyName, $ignoreList))
                continue;
            $this->$propertyName = $row[$propertyName];
        }
    }
}
