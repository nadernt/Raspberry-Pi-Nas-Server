#!/bin/bash
case "$1" in
'uptime')
    uptime -p;;
'media_path')
    lsblk -P;;
'media_info')
    sudo -S df "$2" 2>&1 | sed -e /Filesystem/d;;
'set_ip')
    if grep -Fq "ip=" /boot/cmdline.txt
    then
    sudo -S sed -i -e "s/[0-9]\{1,3\}\.[0-9]\{1,3\}\.[0-9]\{1,3\}\.[0-9]\{1,3\}/$2/g" /boot/cmdline.txt
    else
    # printf instead of echo. Raspberri echo did not recognised echo -n switch
    printf "ip=$2" >> /boot/cmdline.txt    
    fi
;;
'set_samba_path')
    service smbd stop
    sleep 5
    sudo -S sed -i "s/\(path *= *\).*/\1\"$2\"/" /etc/samba/smb.conf 2>&1
    service smbd start
    sleep 30
    sudo reboot
;;
'set_workgroup')
    sudo -S sed -i "s/\(workgroup *= *\).*/\1\"$2\"/" /etc/samba/smb.conf 2>&1;;
'scan')
    sudo -S find "$2" -maxdepth 1 -exec stat --format "%n|%F|%Y|%s"  {} +
;;
'filepathinfo')
    sudo -S find "$2" -maxdepth 1 -exec stat --format "%n|%F|%Y|%s"  {} +
    sudo -S find /media/pi/UBUNTU18_0/ -maxdepth 1 -exec stat --format "%n|%F|%Y|%s"  {} +
;;
'set_wifi_onoff')
if [ $2 = "on" ]; then
    sudo -S rfkill unblock all
else
    sudo -S rfkill block all
fi
;;
'set_wifi_settings')
    sudo -S cp -f wificonf /etc/wpa_supplicant/wpa_supplicant.conf
    sudo wpa_cli -i wlan0 reconfigure
    echo 'Ok'
    sleep 5
    sudo reboot
;;
'get_wifisettings')
    
    iswifiactive=$(sudo rfkill list all | grep -o "Soft blocked: no")
    
    echo "<ACTIVE>"
    
    if [ ${#iswifiactive} -gt 0 ];
    then
        echo "true"
    else
        echo "false"
    fi

    echo "</ACTIVE>"
# this call returns error message if wlan is not enable.
    ssids=$(sudo -S iw dev wlan0 scan 2> /dev/null | grep SSID)
    echo "<SSIDS>"
    echo "$ssids"
    echo "</SSIDS>"
    cursysssid=$(sudo -S cat /etc/wpa_supplicant/wpa_supplicant.conf | grep 'ssid\|psk')
    echo "<CURSSIDPASS>"
    echo "$cursysssid"
    echo "</CURSSIDPASS>"
;;
'set_wifisettings')
    echo "reboot"
;;
'reboot')
    echo "reboot"
    sudo reboot
;;
'shutdown')
echo "shutdown"
    sudo shutdown;;
'test')
if grep -Fq "ip=" /boot/cmdline.txt
then
    echo -n "$2" >> /boot/cmdline.txt
else
    sudo -S sed -i -e "s/[0-9]\{1,3\}\.[0-9]\{1,3\}\.[0-9]\{1,3\}\.[0-9]\{1,3\}/$2/g" /boot/cmdline.txt
fi;;
*)
echo "wrong argument";;
esac