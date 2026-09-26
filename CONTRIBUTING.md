# Contributing

Thanks for your interest in improving **Audit Trail for Filament**! Contributions from the community are what make packages like this useful.

## Getting started

1. Fork the repository and clone your fork locally.
2. Install dependencies:

   ```bash
   composer install
   ```

3. Create a branch for your change:

   ```bash
   git checkout -b my-feature
   ```

## Making changes

- Follow the existing code style. The project uses [Laravel Pint](https://laravel.com/docs/pint); run it before committing:

  ```bash
  composer lint          # auto-fix style
  composer test:lint     # check style without changing files
  ```

- Add or update tests for anything you change. The suite uses [Pest](https://pestphp.com) with Orchestra Testbench:

  ```bash
  composer test          # vendor/bin/pest
  ```

- Keep the `audit_logs` schema append-only. If you add a column, ship it as a new migration in `database/migrations/`.

## Testing against a real panel

The fastest way to see your change end to end is to install the package into a local Filament v5 demo app using a path repository:

```json
"repositories": [
    { "type": "path", "url": "../path/to/filament-audit-trail", "options": { "symlink": true } }
]
```

Then `composer require zhenjun/filament-audit-trail:*`, register the plugin on your panel, and add the `Auditable` trait to a model.

## Submitting a pull request

- Make sure `composer test` and `composer test:lint` both pass.
- Fill in the pull request template.
- For larger changes, please [open an issue](../../issues) first so we can agree on the approach before you invest a lot of time.

## Reporting bugs & asking questions

- Bugs: use the [issue template](../../issues/new?template=bug_report.yml) and include the version matrix (package / Filament / Laravel / PHP).
- Questions: use [GitHub Discussions](../../discussions) rather than issues.
- Security issues: please report privately via a [security advisory](../../security/advisories/new), not in a public issue.

This project has a [code of conduct](CODE_OF_CONDUCT.md); be kind and constructive.

By contributing, you agree that your contributions will be licensed under the project's [MIT license](LICENSE.md).
