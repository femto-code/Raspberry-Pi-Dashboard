# Raspberry Pi Dashboard

> View RPi system information and hardware statistics on a webpage in your web browser!

![Intro](intro.png?raw=true "Components of this project")

![Preview](preview.png?raw=true "Preview of dashboard in a web browser")

## Installation

### Prequisites

- Running Web Server (Lighttpd or Apache) with PHP installed (5.6 or higher)
- Installed Git (comes preinstalled on Raspberry Pi OS / formerly Raspbian)

### Setup project

- navigate to your web folder (`cd /var/www/html`)
- download this Github repository: `git clone https://github.com/femto-code/Rasberry-Pi-Dashboard.git`
- DONE!
- Open web browser with URL: `http://IP_OF_YOUR_RPI/Raspberry-Pi-Dashboard`
- [OPTIONAL] rename the folder to shorten the address input: `mv /var/www/html/Raspberry-Pi-Dashboard /var/www/html/{subfolder_name}` (Note: replace {subfolder_name} with your wish to enter the web page)