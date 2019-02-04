#!/bin/sh

# If you would like to do some extra provisioning you may
# add any commands you wish to this file and they will
# be run after the Homestead machine is provisioned.
#
# If you have user-specific configurations you would like
# to apply, you may also create user-customizations.sh,
# which will be run after this script.

if [ ! -f /usr/local/extra_homestead_software_installed ]; then
    echo "Installing extra software"

    # FFMPEG
    sudo apt-get install -y build-essential curl g++

    export AUTOINSTALL="yes"
    VAGRANT_HOME=/home/vagrant

    wget 'https://raw.githubusercontent.com/markus-perl/ffmpeg-build-script/master/web-install.sh' --directory-prefix=${VAGRANT_HOME}
    chmod +x ${VAGRANT_HOME}/web-install.sh
    sudo bash ${VAGRANT_HOME}/web-install.sh

    sudo touch "/usr/local/extra_homestead_software_installed"
fi

exit 0
