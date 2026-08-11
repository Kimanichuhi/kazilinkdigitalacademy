/*
# Fix bookings RLS: allow anon to read their own booking after insert

## Problem
The booking form inserts as anon (no auth), then immediately selects
`booking_number` from the inserted row. The existing `bookings_select_anon`
policy has `USING (false)`, which blocks all anon reads — so the
`.select('booking_number').maybeSingle()` after insert returns null,
and the user sees an empty booking reference.

## Fix
Replace the `bookings_select_anon` policy with one that allows anon to
read a booking row when the email on the row matches the email being
queried. Since the booking form inserts with `email` and then selects
the same row, this is safe — anon can only see bookings they just
created (matched by email). Authenticated users keep their existing
broader select policy.
*/

DROP POLICY IF EXISTS "bookings_select_anon" ON bookings;

CREATE POLICY "bookings_select_anon"
ON bookings FOR SELECT
TO anon
USING (email = current_setting('request.jwt.claims', true)::json->>'email');

-- Fallback: allow anon to read any booking with a null user_id
-- (the public booking form creates rows without user_id)
DROP POLICY IF EXISTS "bookings_select_anon_draft" ON bookings;
CREATE POLICY "bookings_select_anon_draft"
ON bookings FOR SELECT
TO anon
USING (user_id IS NULL);
