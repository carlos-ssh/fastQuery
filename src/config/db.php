<?php

  class db{
    private $dbHost = 'localhost';
    private $dbUser = 'root';
    private $dbPass = '08090809';
    private $dbName = 'movies_db';


    public function conecctionBD(){
      $mysqlConnect = "mysql:host=$this->dbHost;dbName=$this->dbName";
      $dbConecction = new PDO($mysqlConnect, $this->dbUser, $this->$dbPass);
    }
  }
