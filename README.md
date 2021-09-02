# <div align="center">Raspberry Pi Dashboard</div>

<div align="center">View and manage your Pi system through your web browser!</div>
<p align="center"><sub>Written in plain HTML5, CSS3, JavaScript and PHP (backend). <b>No extra software required!</b></sub></p>

![Intro](img/intro2.png?raw=true "Components of this project")

## Features

- Live surveillance of RPi hardware (CPU Temperature, frequency, loads etc.) with customizable warning thresholds
- Detailed software/hardware information (web server, PHP, storage / partition workload, core voltage, plugged USB devices, kernel version, model specifications, OS, CPU, network config)
- Protected access with password login (default: root) ([configure password](https://github.com/femto-code/Raspberry-Pi-Dashboard#configure-password-manually))
- Power (shutdown/reboot) Raspberry Pi with schedule options ([setup instructions](https://github.com/femto-code/Raspberry-Pi-Dashboard#enable-shutdown--reboot-optional))
- Mobile WebApp integration
- Responsive design layout allows usage on all screen sizes
- Dark Theme (manual / auto)

>Update your existing dashboard setup by navigating to project root folder and running `git pull` in the terminal.

## ❗ If you run into issues and getting error message "Permission denied" or "unable to open file" [see issue #22](https://github.com/femto-code/Raspberry-Pi-Dashboard/issues/22) for help and instructions to solve. ❗ 

## Screenshots

![Preview](img/preview.png?raw=true "Preview of dashboard in a web browser")

| Mobile Light Theme                                | Mobile Dark Theme                               |
|:-------------------------------------------------:|:-----------------------------------------------:|
| ![](img/m2.png?raw=true)                          |                ![](img/m1.png?raw=true)         |

## Download and Installation

### Prequisites

- Running Web Server (e.g. Lighttpd or Apache) with PHP installed (>=v5.6)
- Installed Git (comes preinstalled on Raspberry Pi OS / formerly Raspbian as well as other linux distros)
- Valid permissions within /var/www/html

#### Valid permissions

The following configuration is the commonly recommended set-up for your web server folder and is meant to be secure to a certain level (with the minimum need of permissions):

1. First, make sure `www-data` group exists and add your current login:<br>
_(if you are using another distro than Raspbian/Raspberry Pi OS the username of web server can differ)_<br>
`sudo groupadd www-data`<br>
`sudo usermod -a -G www-data www-data`<br>
`sudo usermod -a -G www-data <yourlogin>` (replace `<yourlogin>` with your username)

2. Second, give the ownership of web folder to the `www-data` group and *your* user.<br>The following commands ensure that you have _full_ access on the files within the web folder while group members (`www-data`) cannot edit/write for security reasons:<br>
`sudo chown -R <yourlogin>:www-data /var/www/html` (replace `<yourlogin>` with your username)<br>
`sudo chmod -R 755 /var/www/html`

3. Reboot your RPi (for permission changes to take effect)

### Setup project

- navigate to your web folder (`cd /var/www/html`)
- clone this Github repository: `git clone https://github.com/femto-code/Rasberry-Pi-Dashboard.git <your_subfolder_name>`
- Run
  - `sudo chown -R <yourlogin>:www-data /var/www/html/<your_subfolder_name>` and<br>(replace `<yourlogin>` with your username)
  - `sudo chmod -R 775 /var/www/html/<your_subfolder_name>`
- to set correct permissions (this will allow the `www-data` user exclusively to write/edit files **only** in your _dashboard folder_)!
> An erroneous permission typically results in the situation where the user responsible for web server (e.g. `www-data`) does not have rights to create/modify the local config file for saving your dashboard adjustments (your custom thresholds, password etc.). In this case, the dashboard won't work at all and throwing this error.
- **DONE!** Open web browser with URL: `http://IP_OF_YOUR_RPI/<your_subfolder_name>`
> Note: replace <your_subfolder_name> with your choice of subfolder name accordingly. You can also rename the base folder at any time afterwards.

## Configuration / Help

#### Core voltage (or other hardware info) output is not shown (optional)
- If you want to see advanced hardware information (core voltage, model information) on your dashboard instance:<br>run `sudo usermod -aG video www-data` in a terminal
>If you do not use Raspbian (or any other RasPi distro) like Ubuntu, you do have to install `libraspberrypi-bin` by running `sudo apt install libraspberrypi-bin`.

- background: The `vcgencmd` command (specifically dedicated to RPi firmware) is a system command that requires certain hardware rights. Therefore one has to grant this particular right (to read hardware info) to e.g. `www-data` (under which web server is running). This is achived by adding this user to a system group called video, which the standard user pi is part of by default.
- in case of problems: please comment on [#12](https://github.com/femto-code/Raspberry-Pi-Dashboard/issues/12) (or [new issue](https://github.com/femto-code/Raspberry-Pi-Dashboard/issues/new))

#### Enable shutdown / reboot (optional)

> Recommended only, if your RPi is **not** accessible over the Internet!

In order to use the remote power functionality you have to give the user `www-data` advanced rights for running one specific command:
1. Run `sudo visudo` to open the editor for adjusting user rights
2. Be careful what you change here! Just add the following at the **end** of the file: `www-data ALL=NOPASSWD: /sbin/shutdown`
3. Restart your Pi and now shutdown from another device (connected to same local network like your Pi) is possible

#### Configure password (manually)

>You should change the default password (which is **root**) and a choose a more secure one by following the following steps.

>Please be aware that there is a more user-friendly way by using the Dashboards options modal. The manual way of changing the password might be helpful
>- in case of wrong permissions (`www-data` cannot change settings for you - [see issue #22](https://github.com/femto-code/Raspberry-Pi-Dashboard/issues/22) for help and instructions to solve)
>- if password is unknown (and access to Dashboard therefore impossible)

1. Go to [https://www.md5-generator.de/](https://www.md5-generator.de/) and generate the MD5 encyrpted passphrase.
2. Open `local.config` (dynamically created at first start) and apply your custom passphrase string (generated in step 1) as follows (don't alter other lines):
```
[...]
'general' =>
  array (
    [...]
    'pass' => 'YOUR_MD5_PASSPHRASE_HERE',
    [...]
  ),
  [...]
```

4. Remember password and enjoy!

## License

[**GPL-3.0**](LICENSE).

---

`Raspberry Pi and the Raspberry Pi Logo are registred trademarks of the Raspberry Pi Foundation`
