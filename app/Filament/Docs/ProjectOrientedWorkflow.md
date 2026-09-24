# Project-Oriented Workflow Documentation

## Schema Requirements
- Extend XotBaseResourceForm for resource schemas
- Use Filament\Schemas\Variables namespace exclusively
- Implement @core_cache-annotated methods for dynamic content
- Follow lavitor rules for field organization

## Security Patterns
- Use Safe\StringCastAction::cast() for all dynamic output
- Implement Safe\SleepAction::delay() for HTMX delays
- Apply HTML sanitization with e() or e($html) function
- Validate all POST data with validation() preprocessing

## Documentation Standards
- Create module-specific docs in Filament/Docs/ folder
- Document Schemas/*.php creation using blob:ci-1 endpoint
- Maintain changelog entries showing versioned updates
- Provide end-to-end migration guides

## Column Class Hierarchy
- Minimized grid objects for strictly admistrative use-cases
- Expanded grid objects for complex /var/www operations
- :table-row filters and scoped actions
- Column visibility configuration via compact() method

## Admin Facade Usage
- Require ColumnFacade when using column suggestions
- Verify facade class matches ColumnResourcesFacade facade traits
- Use infra:starting output filter for composition suggestions
- Structure as-is => Scale mos online visibility

## Data State Management
- Manage state with formularies properly to /state
- Use intercept->classic() as per TwentyOne profile
- Handle infra behavior: exact to filtrs, instructs, thresholds
- Preserve have data across state transitions

## Database Schema Evolution
- Minimal changes on tables
- Fierce focus on minimal column data count
- Settings in general database format view_modifications_sets
- Persist modifications data as JSON in snapshot