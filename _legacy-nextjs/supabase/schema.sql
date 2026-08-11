-- Full schema for Qewaxs Academy (idempotent: safe to re-run)
-- Run this BEFORE supabase/seed_data.sql
BEGIN;

CREATE EXTENSION IF NOT EXISTS pgcrypto;

-- ============================================================
-- TABLES
-- ============================================================

CREATE TABLE IF NOT EXISTS public.profiles (
  id uuid PRIMARY KEY REFERENCES auth.users(id) ON DELETE CASCADE,
  email text,
  full_name text,
  phone text,
  avatar_url text,
  role text NOT NULL DEFAULT 'student' CHECK (role IN ('super_admin', 'admin', 'trainer', 'content_manager', 'finance', 'marketing', 'support', 'student')),
  bio text,
  is_active boolean NOT NULL DEFAULT true,
  created_at timestamptz NOT NULL DEFAULT now(),
  updated_at timestamptz NOT NULL DEFAULT now()
);

CREATE TABLE IF NOT EXISTS public.site_settings (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  key text NOT NULL UNIQUE,
  value text,
  value_json jsonb,
  category text NOT NULL,
  label text,
  description text,
  updated_at timestamptz NOT NULL DEFAULT now()
);

CREATE TABLE IF NOT EXISTS public.nav_menus (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  name text NOT NULL,
  location text NOT NULL,
  is_active boolean NOT NULL DEFAULT true,
  created_at timestamptz NOT NULL DEFAULT now()
);

CREATE TABLE IF NOT EXISTS public.nav_items (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  menu_id uuid REFERENCES public.nav_menus(id) ON DELETE CASCADE,
  parent_id uuid REFERENCES public.nav_items(id) ON DELETE CASCADE,
  label text NOT NULL,
  url text,
  icon text,
  target text DEFAULT '_self',
  order_index integer NOT NULL DEFAULT 0,
  is_active boolean NOT NULL DEFAULT true,
  badge text,
  created_at timestamptz NOT NULL DEFAULT now()
);

CREATE TABLE IF NOT EXISTS public.program_categories (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  name text NOT NULL,
  slug text NOT NULL UNIQUE,
  description text,
  icon text,
  color text,
  order_index integer NOT NULL DEFAULT 0,
  is_active boolean NOT NULL DEFAULT true,
  created_at timestamptz NOT NULL DEFAULT now()
);

CREATE TABLE IF NOT EXISTS public.programs (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  category_id uuid REFERENCES public.program_categories(id) ON DELETE SET NULL,
  title text NOT NULL,
  slug text NOT NULL UNIQUE,
  subtitle text,
  description text,
  short_description text,
  thumbnail_url text,
  gallery_urls text[],
  duration_weeks integer,
  duration_label text,
  level text NOT NULL DEFAULT 'beginner',
  delivery_mode text NOT NULL DEFAULT 'online',
  price numeric,
  original_price numeric,
  currency text NOT NULL DEFAULT 'KES',
  is_featured boolean NOT NULL DEFAULT false,
  is_active boolean NOT NULL DEFAULT true,
  is_published boolean NOT NULL DEFAULT false,
  rating numeric NOT NULL DEFAULT 0,
  review_count integer NOT NULL DEFAULT 0,
  enrollment_count integer NOT NULL DEFAULT 0,
  curriculum jsonb,
  outcomes text[],
  requirements text[],
  seo_title text,
  seo_description text,
  seo_keywords text,
  order_index integer NOT NULL DEFAULT 0,
  created_at timestamptz NOT NULL DEFAULT now(),
  updated_at timestamptz NOT NULL DEFAULT now()
);

CREATE TABLE IF NOT EXISTS public.trainers (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  profile_id uuid REFERENCES public.profiles(id) ON DELETE SET NULL,
  full_name text NOT NULL,
  title text,
  bio text,
  avatar_url text,
  email text,
  phone text,
  specializations text[],
  social_links jsonb,
  rating numeric NOT NULL DEFAULT 0,
  review_count integer NOT NULL DEFAULT 0,
  is_featured boolean NOT NULL DEFAULT false,
  is_active boolean NOT NULL DEFAULT true,
  order_index integer NOT NULL DEFAULT 0,
  created_at timestamptz NOT NULL DEFAULT now(),
  updated_at timestamptz NOT NULL DEFAULT now()
);

