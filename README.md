# Raspberry Pi Dashboard

> View RPi system information and hardware statistics on a webpage in your web browser!

![Intro](intro.png?raw=true "Components of this project")

## Features

- Live surveillance of RPi hardware (CPU Temperature, frequency, loads etc.)
- State Mail to send an email containing a summary
- Power (shutdown/reboot) Raspberry Pi with schedule options ([setup instructions](https://github.com/femto-code/Rasberry-Pi-Dashboard#setup-project))
- Web-App capable with mobile integration and responsive design

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

### Enabling remote shutdown/reboot (OPTIONAL)
> Recommended only, if your RPi is not accessible over the Internet!
In order to use the remote power functionality you have to give the user `www-data` advanced rights:
1. Run `sudo visudo` to open the editor for adjusting user rights
2. Be careful what you change here! Just add the following at the **end** of the file: `www-data ALL=NOPASSWD: /sbin/shutdown`
3. Restart your Pi and now shutdown from another device (connected to local network) is possible

---

`Raspberry Pi and the Raspberry Pi Logo are registred trademarks of the Raspberry Pi Foundation`