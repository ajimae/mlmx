<?php
require("connection.php");

if(isset($_POST['submit'])) {

  # get all form fields
  $name = $_POST['name'];
  $email = $_POST['email'];
  $referer = $_POST['referer'];

  # generate referral code
  $ref_code = generateRef();

  $msg = "";
  if($referer != "") {

    # check to ensure provided referral code is valid
    $result = $conn->query("SELECT ref_code FROM preliminary WHERE ref_code = '$referer'");
    if($result->num_rows <= 0) {
      $error = 'provided referral code <strong>'.$referer.'</strong> is invalid or user has completed his cycle for this level';
      die('cannot register user: ' .$error);
    }

    # register
    $exec_ = $conn->query("INSERT INTO preliminary VALUES (null, '$name', '$email', '$ref_code', '$referer')");
    if($exec_) {
      $msg = "user registration successful";
      echo $msg;
    } else {
      die('cannot register user: ' .$conn->error());
    }

    # check to see if he has completed his cycle of 4 referees
    $result = $conn->query("SELECT referer FROM preliminary WHERE referer = '$referer'");
    if($result->num_rows >= 4) {
      # find the referral code owner and move his records to level_one
      $result = $conn->query("SELECT * FROM preliminary WHERE ref_code = '$referer'");
      if($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $_name = $row['name'];
        $_email = $row['email'];
        $_ref_code = $row['ref_code'];
        $_referer = $row['referer'];
        $exec = $conn->query("INSERT INTO level_one VALUES (null, '$_name', '$_email', '$_ref_code', '$_referer')");
        if($exec) {
          # check to see if they are upto 4 in this level and
          $result_ = $conn->query("SELECT * FROM level_one");
          
          # move the first person into level_two
          if($result_->num_rows > 4) {
            $res = $conn->query("SELECT * FROM level_one LIMIT 1");
            $row = $res->fetch_assoc();
            $_exec = $conn->query("INSERT INTO level_two VALUES (null, '".$row["name"]."', '".$row["email"]."', '".$row["ref_code"]."', '".$row["referer"]."')");
            if($_exec) {
              $exec_query_ = $conn->query("DELETE FROM level_one WHERE ref_code = '".$row["ref_code"]."'");
              if($exec_query_) {
                $msg = "<br />user moved to next level two successfully";
                echo $msg;
              } else {
                die('unable to move user to next level: ' .mysqli_error($conn));
              }
            } else {
              die('unable to move user to next level: ' .mysqli_error($conn));
            }
          }
        }
        # use this return $exec object and check for total number of users in the level_one (or make a check again level one here).
        if($exec) {
          # delete the record from preliminary
          $exec_query = $conn->query("DELETE FROM preliminary WHERE ref_code = '$_ref_code'");
          if($exec_query) {
            $msg = "<br />user moved to next level one successfully";
            echo $msg;
          } else {
            die('unable to move user to next level: ' .mysqli_error($conn));
          }
        }
      }
    }
  } else {
    # register as new user without a referral
    $exec = $conn->query("INSERT INTO preliminary VALUES (null, '$name', '$email', '$ref_code', '$referer')");
    if($exec) {
      $msg = "user registration successful";
      echo $msg;
    } else {
      die('cannot register user: ' .$conn->error());
    }
  }

  # TODO - make the ref_code column field of level_one table unique and not null

  # if($referer != "") {
  #   $result = $conn->query("SELECT ref_code FROM level_one WHERE ref_code = '$referer'");
  #   if($result->num_rows > 0) {
  #     $query = "INSERT INTO preliminary VALUES (null, '$name', '$email', '$ref_code', '$referer')";
  #     $exec = $conn->query($query);

  #     # TODO - check to see if he (the referral code bearer) has completed his cycle
  #   } else {
  #     $error = 'provided referral code <strong>'.$referer.'</strong> is invalid or user is still not in level one';
  #     die('cannot register user: ' .$error);
  #   }
  # } else {
  #   $query = "INSERT INTO level_one VALUES (null, '$name', '$email', '$ref_code', '$referer')";
  #   $exec = $conn->query($query);
  # }

  # # send the details to database

  # if(!$exec) die('Could not insert data: ' . mysqli_error($conn));
  # echo "user created successfully";

  # # close connection
  # $conn->close();

}

function generateRef() {
  $ref = '';
  $string = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
  $len = count(str_split($string));

  for($i = 0; $i < 10; $i++) {
    $ref .= str_split($string)[mt_rand(0, $len - 1)];
  }
  
  return $ref;
}

?>

<html>
   <head>
      <title>4x4 MLM</title>
   </head>
   <body>
      <form action="signup.php" method="POST">
        <input type="text" name="name" placeholder="Enter name"><br />
        <input type="email" name="email" placeholder="Enter email"><br />
        <input type="text" name="referer" placeholder="Enter referer"><br /><br />
        <input type="submit" name="submit" value="Register">
      </form>
      <div>
        <br />
        <div><strong>Preliminary Users</strong></div>
        <table border="1">
          <theader>
            <td>Name</td>
            <td>Email</td>
            <td>Referral Code</td>
            <td>Referer</td>
          </theader>
          <tbody>
          <?php
            $query_result = $conn->query("SELECT * FROM preliminary");
            while($row = $query_result->fetch_assoc()) {
          ?>
            <tr>
              <td><? echo $row['name'] ?></td>
              <td><? echo $row['email'] ?></td>
              <td><? echo $row['ref_code'] ?></td>
              <td><? echo $row['referer'] ?></td>
            </tr>
          <?php } ?>
          </tbody>
        </table>
      </div>
      <br />
      <br />
      <br />
      <br />
      <div>
        <div><strong>Preliminary Users</strong></div>
        <table border="1">
          <theader>
            <td>Name</td>
            <td>Email</td>
            <td>Referral Code</td>
            <td>Referer</td>
          </theader>
          <tbody>
          <?php
            $query_result = $conn->query("SELECT * FROM level_one");
            while($row = $query_result->fetch_assoc()) {
          ?>
            <tr>
              <td><? echo $row['name'] ?></td>
              <td><? echo $row['email'] ?></td>
              <td><? echo $row['ref_code'] ?></td>
              <td><? echo $row['referer'] ?></td>
            </tr>
          <?php } ?>
          </tbody>
        </table>
      </div>
   </body>
</html>