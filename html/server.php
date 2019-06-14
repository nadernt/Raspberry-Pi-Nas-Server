<?php
class server
{
    public function ListOfNetworkAdapters(){
        
        $command = "/bin/sh /var/www/html/syscmd.sh list_network_devices";
        exec($command, $output, $return);
        if (!$return) {
                
            
            $arrout=array();
            for($i =0 ; $i<sizeof($output); $i++)
            {
                
                $str = substr_replace($output[$i],"",strpos($output[$i],"/"),strlen($output[$i]));
                
                if(strpos($str,"127.0.0.1")===false)
                {
                    array_push($arrout,ucfirst($str));
                }
                
            }
            echo nl2br(implode("\r\n",$arrout));
        } else {
            return false;
        }
    }
    
    public function GetEthernet(){

        $command = "/bin/sh /var/www/html/syscmd.sh list_network_devices";
        exec($command, $output, $return);
        if (!$return) {
            $str;
            for($i =0 ; $i<sizeof($output); $i++)
            {
                
                $str = substr_replace($output[$i],"",strpos($output[$i],"/"),strlen($output[$i]));
                
                if(strpos($str,"eth")!==false)
                {
                break;
                }
                
            }

            $arrOut = explode(" ",$str);
            echo $arrOut[1];

        } else {
            return false;
        }
    }
    // Function for basic field validation (present and neither empty nor only white space)
    private function IsNullOrEmptyString($str){
        return (!isset($str) || trim($str) === '');
    }

    public function get_wifi_settings()
    {
        $command = "/bin/sh /var/www/html/syscmd.sh get_wifisettings";
        exec($command, $output, $return);

        if (!$return) {
            // echo '<pre>';
            // var_dump( $output);
            // echo '</pre>';
             
           $arr_ssids= array();
           $arr_active= array();
           $arr_usenamepass= array();
           $a=array();
           $b=array();
            for($i=0;$i<sizeof($output);$i++){
                
                if(strpos($output[$i], "<ACTIVE>") !== false)
                {
                    if(strpos(trim($output[$i+1]),"true")!==false)
                        $arr_active['active']= true;
                    else
                        $arr_active['active']= false;
                    $i++;
                }
                
                if((strpos($output[$i], "<SSIDS>") !== false))
                {
                    //skiping <SSIDS> tag
                    $i +=1;
                    for(;$i<sizeof($output);$i++)
                    {
                        if((strpos($output[$i], "</SSIDS>") !== false)){
                            $arr_ssids ["detected_ssids"] = $a;
                            break;
                        }
                        $ssid = trim(str_replace("SSID:","",trim($output[$i],"\"")));
                        // filters ssids with blank name 
                        if(!$this->IsNullOrEmptyString($ssid))
                            array_push($a,trim(str_replace("SSID:","",trim($output[$i],"\""))));
                    }
                }

                if(strpos($output[$i], "<CURSSIDPASS>") !== false)
                {
                    //skiping <CURSSIDPASS> tag
                        $i +=1;
                        $b["ssid"] = trim(str_replace("ssid=","",$output[$i]));
                        $b["ssid"] = trim($b["ssid"],"\"");
                    
                        //check if there is a ssid in the /etc/wpa_supplicant/wpa_supplicant.conf 
                    if (!$this->IsNullOrEmptyString($b["ssid"])) {
                        $b["password"] = trim(str_replace("psk=", "", $output[$i+1]));
                        $b["password"] = trim($b["password"],"\"");
                    }
                    else{
                        $b["ssid"] = null;
                        $b["password"] = null;
                    }
                    $arr_usenamepass ["current_ssi_pass"] = $b;
                    break;
                }
            }
            $arr_output = array_merge($arr_active,$arr_ssids,$arr_usenamepass);
    
            return $arr_output;
        } else {
            return false;
        }
    }

