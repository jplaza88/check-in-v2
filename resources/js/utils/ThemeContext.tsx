import { router } from '@inertiajs/react';
import type { ReactNode } from 'react';

import { createContext, useCallback, useContext, useEffect, useState } from 'react';

import { update } from '@/actions/App/Http/Controllers/AccountSettingsController';

export type Theme = 'light' | 'dark' | 'system';

/** What is actually painted, once `system` has been resolved against the OS. */
export type ResolvedTheme = 'light' | 'dark';

interface ThemeContextType {
    /** The driver's choice, which may be `system`. */
    currentTheme: Theme;
    /** The choice resolved to something paintable. */
    resolvedTheme: ResolvedTheme;
    changeCurrentTheme: (theme: Theme) => void;
}

const STORAGE_KEY = 'theme';
const DARK_QUERY = '(prefers-color-scheme: dark)';

const ThemeContext = createContext<ThemeContextType>({
    currentTheme: 'system',
    resolvedTheme: 'light',
    changeCurrentTheme: () => {},
});

function isTheme(value: unknown): value is Theme {
    return value === 'light' || value === 'dark' || value === 'system';
}

/**
 * Account -> localStorage -> system, the same order the blocking script in app.blade.php already
 * ran before first paint. Reading it back from the DOM rather than from Inertia props keeps this
 * provider usable outside the Inertia app tree, which is where app.tsx mounts it.
 */
function readTheme(): Theme {
    if (typeof document === 'undefined') {
        return 'system'; // SSR guard
    }

    const stored = document.documentElement.dataset.theme;

    if (isTheme(stored)) {
        return stored;
    }

    const cached = localStorage.getItem(STORAGE_KEY);

    return isTheme(cached) ? cached : 'system';
}

function prefersDark(): boolean {
    return typeof window !== 'undefined' && window.matchMedia(DARK_QUERY).matches;
}

function resolveTheme(theme: Theme): ResolvedTheme {
    if (theme === 'system') {
        return prefersDark() ? 'dark' : 'light';
    }

    return theme;
}

// Keeps the browser UI / iOS status-bar + safe-area tint matched to the active
// theme. Matches the page background (white / gray-900).
function setThemeColorMeta(color: string): void {
    let meta =
        document.querySelector<HTMLMetaElement>('meta[name="theme-color"]');

    if (!meta) {
        meta = document.createElement('meta');
        meta.name = 'theme-color';
        document.head.appendChild(meta);
    }

    meta.content = color;
}

export default function ThemeProvider({ children }: { children: ReactNode }) {
    const [theme, setTheme] = useState<Theme>(readTheme);
    const [resolved, setResolved] = useState<ResolvedTheme>(() =>
        resolveTheme(readTheme()),
    );

    const changeCurrentTheme = useCallback((newTheme: Theme) => {
        setTheme(newTheme);
        setResolved(resolveTheme(newTheme));
        localStorage.setItem(STORAGE_KEY, newTheme);

        // Blade sets this only for signed-in drivers; guests stay on localStorage alone.
        if (!document.documentElement.dataset.themePersist) {
            return;
        }

        // `only` has to be non-empty or Inertia omits the partial header entirely and reloads
        // every prop, which is far too much work for flipping a switch in the navbar.
        router.patch(
            update.url(),
            { theme: newTheme },
            { preserveState: true, preserveScroll: true, only: ['auth'] },
        );
    }, []);

    // Follow the OS live, but only while the driver is actually deferring to it.
    useEffect(() => {
        if (theme !== 'system') {
            return;
        }

        const query = window.matchMedia(DARK_QUERY);
        const sync = () => setResolved(query.matches ? 'dark' : 'light');

        sync();
        query.addEventListener('change', sync);

        return () => query.removeEventListener('change', sync);
    }, [theme]);

    useEffect(() => {
        document.documentElement.classList.add('**:transition-none!');

        if (resolved === 'light') {
            document.documentElement.classList.remove('dark');
            document.documentElement.style.colorScheme = 'light';
        } else {
            document.documentElement.classList.add('dark');
            document.documentElement.style.colorScheme = 'dark';
        }

        setThemeColorMeta(resolved === 'light' ? '#ffffff' : '#111827');

        const transitionTimeout = setTimeout(() => {
            document.documentElement.classList.remove('**:transition-none!');
        }, 1);

        return () => clearTimeout(transitionTimeout);
    }, [resolved]);

    return (
        <ThemeContext.Provider
            value={{ currentTheme: theme, resolvedTheme: resolved, changeCurrentTheme }}
        >
            {children}
        </ThemeContext.Provider>
    );
}

export const useThemeProvider = () => useContext(ThemeContext);
