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
            <div>Add your your help contents here.</div>
        </div>
        <!-- /#page-content-wrapper -->
    </div>
    <!-- /#wrapper -->
    <script>
    </script>
</body>