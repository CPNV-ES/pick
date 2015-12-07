<?php

// Prepare the execution of the MySQL request
//-------------------------------------------
function ExecutePreparedQuerie($oMyDB, $sQuerie, $tab)
{
    $stmt = $oMyDB->prepare($sQuerie);

    $i = 0;
    while($i < count($tab))
    {
        $stmt->bindParam($i+1, $tab[$i]);
        $i++;
    }

    $stmt->execute();
    return $stmt;
}


//-----------------------------------------------------------------------------------------------------------//
//-----------------------------------------------------------------------------------------------------------//
//-----------------------------------------------------------------------------------------------------------//


// Execute the MySQL request
//--------------------------
function ExecuteQuerie($oMyDB, $sQuerie, $typeQuerie)
{

	// Define type of MySQL request
	//-----------------------------
    if ($typeQuerie == 'SELECT')
    {
    	// SELECT
        $results = $oMyDB->query("$sQuerie");
    }
    else
    {
    	// OTHER
        $results = $oMyDB->exec("$sQuerie");
    }
	////////////////////////////////

    return ($results);
}
