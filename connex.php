<?php
  /**
    Connexion à la BD.
  */
  function connex() {
	  $connex = mysql_connect('localhost', 'aximag', 'iMAG04');
	  mysql_set_charset('utf8', $connex);
	  mysql_select_db('CiHai', $connex);
	  return $connex;
  }   
?>
