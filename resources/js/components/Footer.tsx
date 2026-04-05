import { Link, usePage } from '@inertiajs/react';
import Logo from '@/components/Logo';

interface PublicNavigation {
    checkIn: string;
    appointment: string;
}

interface Translations {
    publicNavigation: PublicNavigation;
}

interface PageProps {
    translations: Translations;
    [key: string]: unknown;
}

export default function Footer() {
    const appName = import.meta.env.VITE_APP_NAME;

    const { translations } = usePage<PageProps>().props;

    return (
        <footer className="w-full border-t border-gray-200/80 bg-white/95 dark:border-gray-700/50 dark:bg-gray-900/95">
            <div className="mx-auto flex h-14 max-w-7xl items-center justify-between px-6 text-sm">
                <Logo size="sm" />

                <div className="flex items-center gap-6 text-gray-400 dark:text-gray-500">
                    <Link
                        href="#"
                        className="transition-colors hover:text-brand-green"
                    >
                        {translations.publicNavigation.checkIn}
                    </Link>
                    <Link
                        href="#"
                        className="transition-colors hover:text-brand-green"
                    >
                        { translations.publicNavigation.appointment }
                    </Link>
                </div>

                <span className="text-xs text-gray-400 dark:text-gray-500">
                    © {new Date().getFullYear()} {appName} All Rights Reserved.
                </span>
            </div>
        </footer>
    );
}
