### Standard Flow

#### !! IMPORTANT !!
When working on features or fixes, ALWAYS create or update the GitHub issue BEFORE starting any code implementation. Never jump to coding without confirming the issue exists and the implementation plan is documented.
While in progress, ALWAYS update `docs/progress/issue-XX.md` as the phases and steps progress.

#### Steps
1. **Create a GitHub Issue** - This can be skipped if the issue number is specified.
2. **Create a progress file** - Create an `issue-XX.md` file in `docs/progress`. `XX` is the issue nubmer (5 digits with zero padding. e.g. `issue-00005.md` for issue #5).
3. **Create a plan** - Review the requirements and design the implementation approach. Use plan mode. Consult the client for any undecided specifications.
4. **Confirm the plan** - Confirm with the client if the plan can be proceeed. If the plan is accepted, exit plan mode.
5. **Document the plan** - Document the plan in the issue as a comment.
6. **Create a Git Branch** - Create a feature branch for the issue. ALL feature branches should be derived from the LATEST main branch.
7. **Implement** - Write code and tests
8. **Testing** - Ensure all unit tests pass. When implementing some feature, make sure UI tests are performed with Playwright MCP, too.

### Completion Criteria

- Tests are written and all pass

### Choosing the Right Flow

- **Standard flow**: New features, changes requiring design decisions, multi-file changes
- **Lightweight flow**: Typo fixes, simple bug fixes, small single-file changes
  - Lightweight flow may skip Issue creation

### Branch Naming

Follow [Conventional Branch](https://conventional-branch.github.io/).

Common patterns include:
- `feature/description` or `feat/description` - Feature branches, description may start with issue number like `issue-123-`
- `bugfix/description` or `fix/description` - Bug fix branches, description may start with issue number like `issue-123-`
- `chore/description` - Maintenance branches
