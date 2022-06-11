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

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Sharp:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
  <link rel="stylesheet" href="css/style.css">

  <title>Dashboard - LMS</title>
</head>
<body>
  <nav>
    <div class="left">
      <div class="brand">
        <img src="images/logo.png" alt="Logo">
        <h3>LMS</h3>
      </div>
      <button id="menu-btn">
        <span class="material-symbols-sharp">menu</span>
      </button>
    </div>
    <div class="right">
      <div class="theme-toggler">
        <span class="material-symbols-sharp active">light_mode</span>
        <span class="material-symbols-sharp">dark_mode</span>
      </div>
      <div class="profile">
        <div class="info">
          <p><b>Yohan Sandun</b></p>
          <small class="text-muted">Student</small>
        </div>
        <div class="profile-photo">
          <img src="images/logo.png" alt="Profile">
        </div>
      </div>
    </div>
  </nav>

  <div class="container">
    <aside>
      <div class="top">
        <div class="brand">
          <img src="images/logo.png" alt="Logo">
          <h3>LMS</h3>
        </div>
        <button id="close-btn">
          <span class="material-symbols-sharp">close</span>
        </button>
      </div>
      <div class="sidebar">
        <a href="#" class="active">
          <span class="material-symbols-sharp">grid_view</span>
          <h3>Dashboard</h3>
        </a>
        <a href="#">
          <span class="material-symbols-sharp">campaign</span>
          <h3>Announcements</h3>
          <span class="announcement-count">5</span>
        </a>
        <a href="#">
          <span class="material-symbols-sharp">calendar_month</span>
          <h3>Calender</h3>
        </a>
        <a href="#">
          <span class="material-symbols-sharp">school</span>
          <h3>My Courses</h3>
        </a>
        <a href="#">
          <span class="material-symbols-sharp">logout</span>
          <h3>Logout</h3>
        </a>
      </div>
    </aside>

    <main>
      <h1>Dashboard</h1>
      <div class="courses">

        <?php

          $sql = "SELECT courses.cid, courses.cname, courses.ccover, courses.ccode, enrolls.eprogress FROM enrolls INNER JOIN courses ON courses.cid = enrolls.ecourse WHERE enrolls.estudent='".$uid."' ORDER BY enrolls.edate DESC";
          $result = $link->query($sql);

          if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
              echo "<div class=\"course\"><a href=\"course.php?id=".$row["cid"]."\">";
              echo "<div class=\"course-cover\" style=\"background-image: url('images/covers/".$row["ccover"]."');\"></div>";
              echo '<div class="course-details"><div class="middle"><div class="progress"><div class="circle-wrap"><div class="circle">';
              $deg = $row["eprogress"]/100*180;
              echo '<div class="mask full" style="transform: rotate('.$deg.'deg);"><div class="fill" style="transform: rotate('.$deg.'deg);"></div>';
              echo '</div><div class="mask half"><div class="fill" style="transform: rotate('.$deg.'deg);"></div></div>';
              echo '<div class="inside-circle">'.$row["eprogress"].'%</div></div></div></div>';
              echo '<div class="right"><h2>'.$row["cname"].'</h2><h3 class="text-muted">'.$row["ccode"].'</h3></div></div></div></a></div>';
            }
          } else {
            echo "0 results";
          }

        ?>

        <!--
        <div class="course">
          <div class="course-cover" style="background-image: url('images/test.jpg');"></div>
          <div class="course-details">
            <div class="middle">
              <div class="progress">
                <div class="circle-wrap">
                  <div class="circle">
                    <div class="mask full" style="transform: rotate(100deg);">
                      <div class="fill" style="transform: rotate(100deg);"></div>
                    </div>
                    <div class="mask half">
                      <div class="fill" style="transform: rotate(100deg);"></div>
                    </div>
                    <div class="inside-circle"> 75% </div>
                  </div>
                </div> 
              </div> 
              <div class="right">
                <h2>Economics and Financial Studies B4 2020</h2>
                <h3 class="text-muted">ITC 1123</h3>
              </div>
            </div> 
          </div> 
        </div> 
        -->
        
      </div> <!-- end courses -->
    </main>
    <div class="right-panel">
      <h1>Assessments</h1>
        <div class="assessments">
            
            <div class="assessment">
              <img src="images/assessment.png" alt="Assesment">
              <div class="assesment-info">
                <h2>Assignment 1</h2>
                <h4 class="text-muted">Software Engineering</h4>
              </div>
            </div>

            <div class="assessment">
              <img src="images/assessment.png" alt="Assesment">
              <div class="assesment-info">
                <h2>Assignment 1</h2>
                <h4 class="text-muted">Software Engineering</h4>
              </div>
            </div>

            <div class="assessment">
              <img src="images/assessment.png" alt="Assesment">
              <div class="assesment-info">
                <h2>Assignment 1</h2>
                <h4 class="text-muted">Software Engineering</h4>
              </div>
            </div>

        </div>
    </div>
  </div>
  <script src="js/main.js"></script>
</body>
</html>