### Apache version: 2.4.54 (PHP: 8.2.0)

### MySQL version: 8.0.31

### PHP version (for Composer package manager installation): 8.3.0

### Composer version: 2.6.6

### Install mkcert (for Windows OS):

**Step 1:** Open window powershell as administrator<br>
**Step 2:** Type `choco install mkcert`<br>
**Step 3:** Type `mkcert -install`<br>

### Install all required packages using Composer: `composer install`

### Steps to config apache server before running LOCALLY ONLY (apply for Windows OS, other OSes can be achieved with the same procedure):

**Step 1:** Fetch the source code of this repository to your local machine (example path will be `C:\example_path` for better demonstation).<br>
**Step 2:** Create a self-signed SSL certificate, go to `cert` directory by typing `cd cert` in the terminal and then type in this line `mkcert -key-file key.pem -cert-file cert.pem localhost 127.0.0.1 ::1 www.demo.bookstore.com www.test.bookstore.com [<your ip address>]?` (only use for development, production must not use this step)<br>
**Step 3:** Create three log files named `error.log`, `access.log` and `ssl_request.log` in `C:\example_path\log`<br>
**Step 4:** Locate the apache server installation directory (for example `C:\xampp\apache`)<br>
**Step 5:** Check for modules and includes, open `httpd.conf` file from the `conf` directory of your apache installation directory, and uncomment these groups if they are commented

```
LoadModule log_config_module modules/mod_log_config.so

LoadModule ssl_module modules/mod_ssl.so

Include conf/extra/httpd-vhosts.conf

Include conf/extra/httpd-ssl.conf

<IfModule ssl_module>
SSLRandomSeed startup builtin
SSLRandomSeed connect builtin
</IfModule>
```

<br>

**Step 6:** Add virtual host, open `httpd-vhosts.conf` file from the `conf\extra` directory of your apache installation directory, add the following lines

```
<VirtualHost *:443>
ServerAdmin <your email address>
DocumentRoot "C:\example_path"
ServerName https://www.demo.bookstore.com
ServerAlias https://www.test.bookstore.com

    SSLEngine on
    SSLCertificateFile "C:\example_path\cert\cert.pem"
    SSLCertificateKeyFile "C:\example_path\cert\key.pem"

    CustomLog "C:\example_path\log\ssl_request.log" \
          "%t %h %{SSL_PROTOCOL}x %{SSL_CIPHER}x \"%r\" %b"

    <Directory />
        AllowOverride none
        Require all denied
    </Directory>

    <Directory "C:\example_path">
        #
        # Possible values for the Options directive are "None", "All",
        # or any combination of:
        #   Indexes Includes FollowSymLinks SymLinksifOwnerMatch ExecCGI MultiViews
        #
        # Note that "MultiViews" must be named *explicitly* --- "Options All"
        # doesn't give it to you.
        #
        # The Options directive is both complicated and important.  Please see
        # http://httpd.apache.org/docs/2.4/mod/core.html#options
        # for more information.
        #
        Options Indexes FollowSymLinks Includes ExecCGI

        #
        # AllowOverride controls what directives may be placed in .htaccess files.
        # It can be "All", "None", or any combination of the keywords:
        #   AllowOverride FileInfo AuthConfig Limit
        #
        AllowOverride All

        #
        # Controls who can get stuff from this server.
        #
        Require all granted

        # Set the session save path
        php_value session.save_path "C:\example_path\session"
    </Directory>

    <Directory "C:\example_path\session">
        AllowOverride none
        Require all denied
    </Directory>

    <Directory "C:\example_path\log">
        AllowOverride none
        Require all denied
    </Directory>

    <Directory "C:\example_path\cert">
        AllowOverride none
        Require all denied
    </Directory>

    <Directory "C:\example_path\config">
        AllowOverride none
        Require all denied
    </Directory>

    <Directory "C:\example_path\database">
        AllowOverride none
        Require all denied
    </Directory>

    <Files ".[hH][Tt]*">
        Require all denied
    </Files>

    <Files ".[gG][iI][tT]*">
        Require all denied
    </Files>

    <Files "*.[mM][dD]">
        Require all denied
    </Files>

    <IfModule dir_module>
        DirectoryIndex index.php index.pl index.cgi index.asp index.shtml index.html index.htm \
                   default.php default.pl default.cgi default.asp default.shtml default.html default.htm \
                   home.php home.pl home.cgi home.asp home.shtml home.html home.htm
    </IfModule>

    ErrorLog "C:\example_path\log\error.log"

    <IfModule log_config_module>
        #
        # The following directives define some format nicknames for use with
        # a CustomLog directive (see below).
        #
        LogFormat "%h %l %u %t \"%r\" %>s %b \"%{Referer}i\" \"%{User-Agent}i\"" combined
        LogFormat "%h %l %u %t \"%r\" %>s %b" common

        <IfModule logio_module>
        # You need to enable mod_logio.c to use %I and %O
        LogFormat "%h %l %u %t \"%r\" %>s %b \"%{Referer}i\" \"%{User-Agent}i\" %I %O" combinedio
        </IfModule>

        #
        # The location and format of the access logfile (Common Logfile Format).
        # If you do not define any access logfiles within a <VirtualHost>
        # container, they will be logged here.  Contrariwise, if you *do*
        # define per-<VirtualHost> access logfiles, transactions will be
        # logged therein and *not* in this file.
        #
        #CustomLog "log/access.log" common

        #
        # If you prefer a logfile with access, agent, and referer information
        # (Combined Logfile Format) you can use the following directive.
        #
        CustomLog "C:\example_path\log\access.log" combined
    </IfModule>

</VirtualHost>
```

Replace `C:\example_path` with the directory of this project in your machine to finish setting up this step.<br>

**Step 7:** Update Hosts File, open this file as an administrator `C:\Windows\System32\drivers\etc\hosts` (usually the case) and add these lines at the near bottom

```
# Map www.demo.bookstore.com to localhost
127.0.0.1 www.demo.bookstore.com
::1 www.demo.bookstore.com
# Map www.test.bookstore.com to localhost
127.0.0.1 www.test.bookstore.com
::1 www.test.bookstore.com
```

This only apply for development stage, production stage should skip this<br>
**Step 8:** Restart apache server (by using XAMPP for example)<br>
**Step 9:** Go to https://www.demo.bookstore.com, https://www.test.bookstore.com, https://localhost, https://127.0.0.1 or https://[::1]