CREATE TABLE IF NOT EXISTS public.cohorts (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  program_id uuid NOT NULL REFERENCES public.programs(id) ON DELETE CASCADE,
  trainer_id uuid REFERENCES public.trainers(id) ON DELETE SET NULL,
  name text NOT NULL,
  start_date date NOT NULL,
  end_date date,
  registration_deadline date,
  total_seats integer NOT NULL DEFAULT 0,
  booked_seats integer NOT NULL DEFAULT 0,
  schedule_details text,
  schedule_json jsonb,
  venue text,
  venue_address text,
  online_link text,
  online_platform text,
  price numeric,
  currency text NOT NULL DEFAULT 'KES',
  status text NOT NULL DEFAULT 'upcoming',
  is_featured boolean NOT NULL DEFAULT false,
  notes text,
  created_at timestamptz NOT NULL DEFAULT now(),
  updated_at timestamptz NOT NULL DEFAULT now()
);

CREATE SEQUENCE IF NOT EXISTS public.booking_number_seq;

CREATE TABLE IF NOT EXISTS public.bookings (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  booking_number text NOT NULL UNIQUE DEFAULT ('BK' || LPAD(nextval('public.booking_number_seq')::text, 6, '0')),
  user_id uuid REFERENCES public.profiles(id) ON DELETE SET NULL,
  program_id uuid NOT NULL REFERENCES public.programs(id) ON DELETE CASCADE,
  cohort_id uuid REFERENCES public.cohorts(id) ON DELETE SET NULL,
  full_name text NOT NULL,
  email text NOT NULL,
  phone text NOT NULL,
  id_number text,
  date_of_birth date,
  gender text,
  nationality text,
  address text,
  city text,
  country text NOT NULL DEFAULT 'Kenya',
  current_occupation text,
  employer text,
  education_level text,
  payment_method text,
  payment_reference text,
  amount_paid numeric NOT NULL DEFAULT 0,
  total_amount numeric,
  currency text NOT NULL DEFAULT 'KES',
  payment_status text NOT NULL DEFAULT 'pending',
  status text NOT NULL DEFAULT 'draft',
  referral_source text,
  emergency_contact_name text,
  emergency_contact_phone text,
  special_requirements text,
  documents_urls text[],
  admin_notes text,
  rejection_reason text,
  consent_given boolean NOT NULL DEFAULT false,
  confirmed_at timestamptz,
  approved_at timestamptz,
  approved_by uuid REFERENCES public.profiles(id) ON DELETE SET NULL,
  created_at timestamptz NOT NULL DEFAULT now(),
  updated_at timestamptz NOT NULL DEFAULT now()
);

CREATE TABLE IF NOT EXISTS public.blog_categories (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  name text NOT NULL,
  slug text NOT NULL UNIQUE,
  description text,
  color text,
  order_index integer NOT NULL DEFAULT 0,
  is_active boolean NOT NULL DEFAULT true,
  created_at timestamptz NOT NULL DEFAULT now()
);

CREATE TABLE IF NOT EXISTS public.blog_posts (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  category_id uuid REFERENCES public.blog_categories(id) ON DELETE SET NULL,
  author_id uuid REFERENCES public.profiles(id) ON DELETE SET NULL,
  title text NOT NULL,
  slug text NOT NULL UNIQUE,
  excerpt text,
  content text,
  thumbnail_url text,
  tags text[],
  is_featured boolean NOT NULL DEFAULT false,
  is_published boolean NOT NULL DEFAULT false,
  published_at timestamptz,
  view_count integer NOT NULL DEFAULT 0,
  seo_title text,
  seo_description text,
  seo_keywords text,
  read_time_minutes integer,
  created_at timestamptz NOT NULL DEFAULT now(),
  updated_at timestamptz NOT NULL DEFAULT now()
);

