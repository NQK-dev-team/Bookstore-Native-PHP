### XAMPP version: 8.0.30 (Apache: 2.4.58, PHP: 8.0.30)

### MySQL version: 8.0.35

### PHP version (for Composer package manager installation): 8.3.0

### Composer version: 2.6.6

### Install all required packages using Composer: `composer install`

###### To fix `The zip extension and unzip/7z commands are both missing` error, go to your PHP 8.3.0 installation folder, find the php.ini file and uncomment this line `;extension=zip`

### Install mkcert (for Windows OS):

**Step 1:** Open window powershell as administrator<br><br>
**Step 2:** Type `choco install mkcert`<br><br>
**Step 3:** Type `mkcert -install`<br><br>

###### For others OSes visit `https://github.com/FiloSottile/mkcert` for specific installation steps<br><br>

### Set up SMTP Gmail server

**Step 1:** Go to `https://myaccount.google.com`<br><br>
**Step 2:** Go to `Security` tab<br><br>
**Step 3:** Click on `2-Step Verification` tab and enable it if you haven't. Then scroll down and choose `App passwords`<br><br>
**Step 4:** Enter `App name` and click `Create`. You will receive an app password (this will be use in the `.env` file later on, so save it)<br><br>

### Set up cron job (for Windows OS)

**Step 1:** Open window search and type `Task Scheduler`<br><br>
**Step 2:** Look to `Actions` menu and choose `Create Task`<br><br>
**Step 3:** At `General` tab, enter `Name` and `Description` field, also set `Configure for` to your current Windows version<br><br>
**Step 4:** At `Triggers` tab, click on `New` and set up the trigger configuration as follow:<br><br>
Step 4.1: Choose `On a schedule` in the `Begin the task` field<br><br>
Step 4.2: Choose `Daily` in `Settings`<br><br>
Step 4.3: Choose the `Start` date (first day to begin using the task) and time (execute the task every day at that time). Also, you can check `Synchronize across time zones` option<br><br>
Step 4.4: Check `Repeat task every` and set to 15 minutes or any value you want, check `for a duration of:` and set to 1 day or any value you want<br><br>
Step 4.5: Check `Stop task if it runs longer than:` and set to 2 hours or any value you want<br><br>
Step 4.6 (optional): You can set the expire time of the task by checking `Expire` field and set the value<br><br>
Step 4.7: Check `Enable`<br><br>
**Step 5:** At `Actions` tab, click on `New`, default `Action` should be `Start a program` if not, set it back. Then follow these steps<br><br>
Step 5.1: `Program/script` value should be the location of `php.exe` file of xampp (example `C:\xampp\php\php.exe`)<br><br>
Step 5.2: `Start in (optional):` value should be the location of `cron` directory of this project (example `C:\example_path\cron\`)<br><br>
Step 5.3: `Add arguments (optional):` value will be the `delete_account.php` file<br><br>
**Step 6:** Repeate step 5 but replace with the `discount_notify.php` file at step 5.3<br><br>
**Step 7:** At `Conditions` tab check `Start the task only if the computer is on AC power` and uncheck `Stop if the computer switches to battery power` (the second field is optional)<br><br>
**Step 8:** At `Settings` check these fields: `Allows task to be run on demand`, `Run task as soon as possible after a scheduled start is missed`, `Stop the task if it runs longer than:` (2 hours or any value), `If the running task does not end when requested, force it to stop`, `If the task is not scheduled to run again, delete it after:` (30 days or any value), `If the task is already running, then the following rule applies:` (Do not start a new instance)<br><br>
**Step 9:** Save the config<br>
###### For other OSes, you can look up on the internet for the set up steps<br><br>

### Steps to config the web server before running (apply for Windows OS, other OSes can be achieved with the same procedure):

**Step 1:** Fetch the source code of this repository to your local machine (example path will be `C:\example_path` for better demonstation).<br><br>
**Step 2:** Create a `.env` file base on `.env.example` file and set up your own values.<br><br>
**Step 3:** Create a self-signed SSL certificate, go to `cert` directory by typing `cd cert` in the terminal and then type in this line `mkcert -key-file key.pem -cert-file cert.pem localhost 127.0.0.1 ::1 www.demo.bookstore.com [your_ip_address]` (`your_ip_address` is optional. This step is only used for development, production must not use this step)<br><br>
**Step 4:** Create three log files named `error.log`, `access.log` and `ssl_request.log` in `C:\example_path\log`<br><br>
**Step 5:** Locate the apache server installation directory (for example `C:\xampp\apache`)<br><br>
**Step 6:** Check for modules and includes, open `httpd.conf` file from the `conf` directory of your apache installation directory, and uncomment these groups if they are commented

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

**Step 7:** Add virtual host, open `httpd-vhosts.conf` file from the `conf\extra` directory of your apache installation directory, add the following lines

```
<VirtualHost *:443>
ServerAdmin <your email address>
DocumentRoot "C:\example_path"
ServerName https://www.demo.bookstore.com

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

    <Directory "C:\example_path\vendor">
        AllowOverride none
        Require all denied
    </Directory>

    <Directory "C:\example_path\tool\php">
        AllowOverride none
        Require all denied
    </Directory>

    <Directory "C:\example_path\cron">
        AllowOverride none
        Require all denied
    </Directory>

    <Files "composer.*">
        Require all denied
    </Files>

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

Replace `C:\example_path` with the directory of this project in your machine and `<your email address>` with your designated email address to finish setting up this step.<br>

**Step 8:** Update Hosts File, open this file at this path `C:\Windows\System32\drivers\etc\hosts` (usually the case) as an administrator and add these lines at the bottom of the file

```
# Map www.demo.bookstore.com to localhost
127.0.0.1 www.demo.bookstore.com
::1 www.demo.bookstore.com
```

This only apply for development stage, production stage should skip this<br><br>
**Step 9:** Restart apache server (by using XAMPP for example)<br><br>
**Step 10:** Go to https://www.demo.bookstore.com, https://localhost, https://127.0.0.1 or https://[::1]<br><br>
