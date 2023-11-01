<?php
require_once __DIR__ . '/../models/Login.php';
require_once __DIR__ . '/../models/LoginDao.php';

$welcomeMessage = '';
if(isset($_SESSION['user_id'])) {
    $user = LoginDao::select($_SESSION['user_id']);
    if (!is_null($user)) {
        $userEmail = LoginDao::select($_SESSION['user_id'])->getEmail() ?? '';
        $welcomeMessage = "Welcome $userEmail";
    }
}
?>
<!-- nav_bar.part.php -->
<nav>
    <span> <?= $welcomeMessage ?> </span> |
    <span>
        <?php
        if(isset($_SESSION['user_id'])) {
            echo "<a href='logout.php'>Logout</a>";
        }
        ?>
    </span>
</nav>
