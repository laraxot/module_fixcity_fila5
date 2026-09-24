# Composer PHPStan Version Conflict Fix

## Problem
Running `composer update` fails with version conflict between PHPStan packages:

```
Your requirements could not be resolved to an installable set of packages.

  Problem 1
    - Root composer.json requires phpstan/phpstan-strict-rules ^1.0
    - phpstan/phpstan-strict-rules 1.x requires phpstan/phpstan ^1.x
    - But root composer.json requires phpstan/phpstan ^2.1
```

## Root Cause
The root `composer.json` has:
```json
"require": {
    "phpstan/phpstan": "^2.1"
},
"require-dev": {
    "phpstan/phpstan-strict-rules": "^1.0"
}
```

PHPStan Strict Rules v1.x requires PHPStan ^1.x, but the project needs ^2.1.

## Solution
Update `phpstan/phpstan-strict-rules` to version ^2.0 to be compatible with PHPStan ^2.1.

### Step 1: Update composer.json
```json
"require-dev": {
    "phpstan/phpstan-strict-rules": "^2.0"
}
```

### Step 2: Run composer update
```bash
composer update
```

### Step 3: Verify PHPStan version
```bash
phpstan --version
# Should show: PHPStan - PHP Static Analysis Tool 2.1.17
```

## Prevention
- Always check PHPStan compatibility when adding strict rules
- Use `composer why-not` to check version conflicts:
  ```bash
  composer why-not phpstan/phpstan phpstan/phpstan-strict-rules
  ```

## Files Modified
- `laravel/Modules/User/composer.json` - Updated phpstan-strict-rules to ^2.0

## Additional Notes
- PHPStan Strict Rules v2.0+ supports PHPStan ^2.0
- This fix resolves the dependency conflict while maintaining code quality standards