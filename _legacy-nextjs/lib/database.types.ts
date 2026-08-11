export type Json = string | number | boolean | null | { [key: string]: Json | undefined } | Json[];

export interface Database {
  public: {
    Tables: {
      profiles: {
        Row: {
          id: string;
          email: string | null;
          full_name: string | null;
          phone: string | null;
          avatar_url: string | null;
          role: 'super_admin' | 'admin' | 'trainer' | 'content_manager' | 'finance' | 'marketing' | 'support' | 'student';
          bio: string | null;
          is_active: boolean;
          created_at: string;
          updated_at: string;
        };
        Insert: Omit<Database['public']['Tables']['profiles']['Row'], 'created_at' | 'updated_at' | 'avatar_url' | 'bio' | 'is_active'> & { created_at?: string; updated_at?: string; avatar_url?: string | null; bio?: string | null; is_active?: boolean };
        Update: Partial<Database['public']['Tables']['profiles']['Insert']>;
        Relationships: [];
      };
      site_settings: {
        Row: { id: string; key: string; value: string | null; value_json: Json | null; category: string; label: string | null; description: string | null; updated_at: string };
        Insert: Omit<Database['public']['Tables']['site_settings']['Row'], 'id' | 'updated_at' | 'value_json' | 'label' | 'description'> & { id?: string; updated_at?: string; value_json?: Json | null; label?: string | null; description?: string | null };
        Update: Partial<Database['public']['Tables']['site_settings']['Insert']>;
        Relationships: [];
      };
      nav_menus: {
        Row: { id: string; name: string; location: string; is_active: boolean; created_at: string };
        Insert: Omit<Database['public']['Tables']['nav_menus']['Row'], 'id' | 'created_at'> & { id?: string; created_at?: string };
        Update: Partial<Database['public']['Tables']['nav_menus']['Insert']>;
        Relationships: [];
      };
      nav_items: {
        Row: { id: string; menu_id: string | null; parent_id: string | null; label: string; url: string | null; icon: string | null; target: string | null; order_index: number; is_active: boolean; badge: string | null; created_at: string };
        Insert: Omit<Database['public']['Tables']['nav_items']['Row'], 'id' | 'created_at'> & { id?: string; created_at?: string };
        Update: Partial<Database['public']['Tables']['nav_items']['Insert']>;
        Relationships: [];
      };
      program_categories: {
        Row: { id: string; name: string; slug: string; description: string | null; icon: string | null; color: string | null; order_index: number; is_active: boolean; created_at: string };
        Insert: Omit<Database['public']['Tables']['program_categories']['Row'], 'id' | 'created_at'> & { id?: string; created_at?: string };
        Update: Partial<Database['public']['Tables']['program_categories']['Insert']>;
        Relationships: [];
      };
      programs: {
        Row: {
          id: string; category_id: string | null; title: string; slug: string; subtitle: string | null;
          description: string | null; short_description: string | null; thumbnail_url: string | null;
          gallery_urls: string[] | null; duration_weeks: number | null; duration_label: string | null;
          level: string; delivery_mode: string; price: number | null; original_price: number | null;
          currency: string; is_featured: boolean; is_active: boolean; is_published: boolean;
          rating: number; review_count: number; enrollment_count: number; curriculum: Json | null;
          outcomes: string[] | null; requirements: string[] | null; seo_title: string | null;
          seo_description: string | null; seo_keywords: string | null; order_index: number;
          created_at: string; updated_at: string;
        };
        Insert: Omit<Database['public']['Tables']['programs']['Row'], 'id' | 'created_at' | 'updated_at' | 'gallery_urls' | 'is_active' | 'rating' | 'review_count' | 'enrollment_count' | 'curriculum' | 'outcomes' | 'requirements' | 'seo_title' | 'seo_description' | 'seo_keywords' | 'order_index'> & { id?: string; created_at?: string; updated_at?: string; gallery_urls?: string[] | null; is_active?: boolean; rating?: number; review_count?: number; enrollment_count?: number; curriculum?: Json | null; outcomes?: string[] | null; requirements?: string[] | null; seo_title?: string | null; seo_description?: string | null; seo_keywords?: string | null; order_index?: number };
        Update: Partial<Database['public']['Tables']['programs']['Insert']>;
        Relationships: [
          {
            foreignKeyName: 'programs_category_id_fkey';
            columns: ['category_id'];
            isOneToOne: false;
            referencedRelation: 'program_categories';
            referencedColumns: ['id'];
          }
        ];
      };
      trainers: {
        Row: { id: string; profile_id: string | null; full_name: string; title: string | null; bio: string | null; avatar_url: string | null; email: string | null; phone: string | null; specializations: string[] | null; social_links: Json | null; rating: number; review_count: number; is_featured: boolean; is_active: boolean; order_index: number; created_at: string; updated_at: string };
        Insert: Omit<Database['public']['Tables']['trainers']['Row'], 'id' | 'created_at' | 'updated_at'> & { id?: string; created_at?: string; updated_at?: string };
        Update: Partial<Database['public']['Tables']['trainers']['Insert']>;
        Relationships: [];
      };
      cohorts: {
        Row: { id: string; program_id: string; trainer_id: string | null; name: string; start_date: string; end_date: string | null; registration_deadline: string | null; total_seats: number; booked_seats: number; schedule_details: string | null; schedule_json: Json | null; venue: string | null; venue_address: string | null; online_link: string | null; online_platform: string | null; price: number | null; currency: string; status: string; is_featured: boolean; notes: string | null; created_at: string; updated_at: string };
        Insert: Omit<Database['public']['Tables']['cohorts']['Row'], 'id' | 'created_at' | 'updated_at'> & { id?: string; created_at?: string; updated_at?: string };
        Update: Partial<Database['public']['Tables']['cohorts']['Insert']>;
        Relationships: [
          {
            foreignKeyName: 'cohorts_program_id_fkey';
            columns: ['program_id'];
            isOneToOne: false;
            referencedRelation: 'programs';
            referencedColumns: ['id'];
          },
          {
            foreignKeyName: 'cohorts_trainer_id_fkey';
            columns: ['trainer_id'];
            isOneToOne: false;
            referencedRelation: 'trainers';
            referencedColumns: ['id'];
          }
        ];
      };
      bookings: {
        Row: {
          id: string; booking_number: string; user_id: string | null; program_id: string; cohort_id: string | null;
          full_name: string; email: string; phone: string; id_number: string | null; date_of_birth: string | null;
          gender: string | null; nationality: string | null; address: string | null; city: string | null; country: string;
          current_occupation: string | null; employer: string | null; education_level: string | null;
          payment_method: string | null; payment_reference: string | null; amount_paid: number; total_amount: number | null;
          currency: string; payment_status: string; status: string; referral_source: string | null;
          emergency_contact_name: string | null; emergency_contact_phone: string | null; special_requirements: string | null;
          documents_urls: string[] | null; admin_notes: string | null; rejection_reason: string | null;
          consent_given: boolean; confirmed_at: string | null; approved_at: string | null; approved_by: string | null;
          created_at: string; updated_at: string;
        };
        Insert: Omit<Database['public']['Tables']['bookings']['Row'], 'id' | 'booking_number' | 'created_at' | 'updated_at' | 'user_id' | 'amount_paid' | 'documents_urls' | 'admin_notes' | 'rejection_reason' | 'confirmed_at' | 'approved_at' | 'approved_by'> & { id?: string; booking_number?: string; created_at?: string; updated_at?: string; user_id?: string | null; amount_paid?: number; documents_urls?: string[] | null; admin_notes?: string | null; rejection_reason?: string | null; confirmed_at?: string | null; approved_at?: string | null; approved_by?: string | null };
        Update: Partial<Database['public']['Tables']['bookings']['Insert']>;
        Relationships: [
          {
            foreignKeyName: 'bookings_program_id_fkey';
            columns: ['program_id'];
            isOneToOne: false;
            referencedRelation: 'programs';
            referencedColumns: ['id'];
          },
          {
            foreignKeyName: 'bookings_cohort_id_fkey';
            columns: ['cohort_id'];
            isOneToOne: false;
            referencedRelation: 'cohorts';
            referencedColumns: ['id'];
          }
        ];
      };
      blog_categories: {
        Row: { id: string; name: string; slug: string; description: string | null; color: string | null; order_index: number; is_active: boolean; created_at: string };
        Insert: Omit<Database['public']['Tables']['blog_categories']['Row'], 'id' | 'created_at'> & { id?: string; created_at?: string };
        Update: Partial<Database['public']['Tables']['blog_categories']['Insert']>;
        Relationships: [];
      };
      blog_posts: {
        Row: { id: string; category_id: string | null; author_id: string | null; title: string; slug: string; excerpt: string | null; content: string | null; thumbnail_url: string | null; tags: string[] | null; is_featured: boolean; is_published: boolean; published_at: string | null; view_count: number; seo_title: string | null; seo_description: string | null; seo_keywords: string | null; read_time_minutes: number | null; created_at: string; updated_at: string };
        Insert: Omit<Database['public']['Tables']['blog_posts']['Row'], 'id' | 'created_at' | 'updated_at'> & { id?: string; created_at?: string; updated_at?: string };
        Update: Partial<Database['public']['Tables']['blog_posts']['Insert']>;
        Relationships: [];
      };
      testimonials: {
        Row: { id: string; program_id: string | null; student_name: string; student_title: string | null; student_avatar_url: string | null; content: string; rating: number | null; income_before: string | null; income_after: string | null; video_url: string | null; is_featured: boolean; is_published: boolean; order_index: number; created_at: string };
        Insert: Omit<Database['public']['Tables']['testimonials']['Row'], 'id' | 'created_at' | 'program_id' | 'video_url'> & { id?: string; created_at?: string; program_id?: string | null; video_url?: string | null };
        Update: Partial<Database['public']['Tables']['testimonials']['Insert']>;
        Relationships: [];
      };
      faqs: {
        Row: { id: string; category: string; question: string; answer: string; order_index: number; is_published: boolean; created_at: string; updated_at: string };
        Insert: Omit<Database['public']['Tables']['faqs']['Row'], 'id' | 'created_at' | 'updated_at'> & { id?: string; created_at?: string; updated_at?: string };
        Update: Partial<Database['public']['Tables']['faqs']['Insert']>;
        Relationships: [];
      };
      advertisements: {
        Row: { id: string; campaign_name: string; title: string | null; subtitle: string | null; description: string | null; type: string; placement: string[]; desktop_image_url: string | null; mobile_image_url: string | null; video_url: string | null; cta_text: string | null; cta_link: string | null; button_style: string | null; background_color: string | null; overlay_color: string | null; animation: string | null; priority: number; target_audience: Json | null; status: string; publish_date: string | null; expiry_date: string | null; view_count: number; click_count: number; created_at: string; updated_at: string };
        Insert: Omit<Database['public']['Tables']['advertisements']['Row'], 'id' | 'created_at' | 'updated_at'> & { id?: string; created_at?: string; updated_at?: string };
        Update: Partial<Database['public']['Tables']['advertisements']['Insert']>;
        Relationships: [];
      };
      ctas: {
        Row: { id: string; name: string; title: string; subtitle: string | null; description: string | null; background_color: string | null; background_image_url: string | null; button_one_text: string | null; button_one_link: string | null; button_one_style: string | null; button_two_text: string | null; button_two_link: string | null; button_two_style: string | null; placement: string[] | null; priority: number; is_active: boolean; publish_date: string | null; expiry_date: string | null; created_at: string; updated_at: string };
        Insert: Omit<Database['public']['Tables']['ctas']['Row'], 'id' | 'created_at' | 'updated_at'> & { id?: string; created_at?: string; updated_at?: string };
        Update: Partial<Database['public']['Tables']['ctas']['Insert']>;
        Relationships: [];
      };
      pages: {
        Row: { id: string; title: string; slug: string; description: string | null; is_published: boolean; seo_title: string | null; seo_description: string | null; seo_keywords: string | null; og_image_url: string | null; created_at: string; updated_at: string };
        Insert: Omit<Database['public']['Tables']['pages']['Row'], 'id' | 'created_at' | 'updated_at'> & { id?: string; created_at?: string; updated_at?: string };
        Update: Partial<Database['public']['Tables']['pages']['Insert']>;
        Relationships: [];
      };
      page_blocks: {
        Row: { id: string; page_id: string; type: string; content: Json; order_index: number; is_active: boolean; created_at: string; updated_at: string };
        Insert: Omit<Database['public']['Tables']['page_blocks']['Row'], 'id' | 'created_at' | 'updated_at'> & { id?: string; created_at?: string; updated_at?: string };
        Update: Partial<Database['public']['Tables']['page_blocks']['Insert']>;
        Relationships: [];
      };
      resources: {
        Row: { id: string; program_id: string | null; title: string; description: string | null; type: string; file_url: string | null; thumbnail_url: string | null; is_free: boolean; is_published: boolean; download_count: number; tags: string[] | null; order_index: number; created_at: string; updated_at: string };
        Insert: Omit<Database['public']['Tables']['resources']['Row'], 'id' | 'created_at' | 'updated_at'> & { id?: string; created_at?: string; updated_at?: string };
        Update: Partial<Database['public']['Tables']['resources']['Insert']>;
        Relationships: [];
      };
      team_members: {
        Row: { id: string; full_name: string; title: string; bio: string | null; avatar_url: string | null; email: string | null; social_links: Json | null; department: string | null; is_featured: boolean; is_active: boolean; order_index: number; created_at: string };
        Insert: Omit<Database['public']['Tables']['team_members']['Row'], 'id' | 'created_at'> & { id?: string; created_at?: string };
        Update: Partial<Database['public']['Tables']['team_members']['Insert']>;
        Relationships: [];
      };
      partners: {
        Row: { id: string; name: string; logo_url: string | null; website_url: string | null; description: string | null; is_active: boolean; order_index: number; created_at: string };
        Insert: Omit<Database['public']['Tables']['partners']['Row'], 'id' | 'created_at'> & { id?: string; created_at?: string };
        Update: Partial<Database['public']['Tables']['partners']['Insert']>;
        Relationships: [];
      };
      contact_submissions: {
        Row: { id: string; full_name: string; email: string; phone: string | null; subject: string | null; message: string; status: string; admin_notes: string | null; created_at: string; updated_at: string };
        Insert: Omit<Database['public']['Tables']['contact_submissions']['Row'], 'id' | 'created_at' | 'updated_at' | 'status' | 'admin_notes'> & { id?: string; created_at?: string; updated_at?: string; status?: string; admin_notes?: string | null };
        Update: Partial<Database['public']['Tables']['contact_submissions']['Insert']>;
        Relationships: [];
      };
      statistics: {
        Row: { id: string; label: string; value: string; icon: string | null; description: string | null; order_index: number; is_active: boolean; created_at: string; key: string | null };
        Insert: Omit<Database['public']['Tables']['statistics']['Row'], 'id' | 'created_at' | 'key'> & { id?: string; created_at?: string; key?: string | null };
        Update: Partial<Database['public']['Tables']['statistics']['Insert']>;
        Relationships: [];
      };
      notifications: {
        Row: { id: string; user_id: string | null; title: string; message: string; type: string; link: string | null; is_read: boolean; created_at: string };
        Insert: Omit<Database['public']['Tables']['notifications']['Row'], 'id' | 'created_at'> & { id?: string; created_at?: string };
        Update: Partial<Database['public']['Tables']['notifications']['Insert']>;
        Relationships: [];
      };
      audit_logs: {
        Row: { id: string; user_id: string | null; action: string; resource_type: string; resource_id: string | null; old_values: Json | null; new_values: Json | null; ip_address: string | null; user_agent: string | null; created_at: string };
        Insert: Omit<Database['public']['Tables']['audit_logs']['Row'], 'id' | 'created_at'> & { id?: string; created_at?: string };
        Update: Partial<Database['public']['Tables']['audit_logs']['Insert']>;
        Relationships: [];
      };
    };
    Views: {};
    Functions: {};
    Enums: {};
  };
}

