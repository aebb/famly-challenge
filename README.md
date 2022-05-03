### Technical Assessment

## Part 1: SQL
- challenge1.sql

## Part 2: Language of choice

#### Tech Stack:
- Docker
- PHP 8.0
- Symfony 6.0
- MySQL

#### Install and Run:

```docker-compose up -d```

PHP Container access: ```docker exec -it task-famly /bin/bash```

Database access: ```docker exec -it database-famly mysql -u root -proot```

##### Install dependencies

```composer update -vvv```

##### Create the database
```php bin/console doctrine:database:create```

##### Run the migrations
```php bin/console doctrine:migration:migrate```

##### Create staff and children
```php bin/console doctrine:fixtures:load```

ROLE_STAFF TOKENS: staff1token

#### Tests:

##### To run the tests

```vendor/bin/phpunit -c phpunit.xml ./tests```

#### Quality Tools:

##### Run code beautifier

```vendor/bin/phpcbf```

##### Run code sniffer

```vendor/bin/phpcs```

##### Run mess detector

```vendor/bin/phpmd ./src text ./phpmd.xml```

composer.json also contains shortcuts for these commands

        "test-unit": "vendor/bin/phpunit -c phpunit.xml ./tests/Unit",
        "test-integration":"vendor/bin/phpunit -c phpunit.xml ./tests/Integration",
        "run-tests": [
            "@test-unit",
            "@test-integration"
        ],
        "phpcs": "vendor/bin/phpcs",
        "phpcbf": "vendor/bin/phpcbf",
        "phpmd": "vendor/bin/phpmd ./src text ./phpmd.xml"

#### Solution:

##### PHP:
- PSR-12 Standard
- Endpoints require token authentication from a staff member (see config/packages/security.yaml)
- POST /attendance (Check a child in)
```
curl --location --request POST 'http://localhost:8080/attendance' \
--header 'X-AUTH-TOKEN: staff1token' \
--header 'Content-Type: application/json' \
--data-raw '{
    "childId": "1"
}' | python -mjson.tool
```

- PATCH /attendance/{attendanceId} (Check a child out)
```
curl --location --request PATCH 'http://localhost:8080/attendance/123' \
--header 'X-AUTH-TOKEN: staff1token' | python -mjson.tool
```

- GET /child (List the names of all children that are currently checked in)

Optional query parameters: start, count, search(by child name)
```
curl --location --request GET 'http://localhost:8080/child?start=1&count=5&search=Noah' \
--header 'X-AUTH-TOKEN: staff1token' | python -mjson.tool
```

- Analytics Command (Getting a list of children that were checked in for at least 2h today, optional parameter duration in minutes)
```
docker exec -it task-famly php bin/console app:attendance:duration 120 
```

##### Tests:
- PHPUnit for tests (code coverage ~100%):
1) Integration: In-memory SQLLite for persistence
2) Unit: PHPUnit mock objects for dependencies:


### Remarks and future work:
1. Add nursery relations
2. Check out all kids by the end of the day (cron job or when all staff leaves)
3. More authentication options (RFID, Mobile App, pin codes, etc)
4. Consider computed column for analytics
5. Consider caching check ins with a long TTL
