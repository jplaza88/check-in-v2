import { Check } from 'lucide-react';

export default function SavedPill({ label }: { label: string }) {
    return (
        <span className="inline-flex items-center gap-1 text-sm font-medium text-brand-green">
            <Check className="h-4 w-4" />
            {label}
        </span>
    );
}
