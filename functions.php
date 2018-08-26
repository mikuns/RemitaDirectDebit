<?php

require_once('connect.php');

//returns true or false depending upon success or failure
function runSimpleUpdateQuery($con, $table, $columns, $values, $where_keys, $where_operators, $where_values, $limit = '') {

    $query = "UPDATE " . $table . " SET ";

    for ($i = 0, $columnsCt = count($columns); $i < $columnsCt; $i++) {
        if ($i < $columnsCt - 1) {
            $query .= $columns[$i] . "=" . ($values[$i] ? $values[$i] : 'NULL') . ", ";
        } else {
            $query .= $columns[$i] . "=" . ($values[$i] ? $values[$i] : 'NULL') . " ";
        }
    }

    $query .= "WHERE ";

    for ($i = 0, $countWhereValues = count($where_values); $i < $countWhereValues; $i++) {
        if ($i == 0) {
            $query .= $where_keys[$i] . $where_operators[$i] . $where_values[$i] . " ";
        } else {
            $query .= "AND " . $where_keys[$i] . $where_operators[$i] . $where_values[$i] . " ";
        }
    }

    $limit && ($query .= 'LIMIT ' . $limit);

//    echo $query;

    mysqli_query($con, $query);

    return ['err' => ['error' => mysqli_error($con), 'code' => mysqli_errno($con)], 'result' => mysqli_affected_rows($con)];
}

function runCountQuery($con, $table, $where_keys, $where_operators, $where_values) { // for users....
    $query = "SELECT count(id) ";

    if (count($where_keys) > 0) {
        $query .= "FROM " . $table . " WHERE ";
    } else {
        $query .= "FROM " . $table;
    }

    for ($i = 0, $countWhere_values = count($where_values); $i < $countWhere_values; $i++) {
        if ($i == 0) {
            $query .= $where_keys[$i] . $where_operators[$i] . $where_values[$i] . " ";
        } else {
            $query .= "AND " . $where_keys[$i] . $where_operators[$i] . $where_values[$i] . " ";
        }
    }

    $query .= ";";

    $execute = mysqli_query($con, $query);
    if ($execute) {

        $rows = array();
        while ($r = mysqli_fetch_array($execute, MYSQLI_ASSOC)) {
            $rows[] = $r;
        }
        return ['err' => ['error' => '', 'code' => 0], 'result' => $rows];
    } else {
        return ['err' => ['error' => mysqli_error($con), 'code' => mysqli_errno($con)], 'result' => $execute];
    }
}


function runFetchUsersCount($con) {
    $result = mysqli_query($con, "SELECT COUNT(id) AS t_ac_ap FROM users  UNION ALL SELECT  COUNT(active) FROM users WHERE active = 'TRUE'  UNION ALL SELECT  COUNT(approved) FROM users WHERE approved = 'TRUE'");
    return ['err' => ['error' => mysqli_error($con), 'code' => mysqli_errno($con)], 'result' => mysqli_fetch_all($result, MYSQLI_ASSOC)];
}


//returns true or false depending upon success or failure
// Now supports multiple inserts by specifying $values in arrays in array ...#venerable
function runSimpleInsertQuery($con, $table, $columns, $values) {

    $query = "INSERT INTO " . $table . " (";

    for ($i = 0, $countColumns = count($columns); $i < $countColumns; $i++) {
        if ($i < $countColumns - 1) {
            $query .= $columns[$i] . ", ";
        } else {
            $query .= $columns[$i] . " ";
        }
    }

    $query .= ") VALUES (";

    if (is_array($values[0])) {
        for ($i = 0, $c = count($values); $i < $c; $i++) {

            for ($j = 0, $countValues = count($values[$i]); $j < $countValues; $j++) {
                if ($j < $countValues - 1) {
                    $query .= $values[$i][$j] . ", ";
                } else {
                    $query .= $values[$i][$j] . "";
                }
            }

            if ($i < $c - 1) {
                $query .= "), (";
            }
        }
    } else {
        for ($i = 0, $countValues = count($values); $i < $countValues; $i++) {
            if ($i < $countValues - 1) {
                $query .= $values[$i] . ", ";
            } else {
                $query .= $values[$i] . " ";
            }
        }
    }

    $query .= ") ";
//    echo $query;

    mysqli_query($con, $query);

    return ['err' => ['error' => mysqli_error($con), 'code' => mysqli_errno($con)], 'result' => mysqli_affected_rows($con), 'insertedId' => mysqli_insert_id($con)];
}

