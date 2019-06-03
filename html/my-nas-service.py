#!/usr/bin/python3
import time
import subprocess
import signal
import time 
from board import SCL, SDA
import busio
from PIL import Image, ImageDraw, ImageFont
import adafruit_ssd1306

stop_loop = False 

def Cal_space():
       cmd = "df -h | grep /media/pi/"
       Disk = subprocess.check_output(cmd, shell=True).decode("utf-8")
       li = Disk.split()
       return li 


# Create the I2C interface.
i2c = busio.I2C(SCL, SDA)
 
disp = adafruit_ssd1306.SSD1306_I2C(128, 32, i2c)
 
# Clear display.
disp.fill(0)
disp.show()
 
width = disp.width
height = disp.height
image = Image.new('1', (width, height))
 
draw = ImageDraw.Draw(image)
 
draw.rectangle((0, 0, width, height), outline=0, fill=0)
 
padding = -2
top = padding
bottom = height-padding
# Move left to right keeping track of the current x position for drawing shapes.
x = 0
 
font = ImageFont.load_default()


def stop(sig, frame):
    global stop_loop
    stop_loop = True
    # Clear display.
    disp.fill(0)
    disp.show()
    exit(0)

signal.signal(signal.SIGTERM, stop)
signal.signal(signal.SIGINT, stop)

while not stop_loop:
    # Draw a black filled box to clear the image.
    draw.rectangle((0, 0, width, height), outline=0, fill=0)
    
    cmd = "hostname -I | cut -d\' \' -f1"
    IP = subprocess.check_output(cmd, shell=True).decode("utf-8")
    cmd = "top -bn1 | grep load | awk '{printf \"CPU Load: %.2f\", $(NF-2)}'"
    CPU = subprocess.check_output(cmd, shell=True).decode("utf-8")
    cmd = "free -m | awk 'NR==2{printf \"Mem: %s/%s MB  %.2f%%\", $3,$2,$3*100/$2 }'"
    MemUsage = subprocess.check_output(cmd, shell=True).decode("utf-8")
    disk_info = Cal_space()
    Disk = 'Disk: {freespace}/{totalspace} {percentuse}'.format(freespace=disk_info[2],totalspace=disk_info[3],percentuse=disk_info[4])
        
    # Write four lines of text.
    
    draw.text((x, top+0), "IP: "+IP, font=font, fill=255)
    draw.text((x, top+8), CPU, font=font, fill=255)
    draw.text((x, top+16), MemUsage, font=font, fill=255)
    draw.text((x, top+25), Disk, font=font, fill=255)

        # Display image.
    disp.image(image)
    disp.show()
    time.sleep(1)
