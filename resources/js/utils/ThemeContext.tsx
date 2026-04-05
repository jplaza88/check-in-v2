import type {
    ReactNode
} from 'react';

import {
    createContext,
    useContext,
    useState,
    useEffect
} from 'react';

interface ThemeContextType {
    currentTheme: string;
    changeCurrentTheme: (theme: string) => void;
}

const ThemeContext = createContext<ThemeContextType>({
    currentTheme: 'light',
    changeCurrentTheme: () => {},
});

export default function ThemeProvider({ children }: { children: ReactNode }) {
    const [theme, setTheme] = useState<string>(() => {
        if (typeof window === 'undefined') {
            return 'light';
        } // SSR guard

        return localStorage.getItem('theme') || 'light';
    });

    const changeCurrentTheme = (newTheme: string) => {
        setTheme(newTheme);
        localStorage.setItem('theme', newTheme);
    };

    useEffect(() => {
        document.documentElement.classList.add('**:transition-none!');

        if (theme === 'light') {
            document.documentElement.classList.remove('dark');
            document.documentElement.style.colorScheme = 'light';
        } else {
            document.documentElement.classList.add('dark');
            document.documentElement.style.colorScheme = 'dark';
        }

        const transitionTimeout = setTimeout(() => {
            document.documentElement.classList.remove('**:transition-none!');
        }, 1);

        return () => clearTimeout(transitionTimeout);
    }, [theme]);

    return (
        <ThemeContext.Provider
            value={{ currentTheme: theme, changeCurrentTheme }}
        >
            {children}
        </ThemeContext.Provider>
    );
}

export const useThemeProvider = () => useContext(ThemeContext);
