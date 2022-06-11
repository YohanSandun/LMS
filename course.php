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

if($_SERVER["REQUEST_METHOD"] == "GET") {
    if(empty(trim($_GET["id"]))) {
        $course_err = "Course not found";
        $err = true;
    } else {
        $cid = $_GET["id"];
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

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Sharp:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
  <link rel="stylesheet" href="css/style.css">

  <title><?php 
  if (!$err) {
    echo $course_name;
  } else {
    echo $course_err;
  }
  ?> - LMS</title>
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

  <div class="container course-content">
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
      <h1><?php 
        if (!$err) {
            echo $course_name;
        } else {
            echo $course_err;
        } 
  ?></h1>
      <h2 class="text-muted"><?php 
        if (!$err) {
            echo $course_code;
        }
      ?></h2>
      
        <?php 
            if ($err) {
                echo '</main></div><script src="js/main.js"></script></body></html>';
                exit();
            }
        ?>


        <?php 
            $sections = array();

            class Section {
                public $contents;
                public $name;
                public $id;

                public function __construct($sid, $sname) {
                    $this->id = $sid;
                    $this->name = $sname;
                    $this->contents = array();
                }
            }

            class Content {
                public $ctid;
                public $cttype;
                public $text;
                public $assessment;
                public $activate;
                public $completed;

                public function __construct($ctid, $cttype, $text, $assessment, $activate, $completed) {
                    $this->ctid = $ctid;
                    $this->cttype = $cttype;
                    $this->text = $text;
                    $this->assessment = $assessment;
                    $this->activate = $activate;
                    $this->completed = $completed;
                }
            }

            function getSection($sid, $sname) {
                foreach ($GLOBALS['sections'] as $section) {
                    if ($section->id == $sid) {
                        return $section;
                    }
                }
                $section = new Section($sid, $sname);
                array_push($GLOBALS['sections'], $section);
                return $section;
            }

            $sql = "SELECT std_content.sccontent, std_content.sccompleted, content.ctsection, content.cttype, content.cttext, content.ctassess, content.ctactivate, sections.sname FROM std_content INNER JOIN content ON std_content.sccontent = content.ctid INNER JOIN sections on content.ctsection = sections.sid WHERE std_content.scstudent ='".$uid."' AND std_content.sccourse ='".$cid."';";
            
            $result = $link->query($sql);
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    $section = getSection($row['ctsection'], $row['sname']);
                    array_push($section->contents, new Content($row['sccontent'], $row['cttype'], $row['cttext'], $row['ctassess'], $row['ctactivate'], $row['sccompleted']));
                }
            }

            foreach ($sections as $section) {
                echo '<div class="c-section">';
                echo '<div>';
                echo '<h2>'.$section->name.'</h2>';
                foreach ($section->contents as $content) {
                    // Assessment
                    if ($content->cttype == 1) {
                        $sql = "SELECT aname FROM `assessments` WHERE aid='".$content->assessment."';";
                        $result = $link->query($sql);
                        if ($result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) {
                                echo '<div class="content">';
                                echo '<div><img src="images/assessment.png" style="height:2rem; width:2rem; justify-content:center; align-items:center;"/></div>';
                                echo '<div class="title"><h2 style="font-weight:600; font-size:1.1rem;">'.$row['aname'].'</h2></div>';
                                echo '<div class="completed" id="id'.$content->ctid.'">';
                                if ($content->completed == 0) {
                                    echo '<a class="" target="_blank" href="toggle_complete.php?id='.$cid.'&ctid='.$content->ctid.'&val=1"><span class="material-symbols-sharp" onClick="document.querySelector(\'#id'.$content->ctid.'\').querySelector(\'a:nth-child(1)\').classList.toggle(\'hide\');document.querySelector(\'#id'.$content->ctid.'\').querySelector(\'a:nth-child(2)\').classList.toggle(\'hide\');">circle</span></a>';
                                    echo '<a class="hide" target="_blank" href="toggle_complete.php?id='.$cid.'&ctid='.$content->ctid.'&val=0"><span class="material-symbols-sharp" onClick="document.querySelector(\'#id'.$content->ctid.'\').querySelector(\'a:nth-child(1)\').classList.toggle(\'hide\');document.querySelector(\'#id'.$content->ctid.'\').querySelector(\'a:nth-child(2)\').classList.toggle(\'hide\');">check_circle</span></a>';
                                } else {
                                    echo '<a class="hide" target="_blank" href="toggle_complete.php?id='.$cid.'&ctid='.$content->ctid.'&val=1"><span class="material-symbols-sharp" onClick="document.querySelector(\'#id'.$content->ctid.'\').querySelector(\'a:nth-child(1)\').classList.toggle(\'hide\');document.querySelector(\'#id'.$content->ctid.'\').querySelector(\'a:nth-child(2)\').classList.toggle(\'hide\');">circle</span></a>';
                                    echo '<a class="" target="_blank" href="toggle_complete.php?id='.$cid.'&ctid='.$content->ctid.'&val=0"><span class="material-symbols-sharp" onClick="document.querySelector(\'#id'.$content->ctid.'\').querySelector(\'a:nth-child(1)\').classList.toggle(\'hide\');document.querySelector(\'#id'.$content->ctid.'\').querySelector(\'a:nth-child(2)\').classList.toggle(\'hide\');">check_circle</span></a>';
                                }
                                echo '</div></div>';
                            }   
                        }
                    }
                    // TEXT
                    else if ($content->cttype == 2) {
                        $sql = "SELECT ttext FROM `text` WHERE tid='".$content->text."';";
                        $result = $link->query($sql);
                        if ($result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) {
                                echo '<div class="content">';
                                echo '<div>icon</div>';
                                echo '<div><p>'.$row['ttext'].'</p></div>';
                                echo '<div class="completed" id="id'.$content->ctid.'">';
                                if ($content->completed == 0) {
                                    echo '<a class="" target="_blank" href="toggle_complete.php?id='.$cid.'&ctid='.$content->ctid.'&val=1"><span class="material-symbols-sharp" onClick="document.querySelector(\'#id'.$content->ctid.'\').querySelector(\'a:nth-child(1)\').classList.toggle(\'hide\');document.querySelector(\'#id'.$content->ctid.'\').querySelector(\'a:nth-child(2)\').classList.toggle(\'hide\');">circle</span></a>';
                                    echo '<a class="hide" target="_blank" href="toggle_complete.php?id='.$cid.'&ctid='.$content->ctid.'&val=0"><span class="material-symbols-sharp" onClick="document.querySelector(\'#id'.$content->ctid.'\').querySelector(\'a:nth-child(1)\').classList.toggle(\'hide\');document.querySelector(\'#id'.$content->ctid.'\').querySelector(\'a:nth-child(2)\').classList.toggle(\'hide\');">check_circle</span></a>';
                                } else {
                                    echo '<a class="hide" target="_blank" href="toggle_complete.php?id='.$cid.'&ctid='.$content->ctid.'&val=1"><span class="material-symbols-sharp" onClick="document.querySelector(\'#id'.$content->ctid.'\').querySelector(\'a:nth-child(1)\').classList.toggle(\'hide\');document.querySelector(\'#id'.$content->ctid.'\').querySelector(\'a:nth-child(2)\').classList.toggle(\'hide\');">circle</span></a>';
                                    echo '<a class="" target="_blank" href="toggle_complete.php?id='.$cid.'&ctid='.$content->ctid.'&val=0"><span class="material-symbols-sharp" onClick="document.querySelector(\'#id'.$content->ctid.'\').querySelector(\'a:nth-child(1)\').classList.toggle(\'hide\');document.querySelector(\'#id'.$content->ctid.'\').querySelector(\'a:nth-child(2)\').classList.toggle(\'hide\');">check_circle</span></a>';
                                }
                                echo '</div></div>';
                            }   
                        }
                    }
                }
                echo '</div></div>';
            }
        ?>
        
        <!-- <div class="c-section">
            <div>
                <h2>Announcements</h2>
                <div class="content">
                    <div>icon</div>
                    <div>Content lorem</div>
                    <div class="completed" id="i1">
                        <span class="material-symbols-sharp hide" onClick="document.querySelector('#i1').querySelector('span:nth-child(1)').classList.toggle('hide');document.querySelector('#i1').querySelector('span:nth-child(2)').classList.toggle('hide')">circle</span>
                        <span class="material-symbols-sharp" onClick="document.querySelector('#i1').querySelector('span:nth-child(1)').classList.toggle('hide');document.querySelector('#i1').querySelector('span:nth-child(2)').classList.toggle('hide')">check_circle</span>
                    </div>
                </div>
            </div>
        </div> -->

    </main>
    </div>
  <script src="js/main.js"></script>
</body>
</html>