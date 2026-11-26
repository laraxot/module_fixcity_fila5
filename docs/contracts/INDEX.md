# Fixcity behavioral contract index

This is the machine-readable inventory of behavioral contracts owned by the **Fixcity** module.

A contract describes an existing public operation or boundary. It names the owner, input, output, stable invariants, side effects and their order, errors, and non-goals. Every invariant gets a stable ID and a Pest group when a test exists; missing coverage is recorded as debt instead of being implied away.

## Contracts
- [assign-ticket-action](assign-ticket-action.md)
- [build-public-ticket-detail-action](build-public-ticket-detail-action.md)
- [build-ticket-public-image-urls-action](build-ticket-public-image-urls-action.md)
- [can-view-ticket-media-action](can-view-ticket-media-action.md)
- [change-status](change-status.md)
- [change-ticket-priority-action](change-ticket-priority-action.md)
- [create-ticket-action](create-ticket-action.md)
- [delete-ticket-action](delete-ticket-action.md)
- [geocode-ticket-address-action](geocode-ticket-address-action.md)
- [get-public-ticket-by-code-action](get-public-ticket-by-code-action.md)
- [mark-personal-area-notifications-as-read-action](mark-personal-area-notifications-as-read-action.md)
- [notify-ticket-assignee-of-assignment-action](notify-ticket-assignee-of-assignment-action.md)
- [notify-ticket-subscribers-of-status-change-action](notify-ticket-subscribers-of-status-change-action.md)
- [policy-registration](policy-registration.md)
- [purge-deleted-accounts-action](purge-deleted-accounts-action.md)
- [record-ticket-communication-action](record-ticket-communication-action.md)
- [redirect-ticket-track-code-middleware](redirect-ticket-track-code-middleware.md)
- [send-accessibility-feedback-action](send-accessibility-feedback-action.md)
- [send-ticket-to-ente-action](send-ticket-to-ente-action.md)
- [set-ticket-subscription-action](set-ticket-subscription-action.md)
- [sign-ticket-media-url-action](sign-ticket-media-url-action.md)
- [submit-citizen-ticket-rating-action](submit-citizen-ticket-rating-action.md)
- [submit-ticket-once-action](submit-ticket-once-action.md)
- [ticket-policy](ticket-policy.md)
- [transition-ticket-status-action](transition-ticket-status-action.md)

## Authoring

Use [Xot contract authoring standard](../../../Xot/docs/contracts/contract-authoring-standard.md) and the shared template. Legacy behavior is documented as-is and is not a model for new code.