//Zenith d optimiser: The function below is obsolete and redundant, use runSimpleInsertQuery instead as it also returns the inserted id
//returns ID of inserted record
function runSimpleInsertQueryWithIDReturn($con, $table, $columns, $values) {

    $query = "INSERT INTO " . $table . " (";

    for ($i = 0, $countColumns = count($columns); $i < $countColumns; $i++) {
        if ($i < $countColumns - 1) {
            $query .= $columns[$i] . ", ";
        } else {
            $query .= $columns[$i] . " ";
        }
    }

    $query .= ") VALUES (";

    for ($i = 0, $countValues = count($values); $i < $countValues; $i++) {
        if ($i < $countValues - 1) {
            $query .= $values[$i] . ", ";
        } else {
            $query .= $values[$i] . " ";
        }
    }

    $query .= ") ";

    mysqli_query($con, $query);

    return ['err' => ['error' => mysqli_error($con), 'code' => mysqli_errno($con)], 'result' => mysqli_insert_id($con)];
}

//returns true or false depending upon success or failure
function runSimpleDeleteQuery($con, $table, $columns, $values, $limit = 1) {

    $query = "DELETE FROM " . $table;

    $query .= " WHERE ";

    for ($i = 0, $valuesCount = count($values); $i < $valuesCount; $i++) {
        if ($i == 0) {
            $query .= $columns[$i] . " = " . $values[$i] . " ";
        } else {
            $query .= "AND " . $columns[$i] . " = " . $values[$i] . " ";
        }
    }

    $limit && ($query .= ' LIMIT ' . $limit);

    mysqli_query($con, $query);

    return ['err' => ['error' => mysqli_error($con), 'code' => mysqli_errno($con)], 'result' => mysqli_affected_rows($con)];
}

//returns a numerical array
function runSimpleFetchQuery($con, $columns_to_fetch, $table, $where_keys, $where_operators, $where_values, $group_by, $order, $limit, $offset = null) {

    $query = "SELECT ";

    if (is_array($columns_to_fetch)) {
        for ($i = 0, $countColumns_to_fetch = count($columns_to_fetch); $i < $countColumns_to_fetch; $i++) {
            if ($i < $countColumns_to_fetch - 1) {
                $query .= $columns_to_fetch[$i] . ", ";
            } else {
                $query .= $columns_to_fetch[$i] . " ";
            }
        }
    } else {
        $query .= $columns_to_fetch . " ";
    }

    if (count($where_keys) > 0) {
        $query .= "FROM " . $table . " WHERE ";
    } else {
        $query .= "FROM " . $table . ' ';
    }

    for ($i = 0, $countWhere_values = count($where_values); $i < $countWhere_values; $i++) {
        if ($i == 0) {
            $query .= $where_keys[$i] . $where_operators[$i] . $where_values[$i] . " ";
        } else {
            $query .= "AND " . $where_keys[$i] . $where_operators[$i] . $where_values[$i] . " ";
        }
    }

    if (strcmp($group_by, "") != 0) {
        $query .= "GROUP BY " . $group_by . " ";
    }

    if (strcmp($order, "") != 0) {
        $query .= "ORDER BY " . $order . " ";
    }

    if (strcmp($limit, "") != 0) {
        $query .= "LIMIT " . $limit . " ";
    }
    if (strcmp($offset, "") != 0) {
        $query .= "OFFSET " . $offset . " ";
    }

    $query .= ";";

//    echo $query;

    $execute = mysqli_query($con, $query);
    if ($execute) {
        $res1 = mysqli_num_rows($execute);

        //Fixed a bug: $res1 = 1 instead of $res1 === 1 causes a bug
        if ($res1 === 1 && count($columns_to_fetch) == 1 && strcmp($columns_to_fetch[0], "*") != 0) {
            return ['err' => ['error' => '', 'code' => 0], 'result' => mysqli_fetch_array($execute)[0]];
        } else {
            $rows = array();
            while ($r = mysqli_fetch_array($execute, MYSQLI_ASSOC)) {
                $rows[] = $r;
            }
            return ['err' => ['error' => '', 'code' => 0], 'result' => $rows];
        }
    } else {
        return ['err' => ['error' => mysqli_error($con), 'code' => mysqli_errno($con)], 'result' => $execute];
    }
}

