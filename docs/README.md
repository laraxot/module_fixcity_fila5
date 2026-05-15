# Fixcity Module Documentation

## Overview
The Fixcity module is responsible for managing ticket reports and segnalazioni in the Fixcity application. It provides the core functionality for users to create, manage, and track municipal service requests.

## Architecture

### Core Components

#### 1. Ticket Management
- **Ticket Model**: Main entity representing service requests
- **Ticket Resource**: Filament resource for CRUD operations
- **TicketForm**: Schema definition for ticket creation forms

#### 2. Wizard System
- **CreateTicketWizardWidget**: Multi-step wizard for ticket creation
- **Step Management**: Privacy → Data → Summary workflow
- **Form Validation**: Step-specific validation rules

#### 3. Localization
- **Italian Language Support**: Complete Italian translations
- **Dynamic Labels**: Automatic label generation via LangServiceProvider
- **Translation Keys**: Structured translation files in `lang/it/`

### File Structure

```
Modules/Fixcity/
├── app/
│   ├── Filament/
│   │   ├── Resources/
│   │   │   └── TicketResource/
│   │   │       ├── Schemas/
│   │   │       │   └── TicketForm.php
│   │   │       └── TicketResource.php
│   │   └── Widgets/
│   │       └── CreateTicketWizardWidget.php
│   ├── Models/
│   │   └── Ticket.php
│   └── Actions/
│       └── NormalizeTicketLocationDataAction.php
├── resources/
│   ├── views/
│   │   └── filament/
│   │       └── widgets/
│   │           └── create-ticket-wizard.blade.php
│   └── lang/
│       └── it/
│           └── segnalazione.php
└── routes/
    └── web.php
```

### Key Features

#### 1. Multi-Step Wizard
- **Step 1 - Privacy**: Acceptance of privacy terms
- **Step 2 - Data**: Ticket information collection
- **Step 3 - Summary**: Review and submission

#### 2. Form Schema System
- **TicketForm::getSteps()**: Dynamic step generation
- **Schema Components**: Reusable form components
- **Validation**: Step-specific validation rules

#### 3. Location Management
- **Geolocation Support**: Automatic location detection
- **Address Normalization**: Standardized address formatting
- **Map Integration**: Visual location selection

#### 4. User Management
- **Anonymous Support**: Ticket creation without registration
- **Authenticated Users**: Automatic user association
- **Permission Handling**: Role-based access control

### Configuration

#### Environment Variables
```env
# Ticket confirmation page slug
FIXCITY_WIZARD_CONFIRMATION_SLUG=segnalazione-04-conferma

# Google Maps API Key for location services
GOOGLE_MAPS_API_KEY=your_api_key_here
```

#### Routes
- `tests.view`: Display test pages including segnalazione creation
- `ticket.create`: Direct ticket creation endpoint

### Development Guidelines

#### 1. Form Development
- Use `TicketForm` for consistency
- Follow the step-based approach
- Implement proper validation
- Use Italian translations

#### 2. Widget Development
- Extend `FilamentWidget` for new widgets
- Use the `getCurrentStepIndex()` method for step detection
- Implement proper form state management
- Follow the Design Comuni CSS framework

#### 3. Translation Management
- Add all translations to `lang/it/segnalazione.php`
- Use the LangServiceProvider for automatic labels
- Follow the existing translation key structure
- Test translations thoroughly

#### 4. Testing
- Test each wizard step individually
- Validate form submissions
- Test both authenticated and anonymous users
- Verify location functionality

### Integration Patterns

#### 1. With Xot Base Widgets
- Use `XotBaseWizardWidget` as base when available
- Implement required abstract methods
- Follow the widget inheritance hierarchy

#### 2. With Filament
- Use Filament form components
- Follow Filament best practices
- Implement proper validation
- Use Filament actions for form submission

#### 3. With Design Comuni
- Use Bootstrap-Italia classes
- Follow Design Comuni layout patterns
- Implement proper accessibility
- Use the pub_theme components

### Troubleshooting

#### Common Issues
1. **Form Not Showing**: Check widget inheritance and view paths
2. **Translation Errors**: Verify translation file syntax and keys
3. **Step Navigation**: Ensure proper step management logic
4. **Location Issues**: Check Google Maps API configuration

#### Debugging Tips
- Use Laravel DebugBar for form debugging
- Check Livewire components for form state
- Verify routes and permissions
- Test in different environments

### Performance Considerations

#### 1. Database Optimization
- Use appropriate indexes on ticket tables
- Implement proper relationships
- Use Eloquent lazy loading where appropriate

#### 2. Frontend Performance
- Minimize CSS/JS bundle size
- Use proper caching strategies
- Implement lazy loading for heavy components

#### 3. API Optimization
- Use proper HTTP caching
- Implement rate limiting
- Use efficient data serialization

### Future Enhancements

#### Planned Features
1. **User Registration**: Integrated user accounts
2. **Ticket Status Tracking**: Real-time status updates
3. **Notification System**: Email and SMS notifications
4. **Advanced Search**: Complex filtering and search capabilities

#### Technical Improvements
1. **API Integration**: External service integrations
2. **Mobile Support**: Responsive design improvements
3. **Performance Monitoring**: Analytics and monitoring
4. **Security Enhancements**: Additional security layers

---

*Last Updated: May 2026*  
*Version: 1.0.0*