# Contributing to GEMRISER

## Setup

```bash
git clone https://github.com/gemriser/gemriser.git
cd gemriser
composer install
cd packages/skeleton && npm install && cd ../..
```

## Development

- Run tests: `composer test`
- Run linter: `composer pint:test`
- Run static analysis: `composer phpstan`
- Run all: `composer ci`

## Commit Convention

We use [Conventional Commits](https://www.conventionalcommits.org/):

```
feat(framework): add new feature
fix(skeleton): resolve bug
chore: update dependencies
docs: improve README
```

## Pull Requests

1. Branch from `master`
2. Keep changes focused — one feature per PR
3. Ensure CI is green (lint + phpstan + tests)
4. Request review from a maintainer

## Code of Conduct

Please note that this project has a [Code of Conduct](CODE_OF_CONDUCT.md). By participating, you agree to abide by its terms.
