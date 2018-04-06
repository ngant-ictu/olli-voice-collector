# PHP Framework starter.

### INSTALLATION
    + yum install -y https://dl.fedoraproject.org/pub/epel/epel-release-latest-6.noarch.rpm
    + yum install -y http://rpms.remirepo.net/enterprise/remi-release-6.rpm
    + yum install -y yum-utils
    + yum-config-manager --enable remi-php72
    + yum install -y php php-devel php-mcrypt php-cli php-gd php-curl php-mysql php-ldap php-zip php-fileinfo gcc libtool pcre-devel php-pecl-apcu php-pecl-apcu-bc php-fpm php-mbstring
    + git clone --depth=1 "git://github.com/phalcon/cphalcon.git"
        - cd cphalcon/build
        - sudo ./install
        - echo "extension=phalcon.so" > /etc/php.d/30-phalcon.ini

** On production:
    + Default Display Error = Off
    + Log to syslog
