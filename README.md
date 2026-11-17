# Council Payments

This directory contains the application code for the D3R developer practical test. The application will display payments made by Chichester District Council to suppliers in October 2010, using the supplied database dump.

The listing should follow the supplied design and support searching and pagination. PHP classes in this directory use the `Local\CouncilPayments` namespace and are loaded through Composer's PSR-4 autoloader.

Place this repository in the project's `src/Local/CouncilPayments` directory.

## Entry point

Run the listing controller from the project's `index.php`. Inject the payments model and collection into the controller:

```php
(new \Local\CouncilPayments\Controller\Listing(
    new \Local\CouncilPayments\Model\Payments(),
    new \Local\CouncilPayments\Model\Collection\PaymentCollection()
))->render($_GET);
```

## Tests

PHPUnit is a development dependency. From the project root (`devpracticaltest-2.0`), install the Composer dependencies, including development dependencies:

```sh
composer install
```

If the project's `composer.json` does not yet include PHPUnit, add it first:

```sh
composer require --dev phpunit/phpunit:^13.4
```

Then run the tests from the project root:

```sh
vendor/bin/phpunit --bootstrap vendor/autoload.php src/Local/CouncilPayments/tests
```

The database integration tests are skipped by default. To run them against the configured database and supplied payment data, use:

```sh
D3R_INTEGRATION=1 vendor/bin/phpunit --bootstrap vendor/autoload.php src/Local/CouncilPayments/tests/Integration
```

## Notes

I considered using Next.js or a React application, but either would be too far removed from the project requirements.

I also considered using Tailwind CSS with PHP. I have experience with Tailwind and think it is a good choice in general, but setting up and explaining a build process is not necessarily how I want to spend time on this four-hour project. The specification allows preprocessors such as Sass and Less, but for this task I decided not to introduce Tailwind.

I did not want to build a complete CMS for this project; the aim is to implement the functionality requested in the specification.

The unit tests use test doubles and do not require a database. The optional integration tests run read-only queries against the configured database, so a separate test database is not required. They cover search, pagination, and an injection-like input; they do not prove that every possible input is safe.
