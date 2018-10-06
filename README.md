# Import bundle

A bundle to import data from CSV or Excel files, letting the user map the columns of its file to the required fields for your process.

## Configuration

Setup a temporary directory in which the uploaded files will be stored temporarily.

Setup as many imports as you want by configuring:

- the route prefix (several routes are generated for each import process)
- the mapping (the required columns for your process)
- The process service (which must implement `Vinorcola\ImportBundle\Model\ImportConsumerInterface`).

```yaml
# config/packages/vinorcola_import.yaml

vinorcola_import:
    temporaryDirectory: "%kernel.project_dir%/document-storage/tmp"
    imports:
        company_import:
            route_prefix:
                name: import.company.
                url: /company/import
            mapping: [ nationalIdentifier, companyName, address, city ]
            service: App\Model\CompanyImportHandler
        contact_import:
            route_prefix:
                name: import.contact.
                url: /contact/import
            mapping: [ firstName, lastName, emailAddress, phoneNumber ]
            service: App\Model\ContactImportHanlder
```

```yaml
# config/routes/vinorcola_import.yaml

import_routes:
    resource: '@VinorcolaImportBundle/Controller/'
    type: vinorcola_import
```

Then, the `consume` method of your process service will be called for line of data in the user's file, providing an array with the mapped columns and the line index:

```php
public function consume(array $values, int $lineIndex): void;
```

For example, in the `App\Model\CompanyImportHandler`, the `consume` method will be called with a `$values` array containing the following keys:

- `nationalIdentifier`
- `companyName`
- `address`
- `city`
