# Setup

This is a copy of the devcontainer README.
Devcontainers were added to the main repository but are largely undocumented.

#### System requirements for devcontainers

-   Required: 8 GB memory
-   Recommended: 16 GB memory
    If your machine is at 8 GB of memory, close all other apps until installation is complete. Memory will be freed once RevKeep is up and running.
    For 16 GB you can probably keep most apps running except for the most intensive (close docker desktop and any other running VMs, for instance)

### Choose either Podman or Docker as a container manager

It might not be obvious when our organization gets too big to be in violation of [Docker's new subscription policy](https://docs.docker.com/subscription/#:~:text=Docker%20Desktop%20remains%20free%20for,professional%20use%20in%20larger%20enterprises). Which is why there has been interest in moving over to podman. Unfortunately, podman has had performance and stability issues lately on Macs which Docker has not. Thus, for now, we are using docker, but, to keep ourselves future-proof, the install scripts have been written to work for either container manager.

### Setup docker (if chosen)

1. Download and install Docker Desktop (https://www.docker.com/products/docker-desktop/)
2. [Windows only]
    1. [Install WSL 2](https://docs.microsoft.com/en-us/windows/wsl/install)
        - Note: The Ubuntu default option should work fine
        - Note: Be sure to check that [WSL 2 installed, not WSL 1](https://docs.microsoft.com/en-us/windows/wsl/install#check-which-version-of-wsl-you-are-running)
    2. Inside a WSL terminal run `sudo apt update`
        - To get a WSL terminal open, press the Windows key, search and start the Ubuntu application, if you installed Ubuntu. Otherwise, search and start the appropriate Linux application.

### Setup Podman (if chosen)

#### Setup Podman on Windows

1. [Install WSL 2](https://docs.microsoft.com/en-us/windows/wsl/install)
    - Note: The Ubuntu default option should work fine
    - Note: Be sure to check that [WSL 2 installed, not WSL 1](https://docs.microsoft.com/en-us/windows/wsl/install#check-which-version-of-wsl-you-are-running)
2. Inside a WSL terminal run `sudo apt update`
    - To get a WSL terminal open, press the Windows key, search and start the Ubuntu application, if you installed Ubuntu. Otherwise, search and start the appropriate Linux application.
3. Next, run the following

```
echo "deb https://download.opensuse.org/repositories/devel:/kubic:/libcontainers:/stable/xUbuntu_20.04/ /" |
sudo tee /etc/apt/sources.list.d/devel:kubic:libcontainers:stable.list
```

4. Then, install the podman gpg key

```
curl -L "https://download.opensuse.org/repositories/devel:/kubic:\
/libcontainers:/stable/xUbuntu_20.04/Release.key" | sudo apt-key add -
```

5. Then update and upgrade apt-get

```
sudo apt-get update
sudo apt-get -y upgrade
```

6. Then install podman `sudo apt install podman`
7. Finally, run the following two commands

```
sudo chmod 4755 /usr/bin/newgidmap
sudo chmod 4755 /usr/bin/newuidmap
```

#### Setup Podman on Mac

1. Open a terminal and enter `brew install podman`
2. Once installed, enter `podman machine init --memory 4096`
    - Note: From observation, the default memory of 2048 wasn't enough which is why we are using 4096
3. Finally, enter `podman machine start`

### Setup VSCode (all platforms)

1. Make sure python3 (at least 3.8) is installed on your machine (python3 --version)
    - [Windows] This needs to be installed in WSL, check for python3 in a WSL terminal
2. Download and install [Visual Studio Code](https://code.visualstudio.com/)
3. Open VSCode, click on the extensions ribbon on the left and search for and install "ms-vscode-remote.vscode-remote-extensionpack"
4. In VSCode, open the Command Palette (Cmd + Shift + P) and select "Remote Containers: Settings for Remote containers". In the setting "Remote â€º Containers: Docker Path", enter "podman" or "docker" depending on which container manager you're using.
5. In VSCode, open the Command Palette (Cmd + Shift + P) and select the 'Shell Command: install 'code' command in PATH' option.
6. In VSCode, open the Command Palette (Cmd + Shift + P) and select the 'Remote-Containers: Install devcontainer CLI' option.

### Setup project and devcontainer (all platforms)

1. Log-in to Bitbucket, click on your profile icon > personal settings > app passwords > 'Create app password'. Be to sure to check the options "read" and "write" under repositories permissions. Once done, an app password popup will display. Copy the password and keep it in a safe place. This will be the password which you will enter if prompted when running git commands.
2. Clone the RevKeep devcontainer configuration files repository `git clone https://<yourbitbucketuser>@bitbucket.org/revkeep/revkeep-devcontainers.git`
    - [Windows] Be sure to clone the source into the WSL 2 distro.
    - [All others] Clone where appropriate.
3. In the same directory where you cloned, enter `code .`
4. In VS code, open a terminal by navigating to terminal > new terminal.
5. In the terminal, enter `./full-install-<postgres or mssql>.py <docker or podman>`.
    - The script may take about 10 minutes to complete, so plan accordingly.
6. VSCode should refresh, opened **inside** the container with php and other installation dependencies installed.

### Install RevKeep

-   Note: You can skip these steps if using devcontainers

1. Copy `/config/.env.default` to `/config/.env` and adjust values as necessary:
    - Keep all keys in this file and ensure it is not committed to version control.
2. Install PHP dependencies with Composer for back-end / API.
    - Run `php composer.phar install` to install PHP packages.
    - Composer can be updated with `php composer.phar self-update`.
3. For a new, clean database: - Run the `bin/cake migrations migrate` command to set up the database. - Run the default database seed with `bin/cake migrations seed --seed=DefaultDatabaseSeed`.
4. Run `npm install` for front-end build components to be installed.

### Run RevKeep

-   Open two terminals and run
    -   Terminal 1: `npm run start`
    -   Terminal 2: `npm run api`
-   [Extra notes]
    -   Use `npm run production` to build and minify for production use.
    -   `npm run start` is available for development with hot-reloading.
    -   The `watch` and `start` command configuration is defaulted to proxying to the CakePHP CLI server.

You have to run the PHP CLI server bound to IP 0.0.0.0 in order for BrowserSync to be able to proxy requests to the app. Use `/bin/cake server -H 0.0.0.0` to listen on app IPs.

### How to remove project / reinstall / test portions of the installation

The script `./delete-everything.py <docker or podman>` will remove all associated containers, volumes, networks and pods built for the specified container manager. If you have important changes inside the containers, **make sure** to commit your code changes in git and backup the sql-server instance before running this command otherwise changes **will be lost**. Container images **will not** be deleted. This script does not currently handle deleting only parts of a project setup. If you need to test parts of a project setup, some useful terminal commands are as follows:

1. `<docker or podman> container ls -a` -> lists all containers (running and not running)
2. `<docker or podman> container rm -f <container name or ID>` -> delete a container
3. `<docker or podman> volume ls` -> list all volumes
4. `<docker or podman> volume rm <volume name>` -> delete a volume
5. `<docker or podman> <items like network, pod, image, etc> <ls or rm as above>` -> inspect and delete other kinds of items

Before running `./full-install-<postgres or mssql>.py <docker or podman>` a second time, it's **recommended** that you first run `./delete-everything.py <docker or podman>`

### Javascript Debug of Revkeep in [Dev Container] VS Code

1. Install the Vue Devtools for your browser, found here https://devtools.vuejs.org/guide/installation.html
2. Open the command palette (cmd/ctrl+shift+p), search for Debug: Javascript Debug Terminal, open
    - Debug Terminal 1: `npm run start`
3. Open the command palette (cmd/ctrl+shift+p), search for Debug: Javascript Debug Terminal, open
    - Debug Terminal 2: `npm run api`
4. After running `npm run api`, `0.0.0.0:8765` underlined link should print-out in the terminal. Right click on it to open up a browser in debug mode.
5. Try setting a breakpoint, in vs code, source code explorer panel on the left, locate the file assets/js/clients/Search/Patients.vue, open it, click on line 222, you should see a red circle appear for a breakpoint.
6. In the browser, login as an admin, open the client app, upload a pdf, click in the search patients bar, type a letter, your breakpoint should stop code execution.

### Selenium Functional tests

Revkeep follows a BDD (Behavioral Driven Development). Our product team defines functional behavior using Gherkin and tests are written in javascript using Cucumber and Chai. Checkout the Revkeep Selenium Tests repository and readme at https://bitbucket.org/revkeep/revkeep-selenium-tests/src/master/README.md

### Demo Database and Logins

Revkeep maintains a demo database with data for all entities in Revkeep Connect, login with admin user
nabgilby@gmail.com Revkeep22$

### SQL Server backup

1. On your local machine, open the devcontainer configuration files repository in VSCode + open a terminal.
2. With the SQL server container name, run `./backup-database.py <docker or podman>`
3. A folder with the date of the backup is created. Inside the folder will be a \*.bak file that should be dumped and a files folder containing pdf files from the tmp directory for incoming-documents and appeals.

### Restore SQL server from backup

1. Follow the steps in 'Setup MS SQL Server' to get a fresh sql server up and running. Make sure .bak files are populated in the 'backups folder'
2. Run the following: `./restore-database.py <docker or podman>`
3. Once all prompts are completed, the RevKeep database should now be restored. Running RevKeep should reflect the backed-up data.

### pgAdmin

For Postgres, there is a dev container installed for PGADMIN. Your choice if you like to use it or DBeaver. For to access PGADMIN

1. open a browser on http://localhost:5431/
2. login with admin@admin.com Succ9ssREVKEEP
3. Select Add a New Server from the Dashboard QuickLinks
4. General tab:
   Name: revkeep-pgadmin
   Server group: Servers

    Connection tab:
    Hostname/address: host.docker.internal
    port: 5432
    Maintainence database revkeep
    Username: postgres
    password: <DB password set on install>

### DBeaver

The demo database required for the selenium tests is helpful for understanding the intricacies of the product. To explore the database, demo data, ER diagram, easily create, add, update delete data visually, use DBeaver. The Community edition os free.

1. Download and install DBeaver from https://dbeaver.io/download
2. On the projects tab on the top left, click the first icon for Project Create Wizard, give your project a name like dbRevkeep, click Finish, you'll see a new folder with your project in the left pane under the Projects tab. Click your new project folder to open it, you'll see subfolders Bookmarks, ER Diagrams, Scripts and Connections.
3. Select Connections, right click, select Create, select Connection. A dialog box will pop up, Select your database, select SQLServer, click Next.
4. In the SQL Server Connection Settings:
   Host: localhost Port: 5434
   Database/Schema: revkeep
   Authentication: SQL Server Authentication
   User name: SA
   Password: <YourPassword>
   check Save Password locally
   check Show All Schemas
   Driver name: MS SQL Server/SQL Server

    Click Test Connection, you should see a dialog pop up with Connected.

5. Click Finish, you should see a new revkeep connection, under Connections in the Projects tab on the left, select and right click, select Connect.
6. If you see a small green checkmark on the connection name, revkeep, you have successfully connected.
7. Click the revkeep connection to expand, you'll see Databases, Security, Administer. Click Databases to expand, click revkeep, click Schemas, click dbo, click Tables. You'll see all the tables used in the revkeep app starting with appeals_levels through withdrawn_reasons.
8. Double click the table clients. clients will expand into the view area with 3 sub tabs: Properties (which includes all the column names), Data an ER Diagram.
9. Select Data and you'll see all the client rows in the database.
10. If you select a field and double click, such as fax for SPECTRUM AUTISM CENTER, you can type in a new value, try 989-702-2222, hit return, you'll see the new value show up on the far right in the Value tab. In the lower left of the viewing area, you'll see Save, Cancel, Script. Click Save to commit all changed cells to the database. The changed cells will be highlighted in yellow until you click Save, then they will turn blue when saved.
11. To create a new row in a table, right click any existing cell, click edit, you'll see Add row, Duplicate row, Delete current row, Copy from above, Copy from below.
12. You can view the ER Diagram for the selected table, such as clients, by selecting the ER Diagram sub tab at the top of the view window. If you would like to see the ER diagram for all the tables, double click dbo and select the ER Diagram sub tab.

### RevKeep user account setup

For the demo database there are several users created, to try out an admin level user with full access use account: - Email Address: nabgilby@gmail.com - Password: Revkeep22$

For a new, clean database, no default users are provided. To add a new user, the CakePHP CLI can create a new admin user. - [JIRA ticket](https://nancy-benovich-gilby.atlassian.net/browse/RKDEV-6): As discussed during installation, all cake commands below need to have `sh` prepended when running in a dev container.

1. To create a new user, run `bin/cake user_add <email> <password> <firstname> <lastname>`
    - i.e. `bin/cake user_add nabgilby@gmail.com MyPassw0rd Nancy Benovich-Gilby`
    - i.e. dev container: `sh bin/cake user_add nabgilby@gmail.com MyPassw0rd Nancy Benovich-Gilby`
2. To activate the account (enable login), use the following:
    - `bin/cake user_activate <email>`
    - i.e. dev container: `sh bin/cake user_activate <email>`
3. To promote to admin status, run:
    - `bin/cake user_grant_admin <email>`
    - i.e. dev container: `sh bin/cake user_grant_admin <email>`
4. To revoke admin status from a user, run:
    - `bin/cake user_revoke_admin <email>`
    - i.e. dev container: `sh bin/cake user_revoke_admin <email>`

## Testing

Unit testing was not included in the initial prototype of this application and is planned to be retrofitted for a later rewrite or major update.

PHPStan can be used to static analyze the PHP code by running `composer stan`.

### Running XDebug breakpoint example (dev container)

1. Inside any dev container, navigate to PDOSource.php in VSCode
    - Cmd + P > PDOAdapter.php
    - Located: `revkeep/vendor/robmorgan/phinx/src/Phinx/Db/Adapter/PDOAdapter.php` if VSCode can't find it
2. Place a breakpoint on the `new PDO(` line in `createPDOConnection`. (line 84 currently)
3. Start XDebug
    - Menu > Run > select "Start Debugging"
    - OR select the debug tab on the left panel of VS code. There is a drop down menu at the top, make sure `Listen for XDebug` is selected.
4. In a terminal connected to the dev container (i.e. opened in VSCode), run `sh bin/cake migrations migrate`.
5. The breakpoint should hit in `PDOAdapter.php`
    - Note: You can mouse over the variables such as $dsn, $username etc to see their values or in the debug panel on the left, locate an click the dropdown "Variables" / "Locals". You can step, step over, step in, step out, or continue from the Run menu, or the floating tabs by clicking the icons.

### GDB breakpoint + PHP source example (dev container)

1. Open a php-src dev container (must have -src appended in name! i.e. revkeep-php-src-mssql-dev)
2. Navigate to File > Add folder to workspace > php-src
3. Place a breakpoint under `PHP_METHOD(PDO, __construct)` in `pdo_dbh.c`.
4. Navigate to the debug tab in VSCode and select the `GDB bin/cake migrations migrate` option and press the play button to start GDB and run bin/cake migrations migrate
5. Breakpoint should hit and you should be able to inspect values.
6. Note: Unlike XDebug, you need to enter the command GDB will run into the debug configuration. If you want to debug a different command, you will need to open `launch.json`, copy and paste the GDB entry, and alter the `args` field.