    public function set_wifi_settings($enable,$ssid,$pass,$psk){
        if ($enable==true) {
            $str = "ctrl_interface=DIR=/var/run/wpa_supplicant GROUP=netdev\n"
            ."update_config=1\n\n"
            ."network={\n"
            ."\tssid=\"$ssid\"\n"
            ."\tpsk=\"$pass\"\n"
            ."\tscan_ssid=1\n}";
            //If you want "WPA-PSK" to be effected then uncomment the below line and replace it with top line.
            /*."\tkey_mgmt=" . $psk . "\n}";*/
        } else {
            $str = "ctrl_interface=DIR=/var/run/wpa_supplicant GROUP=netdev\n"
            ."update_config=1\n";
        }
        
        file_put_contents('wificonf', $str);
        $command = "/bin/sh /var/www/html/syscmd.sh set_wifi_settings";
        exec($command, $output, $return);
        echo $output;
        if (!$return) {
            return true;
        } else {
            return false;
        }
    }
    
    public function get_workgroup()
    {
        $variable = explode(PHP_EOL, file_get_contents('/etc/samba/smb.conf'));
        foreach ($variable as $key => $value) {
            if (strpos($value, "workgroup") !== false) {
                if (strpos($value, "#")===false) {
                    $value = str_replace("\"", "", $value);
                    $value = trim(substr($value, strpos($value, "=") + 1));
                    return $value;
                }
            }
        }
    }

    public function set_workgroup($workgroup)
    {
        $command = "/bin/sh /var/www/html/syscmd.sh set_workgroup \"" . $this->escape_space_unixfriendly($workgroup) . "\"";
        exec($command, $output, $return);
        if (!$return) {
            return true;
        } else {
            return false;
        }
    }

    public function get_media_path()
    {
        $command = '/bin/sh /var/www/html/syscmd.sh media_path';
        exec($command, $output_result, $output_variable);
        $outArr = array();
        foreach ($output_result as $key => $value) {
            $strPos = strpos($value, 'media');
            if ($strPos !==false) {
                $startPos = $strPos - 1;
                $str =  substr($value, $startPos);
                array_push($outArr, str_replace('"', '', $str));
            }
        }

        return $outArr;
    }
    
    function convert_filesize($bytes, $decimals = 2){
        $size = array('B','kB','MB','GB','TB','PB','EB','ZB','YB');
        $factor = floor((strlen($bytes) - 1) / 3);
        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$size[$factor];
    }

    public function apply_new_ip($ip)
    {
        $ip = str_replace('_', '.', $ip);

        // If the ip address is not valid then replace with default ip.
        if (ip2long($ip)===false) {
            $ip = '169.254.61.78';
        }

        $command = "/bin/sh /var/www/html/syscmd.sh set_ip " . $ip;
    
        exec($command, $output, $return);

        if (!$return) {
            $default_external_drive_path = $this->get_media_path();
            $command = "/bin/sh /var/www/html/syscmd.sh set_samba_path \"" . $this->escape_space_unixfriendly($default_external_drive_path[0]) . "\"";
            exec($command, $output, $return);
            return true;
        } else {
            return false;
        }
    }
    public function set_wifi_onoff($onoff){
        echo gettype($onoff);
        if($onoff=="true")
            $onoff = "on";
        $command = "/bin/sh /var/www/html/syscmd.sh set_wifi_onoff \"" . $onoff . "\"";
        exec($command, $output, $return);
        if (!$return) {
            return true;
        } else {
            return false;
        }
    }

    public function scan($path){
    
    // This pattern search media/pi as whole path (for security)
    $pattern = '/\bmedia\/pi\b/'; 
  
    if (preg_match($pattern, $path[0]) == false) { 
        return false;
    } 
    
    // escape the space.
    $argPath = str_replace(" ", "\ ", $path[0])."/";

    $command = "/bin/sh /var/www/html/syscmd.sh scan " . "\"" . $argPath . "\"";
        exec($command, $fileArrOut, $return);

        if (!$return) {
           // array_pop($fileArrOut);
            
            $folder_struct = array();
            foreach ($fileArrOut as $value) {
                
                $stat = explode("|",$value);
                $full_name = str_replace($path[0]."/","",$stat[0]);
                $stat[0] = str_replace($path[0]."/","",$stat[0]);
                if ($stat[1]==="directory") {
                    $stat[1] = true;
                    $stat[3] = "";
                }
                else{
                    $stat[3] = $this->convert_filesize($stat[3]);
                    $stat[1] = false;
                }
                
                $dt = new DateTime("@$stat[2]"); 
                
                array_push($folder_struct,array("name"=>$stat[0],"is_dir"=>$stat[1],"mtime"=>$dt->format('d/m/Y H:i A'),"size"=>$stat[3],"full_name"=>$full_name));
            }
            return $folder_struct;
        } else {
            return false;
        }
    }

