# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

Open for your feature [requests](https://github.com/femto-code/Rasberry-Pi-Dashboard/pulls) or [issues](https://github.com/femto-code/Rasberry-Pi-Dashboard/issues)!
[https://github.com/femto-code/Rasberry-Pi-Dashboard](https://github.com/femto-code/Rasberry-Pi-Dashboard)

## [0.10] - 2021-03-18
> Last release before a major 1.0.0

### Added
- allow changing password from options modal 💯

### Changed
- revamped user config options
- notably updated readme with better structuring and more comprehensive help / info
  - replaced top image (use transparent background) adjusted to Github's web site dark theme

## [0.9.2] - 2021-03-16
### Changed
- updated dependencies (bootstrap-icons and mdtoast)
  - alignment of icons much better! 🎆 👍 💯
- forced toast to use global font of dashboard
- add version string as query on includes to help refresh files (prevent caching files for too long)
- code formatting: use spaces (instead of tabs) consistently
- misc dev env improvements

### Fixed
- fixed icons in overall status view in case of warnings

## [0.9.1] - 2021-03-15
### Fixed
- this is a minor release due to a recent issue. It fixes a bug where the dashboard does not seem to load appropriately.
Please comment on #19 if you have similar issues (or open a new one).

### Changed
- updated Readme with instructions on preparation for correct voltage output. (soon)

## [0.9] - 2021-02-26
### Added
- [Font] added local fonts instead of remote embed (better privacy)
  - use extra folder
- [New] logout button
- show exact user-readable date of upcoming power event

### Changed
- [Design] improved navbar appearance
- [Design] tweaked dark mode
- [Design] grid layout in options modal (on larger screens) and scrollable
- [Design] chart UI improved
- [Design] adjusted login modal appearance (removed useless close button)
- improved error handling on network state or when user is unauthorized
- replaced favicon and app icons as well as more consistent colors (updated manifest)
- drop status mail support for now (SOON)

### Fixed
- [Security] Check user authorization when shutdown action is performed
- [Design] minor glitches (alignment, ratio)

## [0.8] - 2021-02-17
### Added
- new settings form for saving custom warning thresholds and basic dashboard settings (more paramters will come)
- further error handling and indicators that inform user about results or when dashboard is updating

### Changed
- improved Readme with new screenshots
- added updating information (run <code>git pull</code> in a terminal)
- minor code improvements

### Fixed
- [Design] fixed tablet appearance (#9)

## [0.7.2] - 2021-02-14
### Changed
- removed user config files (will be replaced by user-friendly settings form in future)
- improved error handling
- adjusted cpu temperature color indicators and increased default critical value
- miscellaneous improvements, updated Readme

### Fixed
- [IMPORTANT] fixed shutdown not working

## [0.7.1] - 2021-02-07
### Changed
- changed footer layout

### Fixed
- [IMPORTANT] fixed password check always failing

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
