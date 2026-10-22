# Ticket hours user boundary

ticket_hours.user_id stores the UUID of the operator who logged work. The
User module owns UUID user identifiers, while Fixcity owns the ticket-hours
record and its reporting relationships.

The forward migration converts the legacy unsigned integer column to
varchar(36). This preserves the feature's purpose: showing effort against a
ticket and in an operator profile. The old integer shape was not usable with
current users, so the seeder skipped every row instead of silently writing
invalid data.

The invariant is verified by FixcityDemoSeederTest: DMO seed data creates
work-hour rows, values stay in half-hour increments, and totals remain within
the ticket estimate.
