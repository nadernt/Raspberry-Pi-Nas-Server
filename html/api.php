<?php
if (isset($_GET['command']) && $_GET['command']!="") {
    $query=$_GET['command'];
    response($query);
} else {
    response("Invalid Request");
}


    function response($query)
    {
        session_start();
 
        if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
            echo json_response(403, "Forbidden");
            exit;
        }
        include("server.php");
        $server = new server();
        
        switch (strtolower($query)) {
            case "ok":
                echo json_response(200, 'OK');
                break;
            case "all":
                $response['media_info']= $server->get_media_info();
                $response['media_path']= $server->get_media_path();
                $response['uptime'] = $server->get_uptime();
                echo json_response(200, $response);
                break;
            case "set_wifi_settings":
                $response = $server->set_wifi_settings(true,urldecode($_POST['ssid']),urldecode($_POST['password']),"WPA-PSK");
                echo json_response(200, $response);
                break;
            case "get_wifi_ssids":
                echo json_response(200, $server->get_wifi_settings());
                break;
            case "set_wifi_onoff":
                echo json_response(200, $server->set_wifi_onoff($_GET['var1']));
                break;
            case "set_groupip_vars":
                $response['setip'] = $server->apply_new_ip(trim($_GET['var1']));
                $response['setworkgroup'] = $server->set_workgroup(trim($_GET['var2']));
                echo json_response(200, $response);
                break;
            case "is_mounted":
                $response['mounting_point'] = $server->get_media_path();
                echo json_response(200, $response);
                break;
            case "shutdown":
                $response['shutdown'] = true;
                echo json_response(200, $response);
                sleep(5);
                $server->shutdown();
                break;
            case "set_vars":
                $response['scan'] = $server->scan("");
                echo json_response(200, $response);
                break;
            default:
                $response['invalid_request'] = null;
                echo json_response(400, $response);
                break;
        }
    }
    
    function json_response($code = 200, $message = null)
    {
        header_remove();
        // set the actual code
        http_response_code($code);
        // set the header to make sure cache is forced
        header("Cache-Control: no-transform,public,max-age=300,s-maxage=900");

        header('Content-Type: application/json');
        $status = array(
        200 => '200 OK',
        400 => '400 Bad Request',
        422 => 'Unprocessable Entity',
        500 => '500 Internal Server Error'
        );
        // ok, validation error, or failure
        header('Status: '.$status[$code]);
        // return the encoded json
        return json_encode(array(
        'status' => $code < 300, // success or not?
        'message' => $message
        ));
    }
