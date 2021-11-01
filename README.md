multi-tenancy-bundle
====================

Multi-tenancy, is a package for symfony and doctrine to manage tenants in a simple way. The package has 2 main entities:

* Tenant
* Hostname

Basically a **tenant** is the way to reuse your default code and a **hostname** is the FQDN (Fully Qualified Domain Name) for example: tenant.example.com, the bundle set the tenancy connection based on this FQDN. 


Installation
------------

Via composer

```console
    composer require mapeveri/multi-tenancy-bundle
```


Configuration
-------------

1. Configuration .env file

Mysql

```txt
DATABASE_URL="mysql://user:password8@127.0.0.1:3306/databaseName?serverVersion=5.7&charset=utf8"
DATABASE_TENANT_URL="mysql://user:password8@127.0.0.1:3306/?serverVersion=5.7&charset=utf8"
```

In mysql DATABASE_TENANT_URL must be empty the database name value.

PostgreSql

```txt
DATABASE_URL="postgresql://user:password@localhost:5432/databaseName?charset=utf8"
DATABASE_TENANT_URL=${DATABASE_URL}
```

In postgresql DATABASE_TENANT_URL must be the same that DATABASE_URL. This is because PostgreSql has schemas to manage the tenants in the same database.

2. Configuration to dotrine.yaml

Mysql:

```yaml
doctrine:
    dbal:
        default_connection: default
        connections:
            default:
                url: '%env(resolve:DATABASE_URL)%'
                driver: 'pdo_mysql'
                server_version: '5.7'
                charset: utf8mb4

            tenant:
                driver: 'pdo_mysql'
                server_version: '5.7'
                charset: utf8mb4
                url: '%env(resolve:DATABASE_TENANT_URL)%'
                wrapper_class: MultiTenancyBundle\Doctrine\DBAL\TenantConnectionWrapper

    orm:
        default_entity_manager: default
        entity_managers:
            default:
                connection: default
                mappings:
                    Main:
                        is_bundle: false
                        type: annotation
                        dir: '%kernel.project_dir%/src/Entity/Main'
                        prefix: 'App\Entity\Main'
                        alias: Main
                    MultiTenancyBundle:
                        is_bundle: true
                        type: annotation
                        dir: 'Entity'
                        prefix: 'MultiTenancyBundle\Entity'
                        alias: MultiTenant
            tenant:
                connection: tenant
                mappings:
                    Tenant:
                        is_bundle: false
                        type: annotation
                        dir: '%kernel.project_dir%/src/Entity/Tenant'
                        prefix: 'App\Entity\Tenant'
                        alias: Tenant
```

PostgreSql:

```yaml
doctrine:
    dbal:
        default_connection: default
        connections:
            default:
                url: '%env(resolve:DATABASE_URL)%'
                driver: 'pdo_psql'
                server_version: '12.8'
                charset: utf8mb4

            tenant:
                driver: 'pdo_psql'
                server_version: '12.8'
                charset: utf8mb4
                url: '%env(resolve:DATABASE_TENANT_URL)%'
                wrapper_class: MultiTenancyBundle\Doctrine\DBAL\TenantConnectionWrapper

    orm:
        default_entity_manager: default
        entity_managers:
            default:
                connection: default
                mappings:
                    Main:
                        is_bundle: false
                        type: annotation
                        dir: '%kernel.project_dir%/src/Entity/Main'
                        prefix: 'App\Entity\Main'
                        alias: Main
                    MultiTenancyBundle:
                        is_bundle: true
                        type: annotation
                        dir: 'Entity'
                        prefix: 'MultiTenancyBundle\Entity'
                        alias: MultiTenant
            tenant:
                connection: tenant
                mappings:
                    Tenant:
                        is_bundle: false
                        type: annotation
                        dir: '%kernel.project_dir%/src/Entity/Tenant'
                        prefix: 'App\Entity\Tenant'
                        alias: Tenant
```

3. Configuration to doctrine_migrations.yaml

```yaml
doctrine_migrations:
  migrations_paths:
    'DoctrineMigrations': 'migrations/Main'
    'DoctrineMigrationsTenant': 'migrations/Tenant'
```

**It's important to keep** DoctrineMigrations and DoctrineMigrationsTenant namespaces.


4. Add the bundle to bundles.php

```php
return [
    ...
    MultiTenancyBundle\MultiTenancyBundle::class => ['all' => true],
    ...
];
```

Commands for main database
--------------------------

In this case we can use doctrine commands:

```console
    php bin/console doctrine:migrations:status
```

```console
    php bin/console doctrine:migrations:diff
```

```console
    php bin/console doctrine:migrations:migrate
```


Commands for tenants
--------------------

Genarate migrations

```console
    php bin/console tenancy:diff tenant
```

Status migrations

```console
    php bin/console tenancy:status tenant --tenant=tenant1
```

Migrate single tenant

```console
    php bin/console tenancy:migrate tenant --tenant=tenant1
```

Migrate all tenants

```console
    php bin/console tenancy:migrate tenant
```


In all cases the first parameter is the entity manager name and the option --tenant is the tenant name.


Supported databases
-------------------

Right now it only works with MySql and PostgreSql.