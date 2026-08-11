/*
# Media storage bucket

## Problem
Images across the admin dashboard (trainer avatars, testimonial photos,
blog/program thumbnails, ad creatives, site-setting images) were entered
as raw URLs. Admins want to upload image files straight from the admin
dashboard, which requires a Supabase Storage bucket that accepts image
uploads and serves them publicly.

## Fix
Create a single public "media" bucket shared by all admin image uploads
(each feature uses its own path prefix, e.g. trainers/, testimonials/,
blog/, programs/, advertisements/, settings/). Anyone can read (images
are shown on public pages), but only admins can upload/replace/delete
objects, matching the write rules already used for admin-managed tables.
*/

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
