/*
  # Add stable key slug to statistics

  ## Problem
  Public pages (homepage hero, About, Success Stories) hardcoded numbers like
  "2,000+ students" and "94% success rate" as plain strings instead of reading
  them from the `statistics` table, so admin edits to a stat's value never
  propagated to every place that number appears, and the same metric drifted
  to different values across pages.

  ## Fix
  Add a stable `key` slug column to `public.statistics` so app code can look
  up a specific stat (e.g. 'students_trained') independent of its editable
  label/order, backfill keys for the existing seeded rows, and add an
  'avg_rating' stat used by the homepage hero and success-stories page.
*/

ALTER TABLE public.statistics ADD COLUMN IF NOT EXISTS key text UNIQUE;

UPDATE public.statistics SET key = 'students_trained' WHERE label = 'Students Trained' AND key IS NULL;
UPDATE public.statistics SET key = 'programs_offered' WHERE label = 'Programs Offered' AND key IS NULL;
UPDATE public.statistics SET key = 'success_rate' WHERE label = 'Success Rate' AND key IS NULL;
UPDATE public.statistics SET key = 'countries_reached' WHERE label = 'Countries Reached' AND key IS NULL;
UPDATE public.statistics SET key = 'years_of_excellence' WHERE label = 'Years of Excellence' AND key IS NULL;
UPDATE public.statistics SET key = 'avg_income_increase' WHERE label = 'Average Income Increase' AND key IS NULL;

INSERT INTO public.statistics (label, value, icon, description, order_index, is_active, key)
SELECT 'Average Rating', '4.8/5', 'Star', 'Average student rating across testimonials', 7, true, 'avg_rating'
WHERE NOT EXISTS (SELECT 1 FROM public.statistics WHERE key = 'avg_rating');
