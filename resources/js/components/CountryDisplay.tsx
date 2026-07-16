import { countryFlag, countryName } from '@/lib/countryFlag';

interface Props {
    value: string;
    id?: string;
    placeholder?: string;
    invalid?: boolean;
}

/**
 * Read-only country field: shows a single flag + name. The value is populated
 * exclusively by the location autocomplete (never typed) so the stored country
 * always maps to a real geo record.
 */
export default function CountryDisplay({ value, id, placeholder, invalid }: Props) {
    return (
        <div
            id={id}
            aria-invalid={invalid || undefined}
            className="flex h-9 w-full items-center gap-2 rounded-4xl border border-input bg-muted/40 px-3.5 text-sm shadow-xs aria-invalid:border-destructive"
        >
            {value ? (
                <>
                    <span className="text-base leading-none">{countryFlag(value)}</span>
                    <span className="text-foreground">{countryName(value)}</span>
                </>
            ) : (
                <span className="text-muted-foreground">{placeholder}</span>
            )}
        </div>
    );
}
