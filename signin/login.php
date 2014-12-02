<?php
session_start();

$url = "Location: ../";
if (isset($_GET['ref'])) {
    $url.= $_GET['ref'];
}

if ($_REQUEST['usr'] == "Ajax" && $_REQUEST['pswd'] == "594206") {
    $_SESSION['usr'] = "Ajax";
    $_SESSION['pswd'] = "594206";
    header($url);
} elseif ($_REQUEST['usr'] == "Yvonne" && $_REQUEST['pswd'] == "0911") {
    $_SESSION['usr'] = "Yvonne";
    $_SESSION['pswd'] = "0911";
    header($url);
} elseif ($_REQUEST['usr'] == "Amber" && $_REQUEST['pswd'] == "0529") {
    $_SESSION['usr'] = "Amber";
    $_SESSION['pswd'] = "0529";
    header($url);
} elseif ($_REQUEST['usr'] == "Shanon" && $_REQUEST['pswd'] == "0915") {
    $_SESSION['usr'] = "Shanon";
    $_SESSION['pswd'] = "0915";
    header($url);
} elseif ($_REQUEST['usr'] == "Emily" && $_REQUEST['pswd'] == "1029") {
    $_SESSION['usr'] = "Emily";
    $_SESSION['pswd'] = "1029";
    header($url);
} elseif ($_REQUEST['usr'] == "unita" && $_REQUEST['pswd'] == "tacosung") {
    $_SESSION['usr'] = "Unita";
    $_SESSION['pswd'] = "tacosung";
    header($url);
} elseif ($_REQUEST['usr'] == "mars" && $_REQUEST['pswd'] == "0324") {
    $_SESSION['usr'] = "Mars";
    $_SESSION['pswd'] = "0324";
    header($url);
} elseif ($_REQUEST['usr'] == "simon" && $_REQUEST['pswd'] == "abc123") {
    $_SESSION['usr'] = "Simon";
    $_SESSION['pswd'] = "abc123";
    header($url);
} elseif ($_REQUEST['usr'] == "shukai" && $_REQUEST['pswd'] == "0601") {
    $_SESSION['usr'] = "舒凱";
    $_SESSION['pswd'] = "0601";
    header($url);
} elseif ($_REQUEST['usr'] == "mike" && $_REQUEST['pswd'] == "368799") {
    $_SESSION['usr'] = "Mike";
    $_SESSION['pswd'] = "368799";
    header($url);
} elseif ($_REQUEST['usr'] == "guest" && $_REQUEST['pswd'] == "most") {
    $_SESSION['usr'] = "Guest";
    $_SESSION['pswd'] = "most";
    header($url);
} elseif ($_REQUEST['usr'] == "LOPE" && $_REQUEST['pswd'] == "1314") {
    $_SESSION['usr'] = "LOPE";
    $_SESSION['pswd'] = "1314";
    header($url);
} else {
    header("Location: .");
}
?>