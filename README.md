# <div align="center">Raspberry Pi Dashboard</div>

<div align="center">View and manage your Pi system through your web browser!</div>
<p align="center"><sub>Written in plain HTML5, CSS3, JavaScript and PHP (backend). <b>No extra software required!</b></sub></p>

![Intro](img/intro.png?raw=true "Components of this project")

## Features

- Live surveillance of RPi hardware (CPU Temperature, frequency, loads etc.)
- Protected access with password login (default: root) ([configure password](https://github.com/femto-code/Rasberry-Pi-Dashboard#configure-password))
- State Mail to send an email containing a summary
- Power (shutdown/reboot) Raspberry Pi with schedule options ([setup instructions](https://github.com/femto-code/Rasberry-Pi-Dashboard#setup-project))
- Web-App capable with mobile integration thanks to responsive design
- Dark Theme (manual/auto)

>Update your existing dashboard setup by navigating to root folder and running `git pull` in the terminal.

## Screenshots

![Preview](img/preview.png?raw=true "Preview of dashboard in a web browser")

| Mobile Light Theme                                | Mobile Dark Theme                               |
|:-------------------------------------------------:|:-----------------------------------------------:|
| ![](img/m2.png?raw=true)                          |                ![](img/m1.png?raw=true)         |

## Download and Installation

### Prequisites

- Running Web Server (Lighttpd or Apache) with PHP installed (5.6 or higher)
- Installed Git (comes preinstalled on Raspberry Pi OS / formerly Raspbian)

### Setup project

- navigate to your web folder (`cd /var/www/html`)
- download this Github repository: `git clone https://github.com/femto-code/Rasberry-Pi-Dashboard.git`
- DONE!
- Open web browser with URL: `http://IP_OF_YOUR_RPI/Raspberry-Pi-Dashboard`
- [OPTIONAL] rename the folder to shorten the address input: `mv /var/www/html/Raspberry-Pi-Dashboard /var/www/html/{subfolder_name}` (Note: replace {subfolder_name} with your wish to enter the web page)
- [SOON] There will be a user-friendly setup page for configuration. (WIP)

### Configure password

>You should change the default password (which is **root**) and a choose a more secure one by following these steps:
1. Go to [https://www.md5-generator.de/](https://www.md5-generator.de/) and generate the MD5 encyrpted passphrase.
2. Open `user-settings.php` on line 13 and replace the passphrase string with the generated one from step 1.
3. Remember password and enjoy!

### Enabling remote shutdown/reboot (OPTIONAL)
> Recommended only, if your RPi is **not** accessible over the Internet!
In order to use the remote power functionality you have to give the user `www-data` advanced rights for running one specific command:
1. Run `sudo visudo` to open the editor for adjusting user rights
2. Be careful what you change here! Just add the following at the **end** of the file: `www-data ALL=NOPASSWD: /sbin/shutdown`
3. Restart your Pi and now shutdown from another device (connected to local network) is possible

## License

[**GPL-3.0**](LICENSE).

---

`Raspberry Pi and the Raspberry Pi Logo are registred trademarks of the Raspberry Pi Foundation`
