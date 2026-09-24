# MCP Servers - Fixcity Module

**Module**: Fixcity (Ticket System)  
**Last Updated**: 2026-04-09

## Master Documentation

**See [Project MCP Servers](../../../docs/MCP_SERVERS.md)** for complete server list, configuration, and usage examples.

This document provides Fixcity module-specific MCP usage guidelines only.

## Fixcity-Specific MCP Usage

### sqlite
- **Use**: Query ticket data, verify statuses, debug reporting workflow
- **Example**:
  ```sql
  SELECT * FROM tickets WHERE status = 'open' ORDER BY created_at DESC LIMIT 10;
  SELECT COUNT(*) FROM tickets WHERE issue_type = 'public_damage';
  SELECT t.*, u.email FROM tickets t LEFT JOIN users u ON t.email = u.email;
  ```

### filesystem
- **Scope**: `Modules/Fixcity/` directory
- **Use**: 
  - Explore module structure (models, Filament widgets, views)
  - Find translation files
  - Verify widget patterns
- **Key Paths**:
  - `app/Filament/Widgets/CreateTicketWizardWidget.php` - Ticket creation wizard
  - `app/Models/Ticket.php` - Core ticket model
  - `resources/views/filament/widgets/ticket-create-wizard.blade.php` - Wizard view
  - `lang/it/segnalazione.php` - Italian translations

### memory
- **Use**: Store Fixcity-specific decisions, patterns, conventions
- **Examples**:
  - "Ticket model extends XotBaseModel (Laraxot pattern)"
  - "CreateTicketWizardWidget uses Filament Wizard schema and extends XotBaseWizardWidget"
  - "Wizard step override uses shared Laraxot policy via XotBaseWizardWidget"
  - "Widget naming: Use 'Ticket' NOT 'Segnalazione' in class names"

### context7
- **Use**: Look up Filament, Livewire, Laravel documentation
- **Example Queries**:
  - "Filament widget best practices Laravel 12"
  - "Livewire file upload handling patterns"
  - "Laravel model relationships best practices"
  - "Filament actions vs services pattern"

### sequential-thinking
- **Use**: Ticket workflow design, form validation strategies
- **Example**: Designing multi-step wizard, evaluating validation approaches

### supermemory
- **Container Tag**: `fixcity`
- **Use**: Store ticket system evolution, workflow decisions, user feedback
- **Commands**:
  - `supermemory add --tag fixcity --content "Ticket creation: 3-step wizard (privacy, data, summary)"`
  - `supermemory search "file upload widget" --tag fixcity`
  - `supermemory profile --tag fixcity --query "ticket system preferences"`

### memory-bank
- **Use**: Store Fixcity development sessions, feature implementations
- **Example**: "Session 2026-04-09: Added file upload to CreateTicketWizardWidget"

### git
- **Scope**: Fixcity module changes
- **Use**: Track feature evolution, find when patterns were introduced
- **Example**: 
  ```bash
  git blame Modules/Fixcity/app/Filament/Widgets/CreateTicketWizardWidget.php
  git log --oneline Modules/Fixcity/ | head -20
  ```

### github
- **Use**: Create issues, manage features, track bugs
- **Example**: Create issue for missing translation, track feature requests

## Fixcity Development Workflow with MCP

### Feature Development
1. Use `context7` to look up best practices
2. Use `filesystem` to explore existing patterns
3. Use `sequential-thinking` to design approach
4. Use `memory` to store design decisions
5. Use `sqlite` to verify data model

### Bug Investigation
1. Use `sqlite` to query problematic data
2. Use `filesystem` to read relevant code
3. Use `git` to find when issue was introduced
4. Use `memory` to store root cause analysis

### Translation Updates
1. Use `filesystem` to find translation files
2. Use `sqlite` to verify translation keys used in views
3. Use `memory` to store translation conventions (5-level namespace)

### Widget Testing
1. Use `filesystem` to read widget view
2. Use `sqlite` to create test data
3. Use `memory` to store testing patterns
4. Use `supermemory` to store test results

## Current Canonical Note

`CreateTicketWizardWidget` is no longer a manual `$currentStep` widget. The canonical architecture is:

- `CreateTicketWizardWidget extends XotBaseWizardWidget`
- Filament `Wizard` / `Step` schema
- thin Blade wrapper around `{{ $this->form }}`

## Cross-References

- **Master MCP Docs**: [Project MCP Servers](../../../docs/MCP_SERVERS.md)
- **Xot Module MCP**: [Xot MCP Guide](../../Xot/docs/MCP_SERVERS.md)
- **Theme MCP**: [Sixteen Theme MCP](../../Themes/Sixteen/docs/MCP_SERVERS.md)
- **SuperMemory Quickstart**: [SuperMemory Guide](../../../docs/SUPERMEMORY_QUICKSTART.md)
- **Fixcity Documentation**: [Fixcity Docs Index](README.md)

---

*This document follows DRY+KISS principles. Server list and configuration are in the master doc.*