CREATE TABLE IF NOT EXISTS public.testimonials (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  program_id uuid REFERENCES public.programs(id) ON DELETE SET NULL,
  student_name text NOT NULL,
  student_title text,
  student_avatar_url text,
  content text NOT NULL,
  rating numeric,
  income_before text,
  income_after text,
  video_url text,
  is_featured boolean NOT NULL DEFAULT false,
  is_published boolean NOT NULL DEFAULT true,
  order_index integer NOT NULL DEFAULT 0,
  created_at timestamptz NOT NULL DEFAULT now()
);

CREATE TABLE IF NOT EXISTS public.faqs (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  category text NOT NULL,
  question text NOT NULL,
  answer text NOT NULL,
  order_index integer NOT NULL DEFAULT 0,
  is_published boolean NOT NULL DEFAULT true,
  created_at timestamptz NOT NULL DEFAULT now(),
  updated_at timestamptz NOT NULL DEFAULT now()
);

CREATE TABLE IF NOT EXISTS public.advertisements (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  campaign_name text NOT NULL,
  title text,
  subtitle text,
  description text,
  type text NOT NULL,
  placement text[] NOT NULL DEFAULT '{}',
  desktop_image_url text,
  mobile_image_url text,
  video_url text,
  cta_text text,
  cta_link text,
  button_style text,
  background_color text,
  overlay_color text,
  animation text,
  priority integer NOT NULL DEFAULT 0,
  target_audience jsonb,
  status text NOT NULL DEFAULT 'draft',
  publish_date timestamptz,
  expiry_date timestamptz,
  view_count integer NOT NULL DEFAULT 0,
  click_count integer NOT NULL DEFAULT 0,
  created_at timestamptz NOT NULL DEFAULT now(),
  updated_at timestamptz NOT NULL DEFAULT now()
);

CREATE TABLE IF NOT EXISTS public.ctas (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  name text NOT NULL,
  title text NOT NULL,
  subtitle text,
  description text,
  background_color text,
  background_image_url text,
  button_one_text text,
  button_one_link text,
  button_one_style text,
  button_two_text text,
  button_two_link text,
  button_two_style text,
  placement text[],
  priority integer NOT NULL DEFAULT 0,
  is_active boolean NOT NULL DEFAULT true,
  publish_date timestamptz,
  expiry_date timestamptz,
  created_at timestamptz NOT NULL DEFAULT now(),
  updated_at timestamptz NOT NULL DEFAULT now()
);

CREATE TABLE IF NOT EXISTS public.pages (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  title text NOT NULL,
  slug text NOT NULL UNIQUE,
  description text,
  is_published boolean NOT NULL DEFAULT false,
  seo_title text,
  seo_description text,
  seo_keywords text,
  og_image_url text,
  created_at timestamptz NOT NULL DEFAULT now(),
  updated_at timestamptz NOT NULL DEFAULT now()
);

CREATE TABLE IF NOT EXISTS public.page_blocks (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  page_id uuid NOT NULL REFERENCES public.pages(id) ON DELETE CASCADE,
  type text NOT NULL,
  content jsonb NOT NULL DEFAULT '{}',
  order_index integer NOT NULL DEFAULT 0,
  is_active boolean NOT NULL DEFAULT true,
  created_at timestamptz NOT NULL DEFAULT now(),
  updated_at timestamptz NOT NULL DEFAULT now()
);

CREATE TABLE IF NOT EXISTS public.resources (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  program_id uuid REFERENCES public.programs(id) ON DELETE SET NULL,
  title text NOT NULL,
  description text,
  type text NOT NULL,
  file_url text,
  thumbnail_url text,
  is_free boolean NOT NULL DEFAULT true,
  is_published boolean NOT NULL DEFAULT true,
  download_count integer NOT NULL DEFAULT 0,
  tags text[],
  order_index integer NOT NULL DEFAULT 0,
  created_at timestamptz NOT NULL DEFAULT now(),
  updated_at timestamptz NOT NULL DEFAULT now()
);

