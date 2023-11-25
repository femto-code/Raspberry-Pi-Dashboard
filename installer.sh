#!/bin/bash

cecho () {
    declare -A colors;
    colors=(\
        ['black']='\E[0;47m'\
        ['red']='\E[0;31m'\
        ['green']='\E[0;32m'\
        ['yellow']='\E[0;33m'\
        ['blue']='\E[0;34m'\
        ['magenta']='\E[0;35m'\
        ['cyan']='\E[0;36m'\
        ['white']='\E[0;37m'\
    );

    local defaultMSG="No message passed.";
    local defaultColor="black";
    local defaultNewLine=true;

    while [[ $# -gt 1 ]];
    do
    key="$1";

    case $key in
        -c|--color)
            color="$2";
            shift;
        ;;
        -n|--noline)
            newLine=false;
        ;;
        *)
            # unknown option
        ;;
    esac
    shift;
    done

    message=${1:-$defaultMSG};   # Defaults to default message.
    color=${color:-$defaultColor};   # Defaults to default color, if not specified.
    newLine=${newLine:-$defaultNewLine};

    echo -en "${colors[$color]}";
    echo -en "$message";
    if [ "$newLine" = true ] ; then
        echo;
    fi
    tput sgr0; #  Reset text attributes to normal without clearing screen.

    return;
}
cyanprint() { printf "${CYAN}%s${RESET}\n" "$1"; }
_process() {
  #echo "$(date) PROCESSING:  $@"
  echo -e "\n"
  cyanprint " → $@"
}
_success() {
  printf "\n%s✓ Success:%s\n" "$(tput setaf 2)" "$(tput sgr0) $1"
}

cecho -c 'blue' "Welcome to the RPi Dashboard installer!"
read -p "This setup assumes you have a working web server installed that is up and running. Continue? (Y/N): " confirm && [[ $confirm == [yY] || $confirm == [yY][eE][sS] ]] || exit 1
hostn=${hostname}
cecho -c 'blue' "This setup will install the dashboard to /var/www/html. Please choose your subfolder name, so you will be able to call the dashboard at http://$hostn/{your_subfolder_name}"
read -p "Enter custom subfolder name: " subfoldern
cd /var/www/html
git clone https://github.com/femto-code/Raspberry-Pi-Dashboard $subfoldern
_process "Setting up valid permissions for /var/www/html/$subfoldern"
sudo chown -R ${whoami}:www-data /var/www/html/$subfoldern
sudo chmod -R 775 /var/www/html/$subfoldern
_success "Installation done! To access the dashboard open up a web browser with URL: http://$hostn/$subfoldern"
