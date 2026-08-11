import './globals.css';
import type { Metadata } from 'next';
import { Inter } from 'next/font/google';
import { ThemeProvider } from '@/components/providers/ThemeProvider';
import { AuthProvider } from '@/components/providers/AuthProvider';
import { Toaster } from '@/components/ui/sonner';

const inter = Inter({
  subsets: ['latin'],
  variable: '--font-inter',
  display: 'swap',
});

export const metadata: Metadata = {
  title: { default: 'Kazilink Digital Academy', template: '%s | Kazilink Digital Academy' },
  description: "Kenya's #1 online income skills academy. Learn practical digital skills and start earning from anywhere.",
  keywords: ['online training', 'digital skills', 'freelancing', 'ecommerce', 'Kenya', 'earn online'],
  openGraph: {
    type: 'website',
    locale: 'en_KE',
    url: 'https://kazilink.academy',
    siteName: 'Kazilink Digital Academy',
    images: [{ url: 'https://images.pexels.com/photos/3184338/pexels-photo-3184338.jpeg?auto=compress&cs=tinysrgb&w=1200' }],
  },
  twitter: { card: 'summary_large_image' },
  icons: { icon: '/favicon.jpeg', shortcut: '/favicon.jpeg', apple: '/favicon.jpeg' },
};

export default function RootLayout({ children }: { children: React.ReactNode }) {
  return (
    <html lang="en" suppressHydrationWarning>
      <body className={`${inter.variable} font-sans`}>
        <ThemeProvider attribute="class" defaultTheme="light" enableSystem>
          <AuthProvider>
            {children}
            <Toaster richColors position="top-right" />
          </AuthProvider>
        </ThemeProvider>
      </body>
    </html>
  );
}
