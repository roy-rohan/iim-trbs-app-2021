<?php

include_once __DIR__ . "/../models/Common/CustomException.php";
include_once __DIR__ . '/../utilities/logger.php';

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
                        $filterQuery .= " $filter->field_name $filter->op '$filter->value' ";
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
}
