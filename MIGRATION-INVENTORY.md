# Migration Inventory — Kazilink Digital Academy (Next.js + Supabase → Laravel 11)

**Phase:** 0 — Recon (no app code written)
**Source:** Next.js 13.5.1 (App Router) + Supabase (Postgres, Auth, Storage)
**Generated:** 2026-07-31

This document is the frozen source-of-truth snapshot the Laravel migration will be
measured against. It supersedes the task brief's SOURCE INVENTORY wherever the two
disagree — the differences are called out explicitly in [§7](#7-discrepancies-vs-task-brief).

---

## 1. Database Schema (`supabase/schema.sql` + `lib/database.types.ts`)

All PKs are `uuid DEFAULT gen_random_uuid()` unless noted. All tables use RLS (see §3).
Postgres `text[]` → target `json` (cast to array). `jsonb` → target `json`. `timestamptz` → target `timestamp` (UTC storage, app tz Africa/Nairobi).

### 1.1 `profiles` (→ Module: User)
PK `id` = FK to `auth.users.id` (no Supabase Auth equivalent in target — becomes the `users` row itself, see §4).
| Column | Type | Notes |
|---|---|---|
| id | uuid PK | |
| email | text | |
| full_name | text | |
| phone | text | |
| avatar_url | text | |
| role | text | CHECK IN (super_admin, admin, trainer, content_manager, finance, marketing, support, student); default `student` |
| bio | text | |
| is_active | boolean | default true |
| created_at / updated_at | timestamptz | |

### 1.2 `site_settings` (→ Cms)
id uuid PK, `key` text UNIQUE NOT NULL, `value` text, `value_json` jsonb, `category` text NOT NULL, `label` text, `description` text, `updated_at`.
Known keys observed in app code: `site_name`, `contact_email`, `contact_phone`, `contact_address`, `contact_whatsapp`, `social_facebook`, `social_instagram`, `social_linkedin`.

### 1.3 `nav_menus` (→ Cms)
id, `name`, `location` (e.g. `header`), `is_active`, `created_at`.

### 1.4 `nav_items` (→ Cms)
id, `menu_id` FK→nav_menus (cascade), `parent_id` FK→nav_items self (cascade, enables one level of dropdown nesting), `label`, `url`, `icon`, `target` default `_self`, `order_index`, `is_active`, `badge`, `created_at`.

### 1.5 `program_categories` (→ Academy)
id, `name`, `slug` UNIQUE, `description`, `icon`, `color`, `order_index`, `is_active`, `created_at`.

### 1.6 `programs` (→ Academy)
id, `category_id` FK→program_categories (SET NULL), `title`, `slug` UNIQUE, `subtitle`, `description`, `short_description`, `thumbnail_url`, `gallery_urls` text[], `duration_weeks` int, `duration_label`, `level` text default `beginner`, `delivery_mode` text default `online`, `price` numeric, `original_price` numeric, `currency` default `KES`, `is_featured`, `is_active`, `is_published` (default false), `rating` numeric default 0, `review_count` int default 0, `enrollment_count` int default 0, `curriculum` jsonb, `outcomes` text[], `requirements` text[], `seo_title`, `seo_description`, `seo_keywords`, `order_index`, timestamps.

### 1.7 `trainers` (→ Academy)
id, `profile_id` FK→profiles (SET NULL, optional link to a user account), `full_name`, `title`, `bio`, `avatar_url`, `email`, `phone`, `specializations` text[], `social_links` jsonb, `rating`, `review_count`, `is_featured`, `is_active`, `order_index`, timestamps.

### 1.8 `cohorts` (→ Academy)
id, `program_id` FK→programs (CASCADE, required), `trainer_id` FK→trainers (SET NULL), `name`, `start_date` date NOT NULL, `end_date` date, `registration_deadline` date, `total_seats` int default 0, `booked_seats` int default 0, `schedule_details` text, `schedule_json` jsonb, `venue`, `venue_address`, `online_link`, `online_platform`, `price` numeric, `currency` default `KES`, `status` text default `upcoming` (observed values: `upcoming`, `open`), `is_featured`, `notes`, timestamps.

### 1.9 `bookings` (→ Booking) — the core lifecycle table
id, `booking_number` text UNIQUE, generated via `nextval('booking_number_seq')` formatted `BK######` — **must be replicated as a Laravel-side generator** (DB sequence + `LPAD` won't port to MySQL as-is).
`user_id` FK→profiles (SET NULL, nullable — guest bookings), `program_id` FK→programs (CASCADE, required), `cohort_id` FK→cohorts (SET NULL, optional).
Personal fields: `full_name`, `email`, `phone` (all required), `id_number`, `date_of_birth` date, `gender`, `nationality`, `address`, `city`, `country` default `Kenya`, `current_occupation`, `employer`, `education_level`.
Payment fields: `payment_method`, `payment_reference`, `amount_paid` numeric default 0, `total_amount` numeric, `currency` default `KES`, `payment_status` text default `pending`.
Lifecycle: `status` text default `draft` (values observed in UI: `draft`, `awaiting_payment`, `paid`, `pending_approval`, `approved`, `rejected`, `cancelled`, `completed`).
Other: `referral_source`, `emergency_contact_name`, `emergency_contact_phone`, `special_requirements`, `documents_urls` text[], `admin_notes`, `rejection_reason`, `consent_given` boolean NOT NULL default false, `confirmed_at`, `approved_at`, `approved_by` FK→profiles (SET NULL), timestamps.

### 1.10 `blog_categories` (→ Cms) — **not in task brief's inventory, exists in source**
id, `name`, `slug` UNIQUE, `description`, `color`, `order_index`, `is_active`, `created_at`.

### 1.11 `blog_posts` (→ Cms)
id, `category_id` FK→blog_categories (SET NULL), `author_id` FK→profiles (SET NULL), `title`, `slug` UNIQUE, `excerpt`, `content`, `thumbnail_url`, `tags` text[], `is_featured`, `is_published` (default false), `published_at`, `view_count` default 0, `seo_title`, `seo_description`, `seo_keywords`, `read_time_minutes` int, timestamps.

### 1.12 `testimonials` (→ Cms) — doubles as reviews, no separate reviews table
id, `program_id` FK→programs (SET NULL, optional), `student_name` NOT NULL, `student_title`, `student_avatar_url`, `content` NOT NULL, `rating` numeric, `income_before`, `income_after`, `video_url`, `is_featured`, `is_published` default true, `order_index`, `created_at` (no `updated_at`).

### 1.13 `faqs` (→ Cms)
id, `category` text NOT NULL, `question` NOT NULL, `answer` NOT NULL, `order_index`, `is_published` default true, timestamps.

### 1.14 `advertisements` (→ Marketing)
id, `campaign_name` NOT NULL, `title`, `subtitle`, `description`, `type` NOT NULL (observed value: `announcement_bar`), `placement` text[] default `{}`, `desktop_image_url`, `mobile_image_url`, `video_url`, `cta_text`, `cta_link`, `button_style`, `background_color`, `overlay_color`, `animation`, `priority` default 0, `target_audience` jsonb, `status` text default `draft` (observed: `active`), `publish_date`, `expiry_date`, `view_count` default 0, `click_count` default 0, timestamps.

### 1.15 `ctas` (→ Marketing)
id, `name`, `title` NOT NULL, `subtitle`, `description`, `background_color`, `background_image_url`, `button_one_text/link/style`, `button_two_text/link/style`, `placement` text[], `priority` default 0, `is_active` default true, `publish_date`, `expiry_date`, timestamps.

### 1.16 `pages` (→ Cms)
id, `title` NOT NULL, `slug` UNIQUE, `description`, `is_published` default false, `seo_title/description/keywords`, `og_image_url`, timestamps.

### 1.17 `page_blocks` (→ Cms)
id, `page_id` FK→pages (CASCADE, required), `type` NOT NULL, `content` jsonb NOT NULL default `{}`, `order_index`, `is_active`, timestamps.

### 1.18 `resources` (→ Cms)
id, `program_id` FK→programs (SET NULL, optional), `title` NOT NULL, `description`, `type` NOT NULL, `file_url`, `thumbnail_url`, `is_free` default true, `is_published` default true, `download_count` default 0, `tags` text[], `order_index`, timestamps.

### 1.19 `team_members` — **not in task brief's inventory, exists in source, no admin UI found**
id, `full_name` NOT NULL, `title` NOT NULL, `bio`, `avatar_url`, `email`, `social_links` jsonb, `department`, `is_featured`, `is_active`, `order_index`, `created_at`.

### 1.20 `partners` — **not in task brief's inventory, exists in source, no admin UI found**
id, `name` NOT NULL, `logo_url`, `website_url`, `description`, `is_active`, `order_index`, `created_at`.

### 1.21 `contact_submissions` — **not in task brief's inventory, exists in source, no admin UI found**
id, `full_name` NOT NULL, `email` NOT NULL, `phone`, `subject`, `message` NOT NULL, `status` default `new`, `admin_notes`, timestamps. Written by the public `/contact` form (anon insert).

### 1.22 `statistics` (→ Marketing) — stat tiles shown on home/about/success-stories
id, `label` NOT NULL, `value` text NOT NULL (free-form, e.g. `"2,000+"`, `"94%"`), `icon`, `description`, `order_index`, `is_active`, `created_at`, `key` text UNIQUE (added by migration `20260730140000`; nullable, used as a stable lookup slug e.g. `students_trained`, `avg_rating`).

### 1.23 `notifications` (→ Notification)
id, `user_id` FK→profiles (CASCADE), `title` NOT NULL, `message` NOT NULL, `type` default `info`, `link`, `is_read` default false, `created_at`. **Table exists with RLS policies but has no admin write UI and no read UI** — Navbar links to `/student/notifications`, which is a 404 in the source (page was never built). Confirms task brief: wire this up as a genuine addition in Phase 7.

### 1.24 `audit_logs` (→ Audit)
id, `user_id` FK→profiles (SET NULL), `action` NOT NULL, `resource_type` NOT NULL, `resource_id` text, `old_values` jsonb, `new_values` jsonb, `ip_address`, `user_agent`, `created_at`. **No app code writes to this table** — RLS policies exist (admin SELECT, authenticated INSERT) but zero call sites found in `app/`. Treat as an inert table with policy scaffolding only; the Laravel `Audit` module listener wiring is new functional behavior, not a port of existing logic.

### 1.25 Sequences / triggers
- `booking_number_seq` — feeds `bookings.booking_number` (`'BK' || LPAD(nextval(...)::text, 6, '0')`).
- `handle_new_user()` trigger on `auth.users` insert → auto-creates a `profiles` row with role `student`. Target equivalent: Breeze `Registered` listener / model event creating the role assignment (spatie) at registration — see §4.

### 1.26 Storage
One public bucket `media` (5 MB limit, mime allow-list `image/png, image/jpeg, image/webp, image/gif`). Path convention: `<feature>/<uuid>.<ext>` (e.g. `trainers/…`, `testimonials/…`, `blog/…`, `programs/…`, `advertisements/…`, `settings/…`). Public read for everyone; write restricted to `is_admin()`. → target: `public` disk, same path convention, `ImageUpload` Blade/Livewire component enforces the 5 MB + image-mime check both client- and server-side (Form Request rule).

---

## 2. Row-Level Security → Target Policy Mapping

| Source RLS pattern | Target Laravel equivalent |
|---|---|
| `is_admin()` SECURITY DEFINER helper (role IN admin, super_admin) | `User::isAdminFamily()` helper / Gate; note this is narrower than the "admin-family" set used in app-side redirect checks (which also includes content_manager, marketing, finance, support — see §4) |
| Public content tables: anon+authenticated SELECT true, admin-only write | Policy `viewAny` open, `create/update/delete` gated to admin-family roles per module |
| `profiles`: self or admin SELECT/UPDATE | `ProfilePolicy::view/update` |
| `bookings`: anon INSERT (true), anon SELECT by matching email OR `user_id IS NULL`, authenticated SELECT own-or-admin, admin UPDATE/DELETE | `BookingPolicy`; guest booking retrieval by email match must be reproduced carefully (session-based receipt view, not full anon SELECT-by-email, since Laravel has no anon JWT claims mechanism) |
| `contact_submissions`: anon INSERT, admin SELECT/UPDATE/DELETE | `ContactSubmissionPolicy`, admin-only CRUD (no admin UI currently — Phase 7 addition if kept in scope) |
| `notifications`: own-or-admin SELECT/UPDATE/DELETE, admin-only INSERT | `NotificationPolicy` |
| `audit_logs`: admin SELECT, authenticated INSERT (true) | Written only by the `Audit` listener, never directly by users — Policy `viewAny` admin-only |
| Storage `media` bucket: public read, admin write | Filesystem `public` disk; upload endpoints gated behind admin middleware/policy per module |

---

## 3. Routes / Pages Enumerated (Next.js App Router)

### 3.1 Public (no auth), layout: `app/(public)/layout.tsx` wraps Navbar+Footer
- `/` — home (stat tiles from `statistics`, testimonials, featured programs, announcement bar from `advertisements` type `announcement_bar`)
- `/about`
- `/programs` — listing with **client-side filters**: category, level, delivery mode, search
- `/programs/[slug]` — program detail
- `/cohorts`
- `/pricing`
- `/blog`
- `/blog/[slug]`
- `/faq`
- `/resources`
- `/success-stories`
- `/contact` — contact form (zod, see §5) writing to `contact_submissions`, reads `site_settings` for contact info display

### 3.2 Booking (own layout, still public — Navbar+Footer, no auth required)
- `/booking` — 4-step wizard, see §6 in full detail

### 3.3 Auth
- `/auth/login`
- `/auth/register`

### 3.4 Student (auth-guarded: any logged-in user; layout redirects to `/auth/login` if unauthenticated)
- `/student` — dashboard: booking list + status badges, profile card, quick links
- `/student/profile`
- `/student/notifications` — **referenced in Navbar, page does not exist in source (404)**. Genuine new build in target, not a port.

### 3.5 Admin (auth-guarded: role must be one of `admin, super_admin, content_manager, marketing, finance, support`; redirects to `/student` otherwise)
Sidebar groups and routes (`components/admin/AdminSidebar.tsx`):
- Overview: `/admin` (dashboard, has recharts), `/admin/analytics` (recharts)
- Bookings & Students: `/admin/bookings`, `/admin/students`, `/admin/cohorts`
- Programs: `/admin/programs`, `/admin/trainers`
- Marketing & CMS: `/admin/stats`, `/admin/advertisements`, `/admin/ctas`, `/admin/blog`, `/admin/testimonials`, `/admin/faqs`, `/admin/resources`
- Navigation: `/admin/menus`
- System: `/admin/users`, `/admin/settings`

No route exists for `team_members`, `partners`, or `contact_submissions` admin management in the source, despite the tables existing (§1.19–1.21).

### 3.6 Trainer
No `/trainer` route exists anywhere in `app/`. Confirmed absent — task brief's instruction to scaffold an empty guarded `/trainer` module is a **net-new addition**, not a port.

---

## 4. Auth & Role Redirect Logic

Auth is 100% client-side Supabase (`@supabase/supabase-js`), no server session/cookie middleware — `AuthProvider` (`components/providers/AuthProvider.tsx`) holds session/user/profile in React context, fetched via `supabase.auth.getSession()` + `onAuthStateChange`.

**Redirect rule (from `app/auth/login/page.tsx:44-49`), fires immediately after successful `signInWithPassword`:**
```
role in [admin, super_admin, content_manager, marketing, finance, support] → /admin
role === trainer                                                            → /trainer
else (student, or profile not yet loaded)                                   → /student
```
This exact 8-role partition must be reproduced in the Laravel `Auth` module's post-login redirect (`RedirectIfAuthenticated` / login response handler) — it is the authoritative version, not the simplified 3-way split implied by the task brief's phrasing.

**Registration** (`app/auth/register/page.tsx`): Supabase `signUp` + manual `profiles` insert with `role: 'student'` hardcoded (the DB trigger `handle_new_user` is redundant/defensive — the client already inserts the profile row explicitly, `ON CONFLICT DO NOTHING` on the trigger avoids a collision). Zod: full_name min 2, email, phone min 9 chars, password min 6, confirm_password must match.

**Guards observed:**
- `app/admin/layout.tsx`: not logged in → `/auth/login`; logged in but role not admin-family → `/student`.
- `app/student/layout.tsx`: not logged in → `/auth/login`. No role check — any authenticated user (including admin-family) can view `/student`.
- No guard exists for a `/trainer` area since it doesn't exist.

---

## 5. Form Validation (Zod Schemas) — the only 4 in the codebase

Admin CRUD screens (`app/admin/**`) use **plain controlled `useState` forms with no zod/react-hook-form** — confirmed via repo-wide search. Their Laravel Form Requests will need rules inferred from the DB schema + observed `required`/UI constraints rather than ported from an explicit schema.

1. **Login** (`app/auth/login/page.tsx`): `email` (email), `password` (min 6, message "Password must be at least 6 characters").
2. **Register** (`app/auth/register/page.tsx`): `full_name` (min 2, "Full name required"), `email` (email, "Valid email required"), `phone` (min 9, "Valid phone required"), `password` (min 6, "Minimum 6 characters"), `confirm_password` (refine equals password, error path `confirm_password`, "Passwords do not match").
3. **Contact** (`app/(public)/contact/page.tsx`): `full_name` (min 2, "Name required"), `email` (email, "Valid email required"), `phone` (optional), `subject` (optional), `message` (min 10, "Message must be at least 10 characters").
4. **Booking personal details, step 2** (`app/booking/page.tsx`): `full_name` (min 2), `email` (email), `phone` (min 9), `id_number/date_of_birth/gender/nationality/address/city/current_occupation/employer/education_level/referral_source/emergency_contact_name/emergency_contact_phone/special_requirements` all optional strings, `country` defaults `'Kenya'`, `consent_given` boolean refined to `=== true` ("You must agree to the terms").

All error messages above must be reproduced verbatim in Form Request `messages()` per the mapping table's "same fields, same rules, same msgs" rule.

---

## 6. Booking Wizard — Full Behavioral Spec (`app/booking/page.tsx`)

4 steps, no login required, state held in a single React component (not persisted between steps server-side):

1. **Program** — grid of published+active programs (`is_published=true, is_active=true`, ordered by `order_index`); selecting a program loads its cohorts (`status IN (open, upcoming)`, ordered by `start_date`); cohort selection optional (toggleable, "No open cohorts" fallback message shown). Supports deep-link preselection via `?program=<id>` or `?cohort=<id>` query params (skips to step 2 automatically if a cohort is preselected).
2. **Details** — personal info form (zod schema in §5), 4 fieldset groups: Personal Information, Professional Background, Emergency Contact, Special Requirements (textarea). "Change" link jumps back to step 1 and clears cohort selection.
3. **Payment** — method selector (`mpesa` default / `stripe` / `bank`), each shows different static instructional content:
   - `mpesa`: static Paybill instructions (business number `123456`, account = phone number) + optional manual transaction-code input (uppercased on input).
   - `bank`: static bank details block (Equity Bank Kenya, account name/number/branch — all hardcoded in JSX, not from `site_settings`).
   - `stripe`: static "will be processed after booking confirmation" message — **fully inert, no Stripe SDK/API call anywhere in the codebase.** Confirms task brief's "Stripe as a clearly-marked stub" — the *source itself* is already just a stub, there is nothing functional to port.
   - **Critically: there is no M-Pesa Daraja STK push integration anywhere in the source.** The "mpesa" option is static instructions + an optional free-text reference code the user types in manually after paying externally. Building live Daraja STK push in the target (per task brief Phase 6) is **net-new functionality**, not a port — flag this to the user before Phase 6 since it changes the effort/scope of that phase materially and requires real Safaricom Daraja credentials.
4. **Review** — read-only summary of program/cohort/amount, personal details subset, payment method/reference, consent checkbox (same zod field as step-2 schema, physically rendered in step 4's JSX) with links to `/terms`, `/privacy`, `/refund` (none of these pages exist in `app/` — dead links in source, out of scope unless user asks to add them).

**Submit logic:** builds a flat `bookingData` object, `status` is set client-side as `paymentReference ? 'awaiting_payment' : 'draft'` (payment_status always `'pending'` regardless) — i.e., the source never actually reaches `paid`/`pending_approval`/`approved`/`completed` through any code path; those statuses are admin-only manual transitions (presumably via `/admin/bookings`, not yet inspected in Phase 0 — needs Phase-2 confirmation before building `BookingLifecycle`). Inserts into `bookings` as `anon` (RLS allows this), reads back `booking_number` via a follow-up SELECT (needed the anon-select RLS patch in migration `20260711112932` to work at all). Success state shows the booking number and links to `/student` and `/programs`.

---

## 7. Discrepancies vs. Task Brief

These are corrections to the task brief's SOURCE INVENTORY, based on what actually exists in the repo. Flagging now so Phase 2+ scope is accurate:

1. **5 extra tables exist that the brief's inventory omits:** `blog_categories`, `nav_items`, `team_members`, `partners`, `contact_submissions`. Recommendation: `blog_categories` and `nav_items` are load-bearing (blog/nav features break without them) and must be ported into `Cms`. `team_members`, `partners`, `contact_submissions` have RLS policies and (for contact_submissions) a live public write path, but **no admin UI in the source at all** — decide with the user whether to (a) port table+policy only with no UI (parity with source), or (b) also build the missing admin UI as a scope addition. Default recommendation: (a), since the brief's "no feature regressions" constraint only requires matching source, not fixing source gaps.
2. **Payments are far less built than the brief implies.** Source has zero M-Pesa/Stripe/Daraja integration code — "payment" is entirely static instructional UI plus a free-text reference field. Phase 6 ("M-Pesa Daraja STK push live") is new functionality requiring real credentials from the user, not a migration of existing logic.
3. **`audit_logs` has RLS policies but zero write call-sites.** Nothing in the source ever writes an audit row. The Phase 7 "Audit logs written via event listeners" work is new instrumentation, not a port.
4. **`/student/notifications` is a dead Navbar link (404 today).** Matches brief's "wire it up" instruction — just confirming it's a true gap, not a page we can reference for behavior. No existing UI to preserve for notifications; the target UI (bell + list + mark-read) will be designed fresh within the existing design system (navy/brand palette, card patterns from `/student` dashboard).
5. **The role redirect rule is a hard partition of all 8 roles**, not a soft admin/trainer/other split: `[admin, super_admin, content_manager, marketing, finance, support] → /admin`, `trainer → /trainer`, everything else → `/student`. This matches the brief's stated rule exactly, just documenting the precise source line for Phase 3 fidelity.
6. **Admin CRUD forms have no zod schemas to mirror** — Form Requests must be reverse-engineered from DB NOT NULL constraints + observed input `required`/type attributes, since "mirror the existing zod schemas field-for-field" only literally applies to the 4 schemas in §5.
7. **`bookings.status` never progresses past `draft`/`awaiting_payment` in any source code path** other than admin action. Confirmed from `app/admin/bookings/page.tsx`: the list view exposes exactly two guarded quick-actions — `pending_approval → approved` (stamps `approved_at`) or `pending_approval → rejected`, and `paid → pending_approval`. There is **no free-form status selector and no rejection-reason capture** in the list view; `admin_notes`/`rejection_reason`/`cancelled`/`completed` are presumably handled on the unread `/admin/bookings/[id]` detail page — **read that file in Phase 2 before finalizing `BookingLifecycle`**, since it likely contains the rest of the state machine (how `draft/awaiting_payment → paid` and `→ completed/cancelled` are triggered).

---

## 8. Design Tokens (`tailwind.config.ts` + will need `app/globals.css` CSS variables)

- `darkMode: 'class'`
- Custom color scales: `brand` (green, 50–950, base `#4CAF50`) and `navy` (dark blue, 50–950, base `#10204A`) — used extensively for admin sidebar (`bg-navy-950`), CTAs, buttons (`bg-brand-500 hover:bg-brand-600`).
- Semantic colors (`background`, `foreground`, `card`, `popover`, `primary`, `secondary`, `muted`, `accent`, `destructive`, `border`, `input`, `ring`, `chart-1..5`) all sourced from CSS custom properties (`hsl(var(--x))`) — **must copy `app/globals.css` verbatim in Phase 1**, not yet read in full; do so before Phase 1 completes.
- `borderRadius` driven by `--radius` var.
- `tailwindcss-animate` plugin + custom `accordion-down`/`accordion-up` keyframes (drives Radix Accordion — needs an Alpine equivalent transition in target).
- Font/typography scale not yet inspected — read `app/globals.css` and `app/layout.tsx` `<head>` in Phase 1 before porting the base layout.

---

## 9. Open Items Before Phase 1 Starts

- [ ] Read `app/globals.css` in full (CSS vars, base layer, custom utility classes like `.btn-primary`, `.btn-outline`, `.announcement-bar`, `.skeleton`, `.animate-fade-in` seen referenced but not yet sourced).
- [ ] Read `app/admin/bookings/page.tsx` to confirm the actual status-transition triggers for the `BookingLifecycle` service (see discrepancy §7.7).
- [ ] Confirm with user: scope decision on `team_members`/`partners`/`contact_submissions` (table-only parity vs. also building admin UI — see §7.1).
- [ ] Obtain (or confirm stub-only, deferred) M-Pesa Daraja sandbox/production credentials before Phase 6 — this is new integration work, budget accordingly.
- [ ] `seed_data.sql` has 17 `INSERT` statements across 210 lines — read in full during Phase 2 to build equivalent Laravel seeders/factories.

---

**End of Phase 0.** No application code has been written. Next step (Phase 1 — Skeleton) requires explicit go-ahead per the phase-by-phase execution rule.
