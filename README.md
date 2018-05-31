# CIGEN

A super useful tool that helps generating scaffolding and migrations for Codeigniter projects

**Usage :**

1 - Download the folder cigen and put it in the root folder of your app.

2 - Open the terminal and cd to your **your_root_app_folder/cigen**

3 - **If it's a new brand project**, `run php ci.php g basics` this will generate a base_helper.php file in your application/helpers folder and another file My_Security.php file inside your application/core folder

4 - To generate a scaffold (controller+model+views+migration) use something like this:

`php ci.php g scaffold user name:string email:string`

then migrate the database :

`php ci.php db:migrate`


## Other options :

- To see help run `php ci.php g --help`


### Controllers

To create a controller with CRUD methods :

```
php ci.php g controller students
```

To generate only some actions :

```
php ci.php g controller students index show custom_action
```


### Models

To generate a model :

```
php ci.php g model Male
```


### Database 

- To create database run : `php ci.php db:create`

- To drop database run : `php ci.php db:drop` (**Note**: if a problem says you can't drop database because connecion open, go to config/databse.php and temporary give the database name an empty string '' then re-run the command to drop the table)

- `php ci.php db:reset` command will run db:drop + db:create + db:migrate

- `php ci.php db:migrate` will run all the migrations files where the timestamp > the 'version' value in table 'migrations' (table migrations is created by codeigniter when you run migrate command)

- `php ci.php db:rollback` will go one step back (+ will change the file migration we rolled back to "rolledback_timestamp_filename"

- `php ci.php db:migrate:down VERSION=YOUR_FILE_TIMESTAMP_VALUE` will run **DOWN** method of the migration file you specify with VERSION=...

- `php ci.php db:migrate:up VERSION=YOUR_FILE_TIMESTAMP_VALUE` will run **UP** method of the migration (NOTE: mostly this is used to run the migration we undo with db:migrate:down, so make sure you delete the 'rolledback' from your file name)

