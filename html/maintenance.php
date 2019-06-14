<?php
include("server.php");
session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}
$server = new server();
$server_settings = $server->get_wifi_settings();
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
    <title>Maintenance Panel v1.2</title>
    <link href="./lib/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <script src="./lib/js/jquery-3.4.1.min.js"></script>
    <script src="./lib/vendor/bootstrap/js/bootstrap.min.js"></script>
    <link href="./lib/css/simple-sidebar.css" rel="stylesheet">
    <link rel="stylesheet" href="./lib/vendor/font-awesome/css/font-awesome.min.css">
    <style>
    .side-font-color {
        color: LIGHTSTEELBLUE;
    }

    .side-font-color:hover {
        color: SNOW;
    }

    /* The switch - the box around the slider */
    .switch {
        position: relative;
        display: inline-block;
        width: 60px;
        height: 34px;
    }

    /* Hide default HTML checkbox */
    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    /* The slider */
    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        -webkit-transition: .4s;
        transition: .4s;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 26px;
        width: 26px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        -webkit-transition: .4s;
        transition: .4s;
    }

    input:checked+.slider {
        background-color: #2196F3;
    }

    input:focus+.slider {
        box-shadow: 0 0 1px #2196F3;
    }

    input:checked+.slider:before {
        -webkit-transform: translateX(26px);
        -ms-transform: translateX(26px);
        transform: translateX(26px);
    }

    /* Rounded sliders */
    .slider.round {
        border-radius: 34px;
    }

    .slider.round:before {
        border-radius: 50%;
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
                <div class="row">
                    <div class="col-sm-6"
                        style="background-color:yellow; border-color: gainsboro; border-width: thin; border-style: none solid none none;">
                        <h3 class="text-dark">Ethernet Settings</h3>
                    </div>
                    <div class="col-sm-6" style="background-color:yellow;">
                        <h3 class="text-dark">Wifi Settings</h3>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6"
                        style="border-color: gainsboro; border-width: thin; border-style: none solid none none;">
                        <p>Type the default ip address of system in IP4 format and then click on <em>Save and
                                Reboot</em> button to apply the changes.<br>
                            <strong>Note:</strong> After click machine will reboot.</p>
                        <div class="alert alert-primary" role="alert" style="width:50%;">
                            <h5 class="alert-heading">Storage path:</h5>
                            <p><?php echo $server->get_media_path()[0];?></p>
                            <hr>
                            <h5 class="alert-heading">Networks:</h5>
                            <p class="mb-0"><?php echo $server->ListOfNetworkAdapters();?></p>
                        </div>
                        <form class="form" onsubmit="return isValidForm()">
                            <input type="hidden" id="is_mounted" name="is_mounted"
                                value="<?php echo $server->get_media_path()[0];?>">
                            <div class="row">
                                <div class="form-group col-xs-5 col-lg-3">
                                    <label for="ip_address" class="col-form-label-sm"><b>IP Address:</b></label>
                                    <input type="text" pattern="\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}" class="form-control"
                                        id="ip_address" placeholder="IP address" name="ip_address"
                                        value="<?php echo $server->GetEthernet();?>" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-xs-5 col-lg-3">
                                    <label for="work_group" class="col-form-label-sm"><b>Workgroup:</b></label>
                                    <input type="text" class="form-control" id="work_group" placeholder="Workgroup"
                                        style="max-width:auto;" name="work_group" autocomplete="off"
                                        pattern="[a-zA-Z0-9\s]+" value="<?php echo $server->get_workgroup();?>"
                                        required>
                                </div>
                            </div>
                        </form>
                        <button type="submit" id="reboot_ipworkgroup" class="btn btn-primary mb-2"
                            onclick="save_ip()">Save
                            and Reboot</button>
                        <button type="submit" id="shutdown" class="btn btn-danger mb-2" onclick="shutdown()">Shutdown
                            Machine</button>
                    </div>
                    <div class="col-sm-6">
                        <div class="row">
                            <div class="form-group col-xs-5 col-lg-2">
                                <label for="wifi" class="col-form-label-sm"><b>Wifi On/Off:</b></label>
                                <label class="switch">
                                    <input type="checkbox" onclick="turnonofwifi()" id="wifi">
                                    <span class="slider round"></span>
                                </label>
                            </div>
                        </div>
                        <label for="ssids_list" class="col-form-label-sm"><b>Available Networks:</b></label>
                        <form class="form-inline" onsubmit="return isValidForm()">
                            <div class="form-group mb-2">
                                <select class="form-control" id="ssids_list" onchange="selectChanged()">
                                    <?php 
                                        foreach($server_settings['detected_ssids'] as $value)
                                        echo '<option value="' . $value . '">' . $value . '</option>';
                                    ?>
                                </select>
                                <button class="btn btn-primary" onclick="refreshssid()"><i
                                        class="fa fa-repeat"></i></button>
                            </div>
                        </form>
                        <hr>
                        <p>Type the SSID if it is not in the list.</p>
                        <form class="form" onsubmit="return isValidForm()">
                            <div class="row">
                                <div class="form-group col-xs-5 col-lg-3">
                                    <label for="ssid_name" class="col-form-label-sm"><b>SSID (WiFi name):</b></label>
                                    <input type="text" maxlength="32" class="form-control" id="ssid_name"
                                        placeholder="SSID" name="ssid_name" value="<?php 
                                        if($server_settings['active']===true)
                                            if($server_settings['current_ssi_pass']['ssid']!==null)
                                                echo $server_settings['current_ssi_pass']['ssid']
                                        ?>" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-xs-5 col-lg-3">
                                    <label for="passwd" class="col-form-label-sm"><b>Password:</b></label>
                                    <input type="password" class="form-control" id="passwd" placeholder="Password"
                                        name="passwd" autocomplete="off" value="<?php 
                                        if($server_settings['active']===true)
                                            if($server_settings['current_ssi_pass']['ssid']!==null)
                                                echo $server_settings['current_ssi_pass']['password']
                                        ?>" required>
                                </div>
                            </div>
                        </form>
                        <button type="submit" id="reboot_ssidpass" class="btn btn-primary mb-2"
                            onclick="save_wifi_settings()">Save
                            and Reboot</button>
                        <img src="image/loading.gif" alt="Loading" width="32" height="32" id="loading-img">
                    </div>
                    <!-- Modal -->
                    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="myModalLabel">Note</h5>
                                </div>
                                <div class="modal-body">
                                    Page will reload in 15s.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /#container-fluid -->
        </div>
        <!-- /#page-content-wrapper -->
    </div>
    <!-- /#wrapper -->
    <script>
    var counter = 0;

    document.getElementById("wifi").checked = <?php echo json_encode($server_settings['active']);?>;

    show_hideload(false);

    function show_hideload(show_hide_loading) {
        var x = document.getElementById("loading-img");
        if (show_hide_loading === true) {
            x.style.display = "inline";
        } else {
            x.style.display = "none";
        }
    }

    function turnonofwifi() {
        var e = document.getElementById("wifi").checked;
        var xhr = new XMLHttpRequest();

        xhr.onload = function() {

            if (xhr.status >= 200 && xhr.status < 300) {
                console.log('success!', xhr.response);
                $('#myModal').modal('show');
                setTimeout(function() {
                    location.reload(true);
                }, 15000);

            } else if (xhr.status == 401 || xhr.status == 403) {
                window.location.replace("login.php");
            } else {
                console.log('The request failed!');
            }
        };

        xhr.open('GET', '/api/set_wifi_onoff/' + e + '?_=' + new Date().getTime());
        xhr.send();
    }

    function selectChanged() {
        var e = document.getElementById("ssids_list");
        document.getElementById("ssid_name").value = e.options[e.selectedIndex].value;
        document.getElementById("passwd").value = "";
    }

    function save_wifi_settings() {

        if (!document.getElementById("wifi").checked) {
            alert("WiFi is not enabled!");
            return;
        }

        if (!document.getElementById("ssid_name").checkValidity()) {
            alert("SSID is not valid!");
            return;
        }

        if (!document.getElementById("passwd").checkValidity()) {
            alert("Password is not valid!");
            return;
        }

        show_hideload(true);

        var _ssid = document.getElementById("ssid_name").value;
        var _password = document.getElementById("passwd").value;

        var xhr = new XMLHttpRequest();

        xhr.onload = function() {
            if (xhr.status >= 200 && xhr.status < 300) {
                console.log('success!', xhr.response);
            } else if (xhr.status == 401 || xhr.status == 403) {
                window.location.replace("login.php");
            } else {
                console.log('The request failed!');
            }
        };
        if (document.getElementById("wifi").checked)
            blEnableWifi = true;
        else
            elseblEnableWifi = false;

        var theUrl = "/api/set_wifi_settings" + '?_=' + new Date().getTime();

        var data = new FormData();
        data.append('ssid', _ssid.replace(/%20/g, '+'));
        data.append('password', _password.replace(/%20/g, '+'));

        xhr.open("POST", theUrl, true);
        xhr.onerror = function() {
            console.log("** An error occurred during the transaction");
            show_hideload(false);
            counter = 90;
            reboot_counter("reboot_ssidpass");
        };
        xhr.send(data);
    }

    function refreshssid() {
        var select = document.getElementById("ssids_list");

        select.options.length = 0;

        var xhr = new XMLHttpRequest();

        xhr.onload = function() {

            if (xhr.status >= 200 && xhr.status < 300) {
                console.log('success!', xhr.response);
                var myArr = JSON.parse(this.responseText);

                for (index in myArr.message.detected_ssids) {
                    select.options[select.options.length] = new Option(myArr.message.detected_ssids[index], myArr
                        .message.detected_ssids[index]);
                }

            } else if (xhr.status == 401 || xhr.status == 403) {
                window.location.replace("login.php");
            } else {
                console.log('The request failed!');
            }
        };
        xhr.open('GET', '/api/get_wifi_ssids' + '?_=' + new Date().getTime());
        xhr.send();
    }

    function get_wifi_info() {

        var xhr = new XMLHttpRequest();

        xhr.onload = function() {

            if (xhr.status >= 200 && xhr.status < 300) {
                console.log('success!', xhr.response);
            } else if (xhr.status == 401 || xhr.status == 403) {
                window.location.replace("login.php");
            } else {
                console.log('The request failed!');
            }
        };
        xhr.open('GET', '/api/get_wifi' + '?_=' + new Date().getTime());
        xhr.send();
    }

    function shutdown() {
        counter = 90;

        var xhr = new XMLHttpRequest();

        xhr.onload = function() {

            if (xhr.status >= 200 && xhr.status < 300) {

                reboot_counter("shutdown");
                console.log('success!', xhr.response);
            } else if (xhr.status == 401 || xhr.status == 403) {
                window.location.replace("login.php");
            } else {
                console.log('The request failed!');
            }
        };
        xhr.open('GET', '/api/shutdown?_=' + new Date().getTime());
        xhr.send();
    }

    function save_ip() {

        var xhr = new XMLHttpRequest();

        var ip = document.getElementById("ip_address").value;

        ip = ip.split('.').join('_');

        if (!document.getElementById("ip_address").checkValidity()) {
            alert("Ip address is not valid!");
            return;
        }

        if (!document.getElementById("work_group").checkValidity()) {
            alert("Workgroup is not valid!");
            return;
        }

        counter = 90;

        reboot_counter("reboot_ipworkgroup");

        var workgroup = document.getElementById("work_group").value;

        workgroup = workgroup.split('.').join('_');

        xhr.onload = function() {

            if (xhr.status >= 200 && xhr.status < 300) {
                console.log('success!', xhr.response);
            } else if (xhr.status == 401 || xhr.status == 403) {
                window.location.replace("login.php");
            } else {
                console.log('The request failed!');
            }
        };


        xhr.open('GET', '/api/set_groupip_vars/' + encodeURI(ip) + '/' + workgroup + '?_=' + new Date().getTime());
        xhr.send();
    }

    function isValidForm() {
        return false;
    }

    function reboot_counter(element) {

        if ("reboot_ipworkgroup" === element)
            document.getElementById("reboot_ipworkgroup").innerHTML = "Reload on " + counter-- + "s";
        else if ("reboot_ssidpass" === element)
            document.getElementById("reboot_ssidpass").innerHTML = "Reload on " + counter-- + "s";
        else if ("shutdown" === element)
            document.getElementById("shutdown").innerHTML = "Shutdown on " + counter-- + "s";

        if (counter >= 0) {
            setTimeout(function() {
                reboot_counter(element)
            }, 1000);
        } else {
            if ("reboot_ipworkgroup" === element) {
                document.getElementById("reboot_ipworkgroup").innerHTML = "Refreshing the page";
                window.location.replace("index.php");
            } else if ("reboot_ssidpass" === element) {
                document.getElementById("reboot_ssidpass").innerHTML = "Refreshing the page";
                window.location.replace("index.php");
            } else if ("shutdown" === element) {
                document.getElementById("reboot_ipworkgroup").innerHTML = "shutdown";
                document.body.innerHTML = "<h1>Your machine is turned off.</h1>";
            }

        }
    }
    </script>
</body>