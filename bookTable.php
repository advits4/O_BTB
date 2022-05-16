<?php

/**
 * Instructions
 ************************************************************************************************************
 * 
 * You must edit the content of this file, specifically, get_best_tables function, and you can't add any code outside
 * that function.
 * There is no limit of time, but this test shouldn't take more than an hour.
 * 
 *************************************************************************************************************
 * We have a restaurant with 12 tables, and we need a tool to book tables.
 *
 * Function will return an array with table ids, or an empty array if the book can't be done.
 * 
 * We must book the minimum number of tables for a booking, if a group of 10 people is coming
 * for dinner, we won't give them 5 tables of 2 persons, we will give them 1 table of 8 and 1 table of 2.
 * If there are 2 tables with the same size, we will book the one with the lower id.
 *
 * Have in mind that some tables are already booked, we can't book them again.
 * You can rum the script to check in the function works (PHP version 7.0+).
 *
 ************************************************************************************************************
 */

$tables = [
    ["id" => 1, "size" => 6, "status" => "booked"],
    ["id" => 2, "size" => 4, "status" => "free"],
    ["id" => 3, "size" => 2, "status" => "free"],
    ["id" => 4, "size" => 6, "status" => "free"],
    ["id" => 5, "size" => 2, "status" => "free"],
    ["id" => 6, "size" => 8, "status" => "free"],
    ["id" => 7, "size" => 2, "status" => "booked"],
    ["id" => 8, "size" => 4, "status" => "free"],
    ["id" => 9, "size" => 4, "status" => "free"],
    ["id" => 10, "size" => 12, "status" => "booked"],
    ["id" => 11, "size" => 20, "status" => "free"],
    ["id" => 12, "size" => 12, "status" => "free"],
];

/**
 * Get the ids of the tables booked
 *
 * @param array   $table  The array of tables in the restaurant
 * @param integer $persons How many people is the booking
 *
 * @return array
 */
function get_best_tables(array $tables, int $persons): array
{
    $tables_ids = [];

    # our first validation to return an empty set if 0 persons requested
    if ($persons == 0) {
        return $tables_ids;
    }
    # since we do not have a single seat table, we do a even number check for an efficient search alogorithm
    $persons = ($persons % 2 > 0) ? $persons + 1: $persons;

    # to find all the available free tables as per the number of persons requested
    $tableStatus = 'free';
    $availableTables = array_filter($tables, function($element) use($persons, $tableStatus){
        return $element['size'] <= $persons && $element['status'] == $tableStatus;
    });
    
    $tableSize = array_column($availableTables, 'size');
    $totalTableSize = array_sum($tableSize);
    # our second validation to return en empty set if the requested persons are more than the total available seats
    if ($persons > $totalTableSize) {
        return $tables_ids;
    }
    
    $peopleLeft = $persons;
    do {
        $searchTableForPeople = $peopleLeft;
        $peopleMatchingTables = array_filter($availableTables, function($element) use($searchTableForPeople, $tableStatus){
            return $element['size'] <= $searchTableForPeople && $element['status'] === $tableStatus;
        });
        
        # sort it by desc order so that max people can be fit in the first available table
        array_multisort(array_column($peopleMatchingTables, 'size'), SORT_DESC, $peopleMatchingTables);
        
        # assign the first table available table
        $tables_ids[] = $peopleMatchingTables[0]['id'];
        $peopleLeft -= $peopleMatchingTables[0]['size'];
        
        # update the status of the table in the search array so that it is not picked up again
        foreach($availableTables as $k => $t) {
            if(in_array($t['id'], $tables_ids)) {
                $availableTables[$k]['status'] = "reserved";
            }
        }
        # we will repeat the process again to find the next best fit table as per the remaining persons left
    } while ($peopleLeft >= 1);
    
    return $tables_ids;
}


// Check that the function works as expected.
$tables_ids = get_best_tables($tables, 22);
if(count($tables_ids)==2 and $tables_ids[0] == 11 and $tables_ids[1] == 3) {
    echo "[1] - Correct :) \n";
} else {
    echo "[1] - Incorrect :( \n";
}

$tables_ids = get_best_tables($tables, 2);
if(count($tables_ids)==1 and $tables_ids[0] == 3) {
    echo "[2] - Correct :) \n";
} else {
    echo "[2] - Incorrect :( \n";
}

$tables_ids = get_best_tables($tables, 33);
if(count($tables_ids)==3 and $tables_ids[0] == 11 and $tables_ids[1] == 12 and $tables_ids[2] == 3) {
    echo "[3] - Correct :) \n";
} else {
    echo "[3] - Incorrect :( \n";
}

$tables_ids = get_best_tables($tables, 3);
if(count($tables_ids)==1 and $tables_ids[0] == 2) {
    echo "[4] - Correct :) \n";
} else {
    echo "[4] - Incorrect :( \n";
}

$tables_ids = get_best_tables($tables, 1);
if(count($tables_ids)==1 and $tables_ids[0] == 3) {
    echo "[5] - Correct :) \n";
} else {
    echo "[5] - Incorrect :( \n";
}

$tables_ids = get_best_tables($tables, 100);
if(count($tables_ids)==0) {
    echo "[6] - Correct :) \n";
} else {
    echo "[6] - Incorrect :( \n";
}

$tables_ids = get_best_tables($tables, 62);
if(count($tables_ids)==9) {
    echo "[7] - Correct :) \n";
} else {
    echo "[7] - Incorrect :( \n";
}

$tables_ids = get_best_tables($tables, 0);
if(count($tables_ids)==0) {
    echo "[8] - Correct :) \n";
} else {
    echo "[8] - Incorrect :( \n";
}
