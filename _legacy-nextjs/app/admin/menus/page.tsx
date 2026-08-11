'use client';
export default function AdminMenusPage() {
  return (
    <div className="p-6">
      <h1 className="text-2xl font-black mb-2">Navigation Menus</h1>
      <p className="text-muted-foreground mb-6">Manage your site navigation structure.</p>
      <div className="bg-card border border-border rounded-2xl p-10 text-center text-muted-foreground">
        <p className="text-lg font-medium">Navigation Manager</p>
        <p className="text-sm mt-2">Manage header, footer, and dropdown menus from here. Coming soon with drag-and-drop reordering.</p>
      </div>
    </div>
  );
}
