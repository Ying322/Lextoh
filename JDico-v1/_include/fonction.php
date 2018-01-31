<?php
/**
* lit un ficier CSV et construit le tableau PHP correspondant
* @file string 	nom du fichier 
* @return array	tableau PHP correspondant, NULL si erreur
**/

function csv_to_array($filename='', $delimiter=',')
{
    if(!file_exists($filename) || !is_readable($filename))
        return FALSE;

    $header = NULL;
    $data = array();
    if (($handle = fopen($filename, 'r')) !== FALSE)
    {
        while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE)
        {
                $data[$row[0]] =  $row[1];
                $data['dico_'.$row[0]] = $row[2];
        }
        fclose($handle);
    }
    return $data;
}

/*
$l_serv = csv_to_array ('l_serv.csv', "\t");
print_r($l_serv);
*/

function startsWith($haystack, $needle)
{
     $length = strlen($needle);
     return (substr($haystack, 0, $length) === $needle);
}

function endsWith($haystack, $needle)
{
    $length = strlen($needle);
    if ($length == 0) {
        return true;
    }

    return (substr($haystack, -$length) === $needle);
}


?>