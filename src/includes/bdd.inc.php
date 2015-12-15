<?php


// ----------------------------------
// ExecutePreparedQuerie($oMyDB, $sQuerie, $tab)
// Param 		: $oMyDB 	-> PDO connection
// 				: $sQuery 	-> sql request
// 				: $sType 	-> INSERT, SELECT, UPDATE, etc...
// Description : Execute a prepareRequest
// ----------------------------------
function ExecutePreparedQuerie($oMyDB, $sQuery, $sType)
{
    $stmt = $oMyDB->prepare($sQuery);

    $i = 0;
    while ($i < count($sType))
    {
        $stmt->bindParam($i+1, $sType[$i]);
        $i++;
    }

    $stmt->execute();
    return $stmt;
}


// ----------------------------------
// ExecuteQuerie($oMyDB, $sQuerie, $tab)
// Param 		: $oMyDB 	-> PDO connection
//				: $sQuery 	-> sql request
//				: $sType 	-> INSERT, SELECT, UPDATE, etc...
// Description : Execute a request
// ----------------------------------
function ExecuteQuerie($oMyDB, $sQuery, $sType)
{

	// Define type of MySQL request
	//-----------------------------
    if ($sType == 'SELECT')
    {
    	// SELECT
        $sResults = $oMyDB->query("$sQuery");
    }
    else
    {
    	// OTHER
        $sResults = $oMyDB->exec("$sQuery");
    }
	////////////////////////////////

    return ($sResults);
}