// Convenience row types
export type Profile = Database['public']['Tables']['profiles']['Row'];
export type SiteSetting = Database['public']['Tables']['site_settings']['Row'];
export type SiteSettingKV = Pick<SiteSetting, 'key' | 'value'>;
export type NavMenu = Database['public']['Tables']['nav_menus']['Row'];
export type NavItem = Database['public']['Tables']['nav_items']['Row'];
export type ProgramCategory = Database['public']['Tables']['program_categories']['Row'];
export type Program = Database['public']['Tables']['programs']['Row'];
export type Trainer = Database['public']['Tables']['trainers']['Row'];
export type Cohort = Database['public']['Tables']['cohorts']['Row'];
export type Booking = Database['public']['Tables']['bookings']['Row'];
export type BlogCategory = Database['public']['Tables']['blog_categories']['Row'];
export type BlogPost = Database['public']['Tables']['blog_posts']['Row'];
export type Testimonial = Database['public']['Tables']['testimonials']['Row'];
export type FAQ = Database['public']['Tables']['faqs']['Row'];
export type Advertisement = Database['public']['Tables']['advertisements']['Row'];
export type CTA = Database['public']['Tables']['ctas']['Row'];
export type Page = Database['public']['Tables']['pages']['Row'];
export type PageBlock = Database['public']['Tables']['page_blocks']['Row'];
export type Resource = Database['public']['Tables']['resources']['Row'];
export type TeamMember = Database['public']['Tables']['team_members']['Row'];
export type Partner = Database['public']['Tables']['partners']['Row'];
export type ContactSubmission = Database['public']['Tables']['contact_submissions']['Row'];
export type Statistic = Database['public']['Tables']['statistics']['Row'];
export type Notification = Database['public']['Tables']['notifications']['Row'];
export type AuditLog = Database['public']['Tables']['audit_logs']['Row'];
