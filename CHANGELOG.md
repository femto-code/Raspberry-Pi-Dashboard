# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

Open for your feature [requests](https://github.com/femto-code/Rasberry-Pi-Dashboard/pulls) or [issues](https://github.com/femto-code/Rasberry-Pi-Dashboard/issues)!
[https://github.com/femto-code/Rasberry-Pi-Dashboard](https://github.com/femto-code/Rasberry-Pi-Dashboard)

## [0.7] - 2021-02-06
- reworked shutdown / power event handling (less problems detecting which shutdown event is scheduled)
- added dark theme option to apply to current system settings
- bug fixes

## [0.6] - 2021-01-31
- new icons (changes and improvements may follow)
- updated dependencies

## [0.5] - 2020-08-25
- Better warnings about system hardware with new modern Toast library
- See the remaining time until the next shutdown/reboot right on the dashboard

### Changed
- design improvements

## [0.4] - 2020-08-17
- new authorization/login modal to secure dashboard
- small shutdown/reboot improvements

## [0.3] - 2020-07-10
- new loading screen
- small design adjustments
- mobile appearence improved

## [0.2] - 2020-06-21
- Dark Theme
- Reworked power options
- updating dependencies
- smaller improvements and translation

## [0.1] - 2020-06-16
First public release on Github, starting new version count with v0.1

## [2.7] - 2020-04-30
### Added
- form to schedule shutdown/reboot

### Changed
- new ChartJS Animation
- code and markup optimization

## [2.7-alpha] - 2020-04-02
### Added
- PREPARED: form to schedule shutdown

### Changed
- updated libraries
- updated images to neat icons
- structure things and versioning consistency

### Fixed
- added delay to power options to prevent server to exit without notice to front end (!)

## [2.6] - 2020-01-01
### Changed
- Small design adjustments
- updated interface
- improved error descriptions to inform user

## [2.5] - 2018-09-30
### Added
- devices connected to RPi are shown now
- Info animation of updating process (Update process optimized)
- Warning notification if something is wrong
- Web Server Info (User, Server name, php etc.)
- Horizontal divider to seperate dynamically updated and static content
- Help modal with loads explanation (->link) and help file with useful information concerning customization
- added send_supportmail() function to send system infos to support
- added Status-Mail function to send RPi data per email by statemail.py (new in folder)
- warning if javascript is disabled
- option when appending ?live=disabled to dashboard.php url the live update is disabled by default
- this changelog (CHANGELOG.md new in folder)

### Changed
- Static-Button to Live-Static-Change-Button
- Color range of cpu temperature indicator to be more acurate
- custom options extended by loads warn size

### Fixed
- calculation of used RAM space (because of certain incompatibilites between the systems with Linux 8/9 and the free command)

## [2.0.1] - 2018-09-15 - [OBLIGATORY BUGFIX Release]
### Added
- Help information if vcgencmd fails
- Customization options under /custom/custom.js by time interval of updating dashboard

### Changed
- Reduced loading time by half of a second
- Translation: 5(15) Mins to 5(15) Min - more space for the data fitting in one single row

### Fixed
- Getting RAM infos (sys_infos.php) by using 'free -m' instead of 'free -mo' that seems to be not available in newer versions
- Permissions of dashboard files causing all components fail -> set within installer/updater

## [2.0.0] - 2017-09-12 - [FIRST Release]
