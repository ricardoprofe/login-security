<?php declare(strict_types=1);
session_start();

require_once __DIR__ . '/models/Login.php';
require_once __DIR__ . '/models/LoginDao.php';

// define variables and set to empty values
$emailErr = $passErr = $imageErr = $opMsg = $clearPassword = "";
$err = false; //variable to check if there have been errors
$login = new Login();

if ($_SERVER["REQUEST_METHOD"] == "POST") {


    if(empty(trim(strip_tags($_POST['email'])))) {
        $emailErr = "* Email is required";
        $err = true;
    } else {
        $login->setEmail(trim(strip_tags($_POST['email'])));
        $_SESSION['email'] = $login->getEmail();
        //Check if the email is well-formed
        if (!filter_var($login->getEmail(), FILTER_VALIDATE_EMAIL)) {
            $emailErr = "* Invalid email format";
            $err = true;
        }
    }

    if(empty(trim(strip_tags($_POST['pass'])))){
        $passErr = "* Password is required";
        $err = true;
    } else {
        $clearPassword = trim(strip_tags($_POST['pass']));
        $login->setPassword($clearPassword);
        $_SESSION['pass'] = $login->getPassword();
        //Check if the password has at least 8 chars
        if (strlen($login->getPassword()) < 8) {
            $passErr = "* The password must have at least 8 characters";
            $err = true;
        }
    }

    if (count($_FILES) > 0) {
        if (isset($_FILES['image']['tmp_name']) && $_FILES['image']['size'] > 0) {
            $fileType =  strtolower(pathinfo(basename($_FILES['image']['name']),PATHINFO_EXTENSION));

            // Check file size
            if ($_FILES["image"]["size"] > 5000000) { //5MB
                $imageErr = "* File too large. Max 5MB.";
                $err = true;
            }

            //Chek type
            if(!in_array($fileType, ['jpg','png','jpeg','gif', 'bmp']) ){
                $imageErr = '* Sorry, only JPG, JPEG, PNG, BMP & GIF files are allowed to upload.';
                $err = true;
            }

            if (!$err) {
                $imgData = file_get_contents($_FILES['image']['tmp_name']);
                $login->setImage($imgData);
            }
        }
    }

} else {
    $err = true;
}

if (!$err) {
    if(isset($_POST['submit']) ){
        try {
            $idLogin = LoginDao::checkCredential($clearPassword, $login);
            if($idLogin){
                $opMsg = "Login successful";
                $_SESSION['user_id'] = $idLogin;
                header('Location: login_form.php');
            } else {
                $opMsg = "Login failed";
            }
            $login = new Login();
        } catch (Exception $e) {
            echo "Error checking login: " . $e->getMessage();
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <meta name="author" content="Ricardo Sanchez">
    <meta name="description" content="a sample form to show database operations">
    <link rel="stylesheet" type="text/css" href="./main.css">
</head>
<body>
<h1>Login</h1>
<p> <?= $opMsg ?> </p>

<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>"  enctype="multipart/form-data">
    <label> E-mail: <br>
        <input type="text" name="email" value="<?= $login->getEmail();?>">  <span class="error"> <?= $emailErr; ?> </span>
    </label>
    <label>Password: <br>
        <input type="password" name="pass" value="<?= $login->getPassword();?>"> <span class="error"> <?= $passErr; ?> </span>
    </label>

    <input type="submit" name="submit" value="Login">
</form>

</body>
</html>