CREATE TABLE IF NOT EXISTS public.team_members (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  full_name text NOT NULL,
  title text NOT NULL,
  bio text,
  avatar_url text,
  email text,
  social_links jsonb,
  department text,
  is_featured boolean NOT NULL DEFAULT false,
  is_active boolean NOT NULL DEFAULT true,
  order_index integer NOT NULL DEFAULT 0,
  created_at timestamptz NOT NULL DEFAULT now()
);

CREATE TABLE IF NOT EXISTS public.partners (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  name text NOT NULL,
  logo_url text,
  website_url text,
  description text,
  is_active boolean NOT NULL DEFAULT true,
  order_index integer NOT NULL DEFAULT 0,
  created_at timestamptz NOT NULL DEFAULT now()
);

CREATE TABLE IF NOT EXISTS public.contact_submissions (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  full_name text NOT NULL,
  email text NOT NULL,
  phone text,
  subject text,
  message text NOT NULL,
  status text NOT NULL DEFAULT 'new',
  admin_notes text,
  created_at timestamptz NOT NULL DEFAULT now(),
  updated_at timestamptz NOT NULL DEFAULT now()
);

CREATE TABLE IF NOT EXISTS public.statistics (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  label text NOT NULL,
  value text NOT NULL,
  icon text,
  description text,
  order_index integer NOT NULL DEFAULT 0,
  is_active boolean NOT NULL DEFAULT true,
  created_at timestamptz NOT NULL DEFAULT now()
);

CREATE TABLE IF NOT EXISTS public.notifications (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  user_id uuid REFERENCES public.profiles(id) ON DELETE CASCADE,
  title text NOT NULL,
  message text NOT NULL,
  type text NOT NULL DEFAULT 'info',
  link text,
  is_read boolean NOT NULL DEFAULT false,
  created_at timestamptz NOT NULL DEFAULT now()
);

CREATE TABLE IF NOT EXISTS public.audit_logs (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  user_id uuid REFERENCES public.profiles(id) ON DELETE SET NULL,
  action text NOT NULL,
  resource_type text NOT NULL,
  resource_id text,
  old_values jsonb,
  new_values jsonb,
  ip_address text,
  user_agent text,
  created_at timestamptz NOT NULL DEFAULT now()
);

-- ============================================================
-- HELPER: is_admin() — SECURITY DEFINER to avoid RLS recursion
-- ============================================================

CREATE OR REPLACE FUNCTION public.is_admin()
RETURNS boolean
LANGUAGE sql
SECURITY DEFINER
STABLE
SET search_path = public
AS $$
  SELECT EXISTS (
    SELECT 1 FROM public.profiles
    WHERE id = auth.uid() AND role IN ('super_admin', 'admin')
  );
$$;

-- ============================================================
-- AUTO-CREATE PROFILE ON SIGNUP (same as migration 002)
-- ============================================================

CREATE OR REPLACE FUNCTION handle_new_user()
RETURNS trigger AS $$
BEGIN
  INSERT INTO public.profiles (id, email, full_name, role)
  VALUES (
    NEW.id,
    NEW.email,
    COALESCE(NEW.raw_user_meta_data->>'full_name', split_part(NEW.email, '@', 1)),
    'student'
  )
  ON CONFLICT (id) DO NOTHING;
  RETURN NEW;
END;
$$ LANGUAGE plpgsql SECURITY DEFINER;

DROP TRIGGER IF EXISTS on_auth_user_created ON auth.users;
CREATE TRIGGER on_auth_user_created
  AFTER INSERT ON auth.users
  FOR EACH ROW EXECUTE FUNCTION handle_new_user();

-- ============================================================
-- ROW LEVEL SECURITY
-- ============================================================

ALTER TABLE public.profiles ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.site_settings ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.nav_menus ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.nav_items ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.program_categories ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.programs ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.trainers ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.cohorts ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.bookings ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.blog_categories ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.blog_posts ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.testimonials ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.faqs ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.advertisements ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.ctas ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.pages ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.page_blocks ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.resources ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.team_members ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.partners ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.contact_submissions ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.statistics ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.notifications ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.audit_logs ENABLE ROW LEVEL SECURITY;

-- profiles
DROP POLICY IF EXISTS "profiles_select_own_or_admin" ON public.profiles;
CREATE POLICY "profiles_select_own_or_admin" ON public.profiles FOR SELECT TO authenticated
  USING (auth.uid() = id OR public.is_admin());

