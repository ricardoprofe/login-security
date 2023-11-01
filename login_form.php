<?php declare(strict_types=1);
session_start();
require_once __DIR__ . '/models/Login.php';
require_once __DIR__ . '/models/LoginDao.php';

// define variables and set to empty values
$emailErr = $passErr = $opMsg = "";
$err = false; //variable to check if there have been errors
$login = new Login();

//var_dump($_POST);

//Read id from login_list
if($_SERVER['REQUEST_METHOD'] == "GET") {
    if (!empty($_REQUEST["id"])) {
        $login = LoginDao::select($_REQUEST['id']);
    } else {
        //Read id from session
        if(isset($_SESSION['user_id'])){
            $login = LoginDao::select($_SESSION['user_id']);
        }
    }
}

if(isset($_SESSION['user_id'])) {
    $user = LoginDao::select($_SESSION['user_id']);
    $userRole = $user->getRole();
    if($userRole != 'admin' && $login->getId() != $user->getId()){
        //Has the role user and the same id
        header('Location: login_form.php');
    }
} else {
    //No authenticated user
    header('Location: login_access_form.php');
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if(empty($_POST['id'])) {
        $login->setId(0);
    } else {
        $login->setId((int) $_POST['id']);
    }

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
        $login->setPassword(trim(strip_tags($_POST['pass'])));
        $_SESSION['pass'] = $login->getPassword();
        //Check if the password has at least 8 chars
        if (strlen($login->getPassword()) < 8) {
            $passErr = "* The password must have at least 8 characters";
            $err = true;
        }
    }

} else {
    $err = true;
}

if (!$err) {
    if(isset($_POST['submit']) ){
        if($login->getId() == 0){
            //New login
            try {
                LoginDao::insert($login);
                $opMsg = "New login inserted";//If no errors, make a new empty login to clear the fields
                $login = new Login();
            } catch (Exception $e) {
                echo "Error inserting login: " . $e->getMessage();
            }
        } else {
            //Update Login
            LoginDao::update($login);
            $opMsg = "Login updated";
            $login = new Login();
        }

    }

    if(isset($_POST['delete'])  && $login->getId() != 0) {
        //Delete login
        LoginDao::delete($login);
        $opMsg = "Login deleted";
        header('Location: logins_list.php');
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Login</title>
    <meta name="author" content="Ricardo Sanchez">
    <meta name="description" content="a sample form to show database operations">
    <link rel="stylesheet" type="text/css" href="./main.css">
</head>
<body>
<?php include __DIR__ . '/partials/nav_bar.part.php'; ?>
<h1>Edit login</h1>
<p> <?= $opMsg ?> </p>

<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>"  enctype="multipart/form-data">
    <label> ID: <br>
        <input type="text" name="id" readonly value="<?= $login->getId();?>">
    </label>
    <label> E-mail: <br>
        <input type="text" name="email" value="<?= $login->getEmail();?>">  <span class="error"> <?= $emailErr; ?> </span>
    </label>
    <label>Password: <br>
        <input type="text" name="pass" value="<?= $login->getPassword();?>"> <span class="error"> <?= $passErr; ?> </span>
    </label>

    <input type="submit" name="submit" value="Save">
    <input type="submit" name="delete" value="Delete" <?= $login->getId() == 0 ? "disabled" : "" ?> >
</form>

<p class="centered"> <a href="logins_list.php">Return to list </a></p>

</body>
</html>
