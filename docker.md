# Docker

Create and run the Docker instance:

```bash
docker-compose up -d
```
### Initialize project for development

```bash
docker exec -it fintecture-sdk-php make init
```

### PHPUnit (Unit Tests)

Then you can run the tests:

```bash
docker exec -it fintecture-sdk-php make test
```

To generate the test coverage report, you can run this command:

```bash
docker exec -it fintecture-sdk-php make test-coverage
```

### PHPStan (Static Analysis)

There are 9 levels (0-8). Level is set in `phpstan.neon`.
```bash
docker exec -it fintecture-sdk-php make analyse
```

### PHP CS Fixer (Coding Standards)

```bash
docker exec -it fintecture-sdk-php make check
```

```bash
docker exec -it fintecture-sdk-php make format
```
