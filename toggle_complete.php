<?php
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

require_once "core/db.php";

$uid = $_SESSION["id"];
$cid = 0;
$course_name = $course_code = $course_desc = "";
$err = false;
$ctid = $value = "";

if($_SERVER["REQUEST_METHOD"] == "GET") {
    if(empty(trim($_GET["id"]))) {
        $course_err = "Course not found";
        $err = true;
    } else {
        $cid = $_GET["id"];
        $ctid = $_GET["ctid"];
        $value = $_GET["val"];
    }
}

if (!$err) {
    $result = $link->query("SELECT eid FROM enrolls WHERE estudent='".$uid."' AND ecourse='".$cid."'");
    if ($result->num_rows > 0) {
        $sql = "SELECT ccode, cname, cdesc FROM courses WHERE cid='".$cid."'";
        $result = $link->query($sql);
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $course_name = $row['cname'];
                $course_code = $row['ccode'];
                $course_desc = $row['cdesc'];
            }
        }
    } else {
        $err = true;
        $course_err = "You're not enrolled";
    }
}

if (!$err) {
    $sql = "UPDATE std_content SET sccompleted='".$value."' WHERE scstudent='".$uid."' AND sccontent='".$ctid."'";
    echo $sql;
    mysqli_query($link, $sql);
    echo "<script>window.close();</script>";
}