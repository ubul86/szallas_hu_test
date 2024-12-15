# Pull Request Template

## Purpose
Briefly describe the purpose of this Pull Request. What issue does it solve, and why is it important?  
Example:  
> "This PR fixes the X issue that caused Y problem for users."

---

## Testing
Describe how you tested the changes. Include screenshots or examples if necessary.
- **Testing steps**:
  1. Start the application in [local/staging environment].
  2. Test the [modified functionality] with the following data: ...
  3. Verify that the output is correct: ...

- **Tools and methods**:
  - [ ] PHPUnit tests (`vendor/bin/phpunit`)
  - [ ] Laravel Dusk E2E tests
  - [ ] Manual testing in the browser

---

## Issue and Fix
- **Issue description**:  
  Describe what the problem was and how it could be reproduced.  
  Example:  
  > "The X feature returned a 500 error when using the Y parameter."

- **Fix description**:  
  Summarize how the problem was fixed.  
  Example:  
  > "The issue was caused by missing Z validation, so I added a new validation rule."

---

## Documentation
- [ ] Updated the `README.md` if necessary.
- [ ] Updated the API documentation (if applicable).

---

## Checklist
The following checklist helps ensure all necessary steps are completed.

- [ ] Code complies with coding standards (checked with PHP CodeSniffer or Laravel Pint).
- [ ] All tests passed successfully.
- [ ] Added new tests if required.
- [ ] Verified the code with PHPStan.
- [ ] Changes are documented appropriately.

---

## Screenshots (if applicable)
Please upload screenshots to demonstrate the result of the changes (e.g., UI modifications, bug fixes).  
Example:  
![Issue Before](https://via.placeholder.com/600x200)  
![Fixed](https://via.placeholder.com/600x200)

---

## Reviewer Notes
Please review the following areas:
- [ ] Logic clarity and efficiency
- [ ] Code reusability
- [ ] Handling of potential edge cases

---

## References
- **Related Jira ticket**: [PROJ-123](https://jira.example.com/browse/PROJ-123)
- **Related documentation or specification**: [Documentation link](https://example.com/docs)
