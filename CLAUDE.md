## Task Workflow
  - Follow `docs/WORKFLOW.md` - Issue creation, planning, implementation
  - ALWAYS update `docs/progress/issue-XX.md` during work

## Tech Stack
  - PHP 8.4 + Laravel 12
  - Testing: Pest 4 (feature + browser tests)
  - Formatting: Laravel Pint
  - Frontend: Vite + Tailwind CSS 4
  - Database: SQLite (default)

See `docs/LARAVEL_BOOST.md` for complete package versions.

## Laravel Boost Guidelines
  - See `docs/LARAVEL_BOOST.md` - Comprehensive Laravel 12 + Pest 4 guidelines

## Quick Reference
  - Dev: `composer run dev` (server + queue + logs + vite)
  - Test: `composer test`
  - Format: `vendor/bin/pint --dirty`
  - Browser tests: `tests/Browser/` with `assertNoJavascriptErrors()`