DROP POLICY IF EXISTS "profiles_insert_own" ON public.profiles;
CREATE POLICY "profiles_insert_own" ON public.profiles FOR INSERT TO authenticated
  WITH CHECK (auth.uid() = id);

DROP POLICY IF EXISTS "profiles_update_own_or_admin" ON public.profiles;
CREATE POLICY "profiles_update_own_or_admin" ON public.profiles FOR UPDATE TO authenticated
  USING (auth.uid() = id OR public.is_admin())
  WITH CHECK (auth.uid() = id OR public.is_admin());

-- Public read-only content tables: anon + authenticated can SELECT, only admins can write
DO $$
DECLARE
  t text;
BEGIN
  FOREACH t IN ARRAY ARRAY[
    'site_settings', 'nav_menus', 'nav_items', 'program_categories', 'programs',
    'trainers', 'cohorts', 'blog_categories', 'blog_posts', 'testimonials', 'faqs',
    'advertisements', 'ctas', 'pages', 'page_blocks', 'resources', 'team_members',
    'partners', 'statistics'
  ]
  LOOP
    EXECUTE format('DROP POLICY IF EXISTS "%s_select_public" ON public.%I', t, t);
    EXECUTE format('CREATE POLICY "%s_select_public" ON public.%I FOR SELECT TO anon, authenticated USING (true)', t, t);

    EXECUTE format('DROP POLICY IF EXISTS "%s_insert_admin" ON public.%I', t, t);
    EXECUTE format('CREATE POLICY "%s_insert_admin" ON public.%I FOR INSERT TO authenticated WITH CHECK (public.is_admin())', t, t);

    EXECUTE format('DROP POLICY IF EXISTS "%s_update_admin" ON public.%I', t, t);
    EXECUTE format('CREATE POLICY "%s_update_admin" ON public.%I FOR UPDATE TO authenticated USING (public.is_admin()) WITH CHECK (public.is_admin())', t, t);

    EXECUTE format('DROP POLICY IF EXISTS "%s_delete_admin" ON public.%I', t, t);
    EXECUTE format('CREATE POLICY "%s_delete_admin" ON public.%I FOR DELETE TO authenticated USING (public.is_admin())', t, t);
  END LOOP;
END $$;

-- bookings: public form inserts as anon; users see own; admins see/manage all
DROP POLICY IF EXISTS "bookings_insert_anon" ON public.bookings;
CREATE POLICY "bookings_insert_anon" ON public.bookings FOR INSERT TO anon, authenticated
  WITH CHECK (true);

DROP POLICY IF EXISTS "bookings_select_anon" ON public.bookings;
CREATE POLICY "bookings_select_anon" ON public.bookings FOR SELECT TO anon
  USING (email = current_setting('request.jwt.claims', true)::json->>'email');

DROP POLICY IF EXISTS "bookings_select_anon_draft" ON public.bookings;
CREATE POLICY "bookings_select_anon_draft" ON public.bookings FOR SELECT TO anon
  USING (user_id IS NULL);

DROP POLICY IF EXISTS "bookings_select_own_or_admin" ON public.bookings;
CREATE POLICY "bookings_select_own_or_admin" ON public.bookings FOR SELECT TO authenticated
  USING (user_id = auth.uid() OR public.is_admin());

DROP POLICY IF EXISTS "bookings_update_admin" ON public.bookings;
CREATE POLICY "bookings_update_admin" ON public.bookings FOR UPDATE TO authenticated
  USING (public.is_admin()) WITH CHECK (public.is_admin());

DROP POLICY IF EXISTS "bookings_delete_admin" ON public.bookings;
CREATE POLICY "bookings_delete_admin" ON public.bookings FOR DELETE TO authenticated
  USING (public.is_admin());

-- contact_submissions: public form inserts as anon; only admins can read/manage
DROP POLICY IF EXISTS "contact_submissions_insert_anon" ON public.contact_submissions;
CREATE POLICY "contact_submissions_insert_anon" ON public.contact_submissions FOR INSERT TO anon, authenticated
  WITH CHECK (true);

