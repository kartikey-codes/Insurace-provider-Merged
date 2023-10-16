# RevKeep

RevKeep is a software-as-a-service (SaaS) for managing pre and post payment audits in the healthcare industry.

This is a Single Page Application (SPA) and a [CakePHP](https://cakephp.org/) PHP API for the back end.
The front end of the application is currently using the [Bootstrap-Vue](https://bootstrap-vue.org/) UI framework.

For [RevKeep Software](https://revkeepsoftware.com) primarily by [Kyle Weishaupt](https://kyleweishaupt.com).

## File Storage

The persistent file storage was initially intended to be used with Azure Blob Storage. The local filesystem can be used during development.

[Flysystem](https://github.com/thephpleague/flysystem) is used for persistent file storage. Any adapter compatible with Flysystem v3.x should be compatible.

The [Azure Storage Explorer](https://azure.microsoft.com/en-us/features/storage-explorer/) can be used to manage containers and blobs stored through the application. The default application config in `/config/.env.default` is set up to use the default emulator ports.

The `STORAGE_DRIVER` environment variable will allow switching to file based storage or future cloud storage options such as Amazon S3. Drivers will need to be created and compatible with Flysystem. The current available options are `azure` and `local`.

## Database

### SQL Server

This application was originally developed to use Microsoft SQL Server as a database. Every effort has been made to leverage the CakePHP ORM to keep the code database-agnostic. MySQL can be used by changing the `DATABASE_DRIVER` environment variable to the CakePHP-compatible class name.

Use `\Cake\Database\Driver\Mysql` for MySQL, or `Cake\Database\Driver\Sqlserver` for SQL Server. PostgreSQL has not been tested.

### Migrations

The [CakePHP Migrations Plugin](https://book.cakephp.org/migrations/3/en/index.html) is used for managing schema changes. See the documentation for any changes to the schema.

The `migrations` CakePHP command is available for running migrations.
To run these migrations, use `/bin/cake migrations migrate` to move up to the latest version of the database.

### Seeding

Only do this if it is a new, clean, database, no need to do if you are restoring from the demo data. There are database seed files included in the `config/Seeds` directory. These can be run using the `cake migrations` shell command from `bin/`.

To seed a new database, run `/bin/cake migrations seed` to run all seeds. If you need a single seed, you can use the `/bin/cake migrations seed --seed UserSeed` for example.

The default database seed will call all other seeds, so use that on a new installation with command `/bin/cake migrations seed --seed=DefaultDatabaseSeed`.

## Deployment

There are a couple commands that need to be run to build the application and fully deploy.

The following commands are used during the build pipeline:

```
php composer.phar install --no-scripts --optimize-autoloader
npm ci
npm run production
```

The following commands are used after deployment:

```
bin\cake migrations migrate
bin\cake schema_cache clear
bin\cake cache clear_all
```

## Support

This application is property of RevKeep Software.

Please contact [RevKeep Software](https://revkeepsoftware.com) for any support or licensing.

Initial development and prototype created by [Kyle Weishaupt](https://kyleweishaupt.com).
Additional development done by Jingxiang Rao, Nancy Benovich Gilby, Joseph Gilby.
