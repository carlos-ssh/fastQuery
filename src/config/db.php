<?php
  // private $dbHost = 'localhost';
  // private $dbUser = 'root';
  // private $dbPass = '08090809';
  // private $dbName = 'movies_db';

  class db{
    private $dbHost = '35.209.7.121';
    private $dbUser = 'ussvg7ebv26cy';
    private $dbPass = '5dow5cq54wdq';
    private $dbName = 'dbg3gi6ghtc2px';

    public function connectBD(){
      $mysqlConnect = "mysql:host=$this->dbHost;dbname=$this->dbName";
      $dbConnection = new PDO($mysqlConnect, $this->dbUser, $this->dbPass);
      $dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

      return $dbConnection;
    }
  }
