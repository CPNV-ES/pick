<?php

// ----------------------------------
// ExecuterRequetePreparee($connexion, $requete, $tab)
// Date de création :
// Date de révision :
// Auteur : Quentin Girard & Lola Olivet & Théo Zimmermann & Stéphane Martignier
// Entrée : la connexion à utiliser, la requete, les paramètres de la requete
// Sortie : le retour de la requête
// ----------------------------------
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


// ----------------------------------
// ExecuterRequete($connexion, $requete, $typeRequete)
// Date de création :
// Date de révision :
// Auteur : Quentin Girard & Lola Olivet & Théo Zimmermann & Stéphane Martignier
// Entrée : la connexion à utiliser, la requete, le type de requête
// Sortie : le retour de la requête
// ----------------------------------
function ExecuteQuerie($oMyDB, $sQuerie, $typeQuerie)
{

    if ($typeQuerie == 'SELECT')
    {
        // print($sQuerie);
        $results = $oMyDB->query("$sQuerie"); //Exécution d'une requête SELECT
    }
    else
    {
        //print($sQuerie);
        // print("<br/>");
        $results = $oMyDB->exec("$sQuerie"); //Exécution d'une requête non SELECT
    }

    return ($results);
}
?>