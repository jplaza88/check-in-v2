import { useRef, useState } from 'react';

import GeoController from '@/actions/App/Http/Controllers/GeoController';
import { Input } from '@/components/ui/input';
import { countryFlag } from '@/lib/countryFlag';

export interface GeoResult {
    city?: string;
    state: string;
    stateCode: string | null;
    country: string;
    countryCode: string;
}

interface Props {
    /** `city` searches cities (fills city + state + country); `state` searches states (fills state + country). */
    level: 'city' | 'state';
    value: string;
    onChange: (value: string) => void;
    onSelect: (result: GeoResult) => void;
    onBlur?: () => void;
    id?: string;
    placeholder?: string;
    maxLength?: number;
    invalid?: boolean;
    className?: string;
}

const MIN_CHARS = 3;
const DEBOUNCE_MS = 300;

export default function LocationAutocomplete({
    level,
    value,
    onChange,
    onSelect,
    onBlur,
    id,
    placeholder,
    maxLength,
    invalid,
    className,
}: Props) {
    const [results, setResults] = useState<GeoResult[]>([]);
    const [open, setOpen] = useState(false);
    const [loading, setLoading] = useState(false);
    const [activeIndex, setActiveIndex] = useState(-1);

    const timerRef = useRef<ReturnType<typeof setTimeout> | null>(null);
    const abortRef = useRef<AbortController | null>(null);

    const search = (raw: string) => {
        const query = raw.trim();

        if (timerRef.current) {
            clearTimeout(timerRef.current);
        }

        if (query.length < MIN_CHARS) {
            setResults([]);
            setOpen(false);

            return;
        }

        timerRef.current = setTimeout(() => {
            abortRef.current?.abort();
            const controller = new AbortController();
            abortRef.current = controller;
            setLoading(true);

            const url = (
                level === 'city' ? GeoController.cities : GeoController.states
            ).url({ query: { q: query } });

            fetch(url, {
                signal: controller.signal,
                headers: { Accept: 'application/json' },
            })
                .then((res) => res.json())
                .then((data: GeoResult[]) => {
                    setResults(data);
                    setOpen(true);
                    setActiveIndex(-1);
                })
                .catch(() => {
                    /* aborted or network error — leave the field usable as free text */
                })
                .finally(() => setLoading(false));
        }, DEBOUNCE_MS);
    };

    const choose = (result: GeoResult) => {
        onSelect(result);
        setOpen(false);
        setResults([]);

        if (timerRef.current) {
            clearTimeout(timerRef.current);
        }
    };

    const handleKeyDown = (event: React.KeyboardEvent) => {
        if (!open || results.length === 0) {
            return;
        }

        if (event.key === 'ArrowDown') {
            event.preventDefault();
            setActiveIndex((i) => Math.min(i + 1, results.length - 1));
        } else if (event.key === 'ArrowUp') {
            event.preventDefault();
            setActiveIndex((i) => Math.max(i - 1, 0));
        } else if (event.key === 'Enter' && activeIndex >= 0) {
            event.preventDefault();
            choose(results[activeIndex]);
        } else if (event.key === 'Escape') {
            setOpen(false);
        }
    };

    return (
        <div className="relative">
            <Input
                id={id}
                type="text"
                role="combobox"
                aria-expanded={open}
                aria-autocomplete="list"
                autoComplete="off"
                value={value}
                maxLength={maxLength}
                placeholder={placeholder}
                aria-invalid={invalid}
                className={className}
                onChange={(event) => {
                    onChange(event.target.value);
                    search(event.target.value);
                }}
                onFocus={() => {
                    if (results.length > 0) {
                        setOpen(true);
                    }
                }}
                onBlur={() => {
                    // Delay so a click on a suggestion registers before we close.
                    setTimeout(() => setOpen(false), 120);
                    onBlur?.();
                }}
                onKeyDown={handleKeyDown}
            />

            {loading && (
                <span
                    aria-hidden
                    className="absolute top-1/2 right-3 size-4 -translate-y-1/2 animate-spin rounded-full border-2 border-brand-green/30 border-t-brand-green"
                />
            )}

            {open && results.length > 0 && (
                <ul
                    role="listbox"
                    className="absolute z-20 mt-1 max-h-64 w-full overflow-auto rounded-2xl border border-gray-200 bg-white py-1 shadow-lg dark:border-gray-700 dark:bg-gray-800"
                >
                    {results.map((result, index) => (
                        <li
                            key={`${result.city ?? ''}-${result.state}-${result.countryCode}-${index}`}
                            role="option"
                            aria-selected={index === activeIndex}
                            onMouseDown={(event) => {
                                event.preventDefault();
                                choose(result);
                            }}
                            onMouseEnter={() => setActiveIndex(index)}
                            className={`flex cursor-pointer items-center gap-2.5 px-3 py-2 text-sm ${
                                index === activeIndex
                                    ? 'bg-brand-green/10 text-brand-grey dark:text-gray-100'
                                    : 'text-gray-700 dark:text-gray-200'
                            }`}
                        >
                            <span className="text-base leading-none">
                                {countryFlag(result.countryCode)}
                            </span>
                            <span className="truncate">
                                {result.city ? (
                                    <>
                                        <span className="font-medium">{result.city}</span>, {result.state}
                                    </>
                                ) : (
                                    <span className="font-medium">{result.state}</span>
                                )}
                                <span className="text-gray-400 dark:text-gray-500">
                                    {' · '}
                                    {result.country}
                                </span>
                            </span>
                        </li>
                    ))}
                </ul>
            )}
        </div>
    );
}
