<?php
session_start();
 
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <link rel="apple-touch-icon" sizes="180x180" href="image/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="image/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="image/favicon-16x16.png">
    <link rel="manifest" href="image/site.webmanifest">
    <link rel="mask-icon" href="image/safari-pinned-tab.svg" color="#5bbad5">
    <link rel="shortcut icon" href="image/favicon.ico">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="msapplication-config" content="image/browserconfig.xml">
    <meta name="theme-color" content="#ffffff">
    <title>Admin Panel v1.2</title>
    <link href="./lib/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <script src="./lib/js/jquery-3.4.1.min.js"></script>
    <script src="./lib/vendor/bootstrap/js/bootstrap.min.js"></script>
    <link href="./lib/css/simple-sidebar.css" rel="stylesheet">
    <style>
    .side-font-color {
        color: LIGHTSTEELBLUE;
    }

    .side-font-color:hover {
        color: SNOW;
    }
    </style>
</head>

<body>

    <div class="d-flex" id="wrapper">

        <!-- Sidebar -->
        <div class="bg-dark" id="sidebar-wrapper">
            <div class="sidebar-heading" style="color:white; text-align: left; font-size:larger;"><b>Admin Panel</b>
            <img src="image/pen-drive.png" style="width:32px;height:32px;" alt="Logo">
            </div>
            <div class="list-group">
            <a href="index.php" class="list-group-item list-group-item-action side-font-color bg-dark">Files</a>
                <a href="admin.php"
                    class="navbar-dark list-group-item list-group-item-action side-font-color bg-dark">Dashboard</a>
                <a href="maintenance.php"
                    class="list-group-item list-group-item-action side-font-color bg-dark">Maintenance</a>
                <a href="help.php" class="list-group-item list-group-item-action side-font-color bg-dark">Help</a>
            </div>
        </div>
        <!-- /#sidebar-wrapper -->

        <!-- Page Content -->
        <div id="page-content-wrapper">
            <nav class="navbar navbar-expand-sm bg-dark navbar-dark">
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav ml-auto mt-2 mt-lg-0">
                    <div class="btn-group">
                            <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false">
                                User&nbsp;<?php echo ucfirst(htmlspecialchars($_SESSION["username"])); ?>
                            </button>
                            <div class="dropdown-menu  dropdown-menu-right">
                                <a class="dropdown-item" href="logout.php">Logout</a>
                                <a class="dropdown-item" href="reset-password.php">Change Password</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="help.php">Help</a>
                            </div>
                        </div>
                    </ul>
                </div>
            </nav>

            <div class="container-fluid">
            <div class="text-center" id="loading-img">
            <img src="image/loading.gif" alt="Loading" width="42" height="42"> 
            </div>
                <h1 class="mt-4"><b><?php echo ucfirst(htmlspecialchars($_SESSION["username"])); ?></b> Welcome.</h1>
                <p><b>Important:</b> Please just connect one external storage to system.</p>
                <div class="alert" id="media_path_info" role="alert"></div>
                <p id="p_progress"><div class="progress" id ="progress" style="height: 25px;"></div></p>
                <p><div class="alert" style="padding:10px" id="media_info_detail" role="alert"></div></p>
                <p><div class="alert alert-primary" id="uptime" role="alert"></div></p>

                <p><button class="btn btn-primary" id="refresh_me" onClick="location.reload(true)">Refresh</button></p>
            </div>
        </div>
        <!-- /#page-content-wrapper -->
    </div>
    <!-- /#wrapper -->
<script>
var imgloading = document.getElementById("loading-img");
var xhr = new XMLHttpRequest();

xhr.onload = function () {

	if (xhr.status >= 200 && xhr.status < 300) {
		//console.log('success!', xhr);
        var myArr = JSON.parse(this.responseText);
        allInfo(myArr);
    }
    else if (xhr.status == 401 || xhr.status == 403) {
                window.location.replace("login.php");
    } else {
		console.log('The request failed!');
    
    }
    imgloading.style.display = "none";
};

xhr.open('GET', '/api/all?_=' + new Date().getTime());
xhr.send();

function allInfo(arr) {

    var v = document.getElementById("media_path_info"); 
    v.className ='';
    if(arr.message.media_path.length > 0)
    {
        console.log('success!', xhr.response);
        console.log(arr.message.media_path[0]);
        v.className += "alert alert-success";
            v.innerHTML = "<strong>External Storage Path:</strong> " + arr.message.media_path[0] + "</a>";
            for(i = 0; i < arr.message.media_path.length; i++) {
                console.log(arr.message.media_path[i]);
            }

     /**
     *  return {Size  Used Avail Use%} 
     */
    var occupied_space_no_percent = arr.message.media_info[3].replace(/%/g, "")
    var class_user_prog_color; 
    if(occupied_space_no_percent <=30)
        class_user_prog_color = "progress-bar bg-success";
    else if(occupied_space_no_percent <=40)
        class_user_prog_color = "progress-bar bg-info";
    else if(occupied_space_no_percent <=75)
        class_user_prog_color = "progress-bar bg-warning"; 
    else 
        class_user_prog_color = "progress-bar bg-danger";
    
    var elprg = document.getElementById("progress"); 
    elprg.innerHTML = '<div class="' + class_user_prog_color+ '" role="progressbar"' + 
    'id="progress_capacity" style="width: ' + arr.message.media_info[3] + ';" ' +
    'aria-valuemin="0"' +
    'aria-valuemax="100">' + arr.message.media_info[3] + '</div>';
    var elmediadetail = document.getElementById("media_info_detail"); 
    elmediadetail.className += "alert alert-info";
    elmediadetail.innerHTML = '<strong>External storage details:</strong><br>' + 
    '<em>Total size:</em> ' +  arr.message.media_info[0] + '<br>' +
    '<em>Used:</em> ' +  arr.message.media_info[1] + '<br>' +
    '<em>Physically Available:</em> ' +  arr.message.media_info[2] + '<br>';
    }
    else {
        v.className += "alert alert-danger";
            v.innerHTML = "<strong>Note:</strong> The external media is not exist!</a>.";
            removeElement("p_progress");
            removeElement("progress");
            removeElement("media_info_detail");
    }

    var elmeuptime = document.getElementById("uptime"); 
    elmeuptime.innerHTML = "<strong>Machine up time:</strong> " + arr.message.uptime.replace('up','');
}

function removeElement(elementId) {
    var element = document.getElementById(elementId);
    element.parentNode.removeChild(element);
}
    </script>
</body>