   public function get_mime_type($filename) {
        $idx = explode( '.', $filename );
        $count_explode = count($idx);
        $idx = strtolower($idx[$count_explode-1]);
    
        $mimet = array( 
            'txt' => 'oi oi-document',
            'htm' => 'oi oi-document',
            'html' => 'oi oi-document',
            'php' => 'oi oi-document',
            'css' => 'oi oi-document',
            'js' => 'oi oi-document',
            'json' => 'oi oi-document',
            'xml' => 'oi oi-document',
            'swf' => 'oi oi-document',
            'flv' => 'oi oi-document',
    
            // images
            'png' => 'oi oi-image',
            'jpe' => 'oi oi-image',
            'jpeg' => 'oi oi-image',
            'jpg' => 'oi oi-image',
            'gif' => 'oi oi-image',
            'bmp' => 'oi oi-image',
            'ico' => 'oi oi-image',
            'tiff' => 'oi oi-image',
            'tif' => 'oi oi-image',
            'svg' => 'oi oi-image',
            'svgz' => 'oi oi-image',
    
            // archives
            'zip' => 'oi oi-bolt',
            'rar' => 'oi oi-bolt',
            'exe' => 'oi oi-bolt',
            'msi' => 'oi oi-bolt',
            'cab' => 'oi oi-bolt',
    
            // audio/video
            'mp3' => 'oi oi-musical-note',
            'wav' => 'oi oi-musical-note',
            'qt' => 'oi oi-video',
            'mov' => 'oi oi-video',
    
            // adobe
            'pdf' => 'oi oi-document',
            'psd' => 'oi oi-document',
            'ai' => 'oi oi-document',
            'eps' => 'oi oi-document',
            'ps' => 'oi oi-document',
    
            // ms office
            'doc' => 'oi oi-document',
            'rtf' => 'oi oi-document',
            'xls' => 'oi oi-document',
            'ppt' => 'oi oi-monito',
            'docx' => 'oi oi-document',
            'xlsx' => 'oi oi-document',
            'pptx' => 'oi oi-monito',
    
    
            // open office
            'odt' => 'oi oi-document',
            'ods' => 'oi oi-document',
        );
    
        if (isset( $mimet[$idx] )) {
         return $mimet[$idx];
        } else {
         return 'oi oi-file';
        }
     }

private function getfilepathinfo($path){

        $command = "/bin/sh /var/www/html/syscmd.sh filepathinfo " . "\"" . $path . "\"";
        exec($command, $fileArrOut, $return);
       /* echo '<pre>';
        var_dump ($fileArrOut);
        echo '</pre>';*/
        if (!$return) {
            //check if it is file or directory.
            $arrout = explode("|",$fileArrOut[0]);
    //        var_dump($arrout);
            //$arrout[1] = ($arrout[1]==="directory")?true:false;
            return $arrout;
        } else {
            return false;
        }

}
    public function shutdown()
    {
        $command = "/bin/sh /var/www/html/syscmd.sh shutdown";
    
        exec($command, $output, $return);

        if (!$return) {
            return true;
        } else {
            return false;
        }
    }

    /**
     *  return {Size  Used Avail Use%}
     */
    public function get_media_info()
    {
        $arrPath = $this->get_media_path();
    
        if (!empty($arrPath[0])) {
            // escape the space.
            $argPath = str_replace(" ", "\ ", $arrPath[0])."/";
            $command = "/bin/sh /var/www/html/syscmd.sh media_info " . $argPath;
            
            $strRes = shell_exec($command);
            $strRes = preg_replace('!\s+!', ' ', $strRes);
            $arrOut = explode(' ', $strRes);
    
            unset($arrOut[0],$arrOut[5],$arrOut[6]);  //Droping Filesystem and Path we don't need them.
            $arrOut = array_values($arrOut); // Re-index the array elements
    return $arrOut;
        } else {
            return null;
        }
    }
    public function get_uptime()
    {
        $command = '/bin/sh /var/www/html/syscmd.sh uptime';
        return exec($command);
    }

    public function escape_space_unixfriendly($stringPath)
    {
        $stringPath = str_replace("/", "\/", $stringPath);
        return str_replace(" ", "\\ ", $stringPath);
    }
}
