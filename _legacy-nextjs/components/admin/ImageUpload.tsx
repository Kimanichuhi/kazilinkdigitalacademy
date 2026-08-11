'use client';
import { useState } from 'react';
import { supabase } from '@/lib/supabase';
import { toast } from 'sonner';
import { Upload, Loader2, X } from 'lucide-react';

const MAX_IMAGE_BYTES = 5 * 1024 * 1024;

interface ImageUploadProps {
  value: string;
  onChange: (url: string) => void;
  folder: string;
  label?: string;
  shape?: 'circle' | 'square';
}

export function ImageUpload({ value, onChange, folder, label = 'Image', shape = 'square' }: ImageUploadProps) {
  const [uploading, setUploading] = useState(false);
  const inputId = `image-upload-${folder.replace(/\//g, '-')}`;

  async function handleChange(e: React.ChangeEvent<HTMLInputElement>) {
    const file = e.target.files?.[0];
    e.target.value = '';
    if (!file) return;
    if (!file.type.startsWith('image/')) { toast.error('Please select an image file'); return; }
    if (file.size > MAX_IMAGE_BYTES) { toast.error('Image must be under 5MB'); return; }

    setUploading(true);
    const ext = file.name.split('.').pop() || 'jpg';
    const path = `${folder}/${crypto.randomUUID()}.${ext}`;
    const { error } = await supabase.storage.from('media').upload(path, file, { cacheControl: '3600', upsert: false });
    if (error) {
      toast.error(error.message);
    } else {
      const { data } = supabase.storage.from('media').getPublicUrl(path);
      onChange(data.publicUrl);
    }
    setUploading(false);
  }

  const previewClass = shape === 'circle' ? 'w-16 h-16 rounded-full' : 'w-24 h-16 rounded-xl';

  return (
    <div>
      <label className="text-xs font-medium mb-1 block">{label}</label>
      <div className="flex items-center gap-3">
        {value ? (
          <img src={value} alt="" className={`${previewClass} object-cover flex-shrink-0 border border-border`} />
        ) : (
          <div className={`${previewClass} bg-muted flex items-center justify-center text-muted-foreground text-[10px] text-center flex-shrink-0`}>No image</div>
        )}
        <div className="flex items-center gap-2">
          <label htmlFor={inputId} className="cursor-pointer text-sm px-3 py-1.5 border border-border rounded-xl hover:bg-muted inline-flex items-center gap-2">
            {uploading ? <Loader2 className="w-3.5 h-3.5 animate-spin" /> : <Upload className="w-3.5 h-3.5" />}
            {uploading ? 'Uploading...' : value ? 'Replace' : 'Upload'}
          </label>
          <input id={inputId} type="file" accept="image/*" onChange={handleChange} disabled={uploading} className="hidden" />
          {value && (
            <button type="button" onClick={() => onChange('')} className="text-xs text-red-500 hover:underline inline-flex items-center gap-0.5">
              <X className="w-3 h-3" /> Remove
            </button>
          )}
        </div>
      </div>
    </div>
  );
}
