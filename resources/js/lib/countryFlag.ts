export interface CountryOption {
    code: string;
    name: string;
}

/** Countries seeded in the geo database. */
export const SUPPORTED_COUNTRIES: CountryOption[] = [
    { code: 'US', name: 'United States' },
    { code: 'CA', name: 'Canada' },
    { code: 'MX', name: 'Mexico' },
];

/**
 * Turn an ISO 3166-1 alpha-2 code into its flag emoji using Unicode regional
 * indicator symbols (e.g. "US" -> 🇺🇸). Returns "" for anything that isn't a
 * two-letter code.
 */
export function countryFlag(iso2: string | null | undefined): string {
    if (!iso2 || !/^[a-z]{2}$/i.test(iso2)) {
        return '';
    }

    return iso2
        .toUpperCase()
        .replace(/./g, (char) => String.fromCodePoint(127397 + char.charCodeAt(0)));
}

/** Human-readable name for a supported ISO-2 code, falling back to the code. */
export function countryName(iso2: string | null | undefined): string {
    if (!iso2) {
        return '';
    }

    return SUPPORTED_COUNTRIES.find((c) => c.code === iso2.toUpperCase())?.name ?? iso2;
}
