<?php

function dbConnect(string $dbname = 'katoo') : PDO
{
    try
    {
        $dbtoconnect = new PDO("mysql:host=localhost;dbname=cosmocats;charset=utf8", 'cat', 'passwordtochangewhichisnot1234');
    }
    catch (Exception $e)
    {
            die('Erreur : ' . $e->getMessage());
    }
    return $dbtoconnect;
}
?>