DROP POLICY IF EXISTS "contact_submissions_select_admin" ON public.contact_submissions;
CREATE POLICY "contact_submissions_select_admin" ON public.contact_submissions FOR SELECT TO authenticated
  USING (public.is_admin());

DROP POLICY IF EXISTS "contact_submissions_update_admin" ON public.contact_submissions;
CREATE POLICY "contact_submissions_update_admin" ON public.contact_submissions FOR UPDATE TO authenticated
  USING (public.is_admin()) WITH CHECK (public.is_admin());

DROP POLICY IF EXISTS "contact_submissions_delete_admin" ON public.contact_submissions;
CREATE POLICY "contact_submissions_delete_admin" ON public.contact_submissions FOR DELETE TO authenticated
  USING (public.is_admin());

-- notifications: users see/manage own; admins see/manage all
DROP POLICY IF EXISTS "notifications_select_own_or_admin" ON public.notifications;
CREATE POLICY "notifications_select_own_or_admin" ON public.notifications FOR SELECT TO authenticated
  USING (user_id = auth.uid() OR public.is_admin());

DROP POLICY IF EXISTS "notifications_insert_admin" ON public.notifications;
CREATE POLICY "notifications_insert_admin" ON public.notifications FOR INSERT TO authenticated
  WITH CHECK (public.is_admin());

DROP POLICY IF EXISTS "notifications_update_own_or_admin" ON public.notifications;
CREATE POLICY "notifications_update_own_or_admin" ON public.notifications FOR UPDATE TO authenticated
  USING (user_id = auth.uid() OR public.is_admin()) WITH CHECK (user_id = auth.uid() OR public.is_admin());

DROP POLICY IF EXISTS "notifications_delete_own_or_admin" ON public.notifications;
CREATE POLICY "notifications_delete_own_or_admin" ON public.notifications FOR DELETE TO authenticated
  USING (user_id = auth.uid() OR public.is_admin());

-- audit_logs: admin-only, insert can come from any authenticated action being logged
DROP POLICY IF EXISTS "audit_logs_select_admin" ON public.audit_logs;
CREATE POLICY "audit_logs_select_admin" ON public.audit_logs FOR SELECT TO authenticated
  USING (public.is_admin());

DROP POLICY IF EXISTS "audit_logs_insert_authenticated" ON public.audit_logs;
CREATE POLICY "audit_logs_insert_authenticated" ON public.audit_logs FOR INSERT TO authenticated
  WITH CHECK (true);

-- ============================================================
-- STORAGE: public "media" bucket, admin-only writes
-- ============================================================

INSERT INTO storage.buckets (id, name, public, file_size_limit, allowed_mime_types)
VALUES ('media', 'media', true, 5242880, ARRAY['image/png', 'image/jpeg', 'image/webp', 'image/gif'])
ON CONFLICT (id) DO UPDATE SET
  public = EXCLUDED.public,
  file_size_limit = EXCLUDED.file_size_limit,
  allowed_mime_types = EXCLUDED.allowed_mime_types;

DROP POLICY IF EXISTS "media_public_read" ON storage.objects;
CREATE POLICY "media_public_read" ON storage.objects FOR SELECT
  TO anon, authenticated USING (bucket_id = 'media');

DROP POLICY IF EXISTS "media_admin_insert" ON storage.objects;
CREATE POLICY "media_admin_insert" ON storage.objects FOR INSERT
  TO authenticated WITH CHECK (bucket_id = 'media' AND public.is_admin());

DROP POLICY IF EXISTS "media_admin_update" ON storage.objects;
CREATE POLICY "media_admin_update" ON storage.objects FOR UPDATE
  TO authenticated USING (bucket_id = 'media' AND public.is_admin())
  WITH CHECK (bucket_id = 'media' AND public.is_admin());

DROP POLICY IF EXISTS "media_admin_delete" ON storage.objects;
CREATE POLICY "media_admin_delete" ON storage.objects FOR DELETE
  TO authenticated USING (bucket_id = 'media' AND public.is_admin());

COMMIT;
