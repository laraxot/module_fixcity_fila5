<?php

declare(strict_types=1);

// Fixcity translations — LangServiceProvider SSoT (never ->label() in Filament PHP).
// claude-audit static: split from ticket.php for maintainability (<500 LOC).
// Canon: Modules/Fixcity/docs/wiki/concepts/claude-audit-static.md
// File: lang/en/ticket_loader.php
return merge_translation_files(__DIR__.'/ticket_breadcrumb.php', __DIR__.'/ticket_inefficiency_types.php', __DIR__.'/ticket_page.php', __DIR__.'/ticket_wizard_a11y.php', __DIR__.'/ticket_heading.php', __DIR__.'/ticket_filters.php', __DIR__.'/ticket_results.php', __DIR__.'/ticket_filter.php', __DIR__.'/ticket_tabs.php', __DIR__.'/ticket_map.php', __DIR__.'/ticket_card.php', __DIR__.'/ticket_load_more.php', __DIR__.'/ticket_contacts.php', __DIR__.'/ticket_fields.php', __DIR__.'/ticket_sections.php', __DIR__.'/ticket_actions.php', __DIR__.'/ticket_steps.php', __DIR__.'/ticket_gdpr_notice.php', __DIR__.'/ticket_privacy.php', __DIR__.'/ticket_geolocation.php', __DIR__.'/ticket_contact.php', __DIR__.'/ticket_warning.php', __DIR__.'/ticket_create_options.php', __DIR__.'/ticket_navigation.php'
);