// returns a numerical array
function runJointFetchQuery($con, $columns_to_fetch, $tables, $connectors, $where_keys, $where_operators, $where_values, $group_by, $order, $limit, $offset = null) {

    $query = "SELECT ";

    if (is_array($columns_to_fetch)) {
        for ($i = 0; $i < count($columns_to_fetch); $i++) {
            if ($i < count($columns_to_fetch) - 1) {
                $query .= $columns_to_fetch[$i] . ", ";
            } else {
                $query .= $columns_to_fetch[$i] . " FROM ";
            }
        }
    } else {
        $query .= $columns_to_fetch . " FROM ";
    }

    for ($n = 0; $n < count($tables); $n++) {

        if ($n < count($tables) - 1) {
            $query .= '(' . $tables[$n] . ' RIGHT JOIN ';
        } else {
            $query .= $tables[$n] . ' ';
        }
    }


    for ($n = (count($connectors) - 1); $n >= 0; $n--) {

        $connector_group = $connectors[$n];

        for ($c = 0; $c < count($connector_group); $c++) {
            $connector = $connector_group[$c];
            $param_1 = $connector[0];
            $param_2 = $connector[1];
            $compa = $connector[2];

            if ($c < count($connector_group) - 1) {
                $query .= 'ON ' . $param_1 . ' ' . $compa . ' ' . $param_2 . ' AND ';
            } else {
                $query .= 'ON ' . $param_1 . ' ' . $compa . ' ' . $param_2 . ' ) ';
            }
        }
    }

    if (count($where_keys) > 0) {
        $query .= " WHERE ";
    } else {
        
    }

    for ($i = 0; $i < count($where_values); $i++) {
        if ($i == 0) {
            $query .= $where_keys[$i] . $where_operators[$i] . $where_values[$i] . " ";
        } else {
            $query .= "AND " . $where_keys[$i] . $where_operators[$i] . $where_values[$i] . " ";
        }
    }

    if (strcmp($group_by, "") != 0) {
        $query .= "GROUP BY " . $group_by . " ";
    }

    if (strcmp($order, "") != 0) {
        $query .= "ORDER BY " . $order . " ";
    }

    if (strcmp($limit, "") != 0) {
        $query .= "LIMIT " . $limit . " ";
    }

    $query .= ";";

    //echo $query;

    $execute = mysqli_query($con, $query);
    if ($execute) {
        $res1 = mysqli_num_rows($execute);

        //Fixed a bug: $res1 = 1 instead of $res1 === 1 causes a bug
        if ($res1 === 1 && count($columns_to_fetch) == 1 && strcmp($columns_to_fetch[0], "*") != 0) {
            return ['err' => ['error' => '', 'code' => 0], 'result' => mysqli_fetch_array($execute)[0]];
        } else {
            $rows = array();
            while ($r = mysqli_fetch_array($execute, MYSQLI_ASSOC)) {
                $rows[] = $r;
            }
            return ['err' => ['error' => '', 'code' => 0], 'result' => $rows];
        }
    } else {
        return ['err' => ['error' => mysqli_error($con), 'code' => mysqli_errno($con)], 'result' => $execute];
    }
}

