# Lessons Learned – Wizard Summary / Submit Button

## Best Practices
- The **final step of a wizard (summary/riepilogo)** must contain a submit button with `type="submit"`.
- Use Filament’s built‑in actions (`->action(...)`) or a Blade `<button>` inside the `<form>`.
- Keep button text translatable via `__()` and follow the Design‑Comuni button styles.
- Validate required fields before reaching the summary; the UI should never be stuck without a way to submit.

## Bad Practices
- Omitting the submit button from the summary step, causing the user to be stuck.
- Adding a second, duplicate button outside the `<form>` that does nothing.
- Using non‑semantic markup (e.g., a `<div>` styled as a button) which breaks accessibility and form submission.

## False Friends
- **"The wizard auto‑submits"** – it doesn’t; a manual action is always required.
- **"A button can be placed anywhere"** – it must reside inside the `<form>` and be clearly labeled.
- **"We can rely on JavaScript to submit"** – always provide a native submit button for robustness.

## References
- Wizard Summary State (related to `laravel/Modules/Fixcity/docs/...`)
- Form Validation Rules (project `validation` rules)