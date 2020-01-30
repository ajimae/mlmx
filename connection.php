<?php
  $dbhost = 'remotemysql.com:3306';
  $dbuser = 'zM8cX0MSic';
  $dbpass = '6fFNpHFZDt';
  $dbase = 'zM8cX0MSic';
  $conn = mysqli_connect($dbhost, $dbuser, $dbpass, $dbase);
  
  if(!$conn) {
    die('could not connect: ' . mysqli_error());
  }

  // echo 'Connected successfully';

  $sql_one = 'CREATE TABLE IF NOT EXISTS preliminary (
    id INT NOT NULL AUTO_INCREMENT,
    name VARCHAR(255),
    email VARCHAR(255),
    ref_code VARCHAR(255),
    referer VARCHAR(255),
    PRIMARY KEY (id)
  )';
  $sql_two = 'CREATE TABLE IF NOT EXISTS level_one (
      id INT NOT NULL AUTO_INCREMENT,
      name VARCHAR(255),
      email VARCHAR(255),
      ref_code VARCHAR(255),
      referer VARCHAR(255),
      PRIMARY KEY (id)
  )';
  $sql_three = 'CREATE TABLE IF NOT EXISTS level_two (
      id INT NOT NULL AUTO_INCREMENT,
      name VARCHAR(255),
      email VARCHAR(255),
      ref_code VARCHAR(255),
      referer VARCHAR(255),
      PRIMARY KEY (id)
  )';

  $queryList = array($sql_one, $sql_two, $sql_three);

  for($i = 0; $i < count($queryList); $i++) {
    $exec = $conn->query($queryList[$i]);
    if(!$exec) die('Could not create database: ' . mysqli_error($conn));
  }

  # echo 'databases created successfully';
?>