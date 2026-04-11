import { Link, router, usePage } from '@inertiajs/react';
import React, { useState } from 'react';
import LocaleController from '@/actions/App/Http/Controllers/LocaleController';
import Logo from '@/components/Logo';
import NavbarLink from '@/components/NavbarLink';
import ThemeToggle from '@/components/ThemeToggle';
import appointment from '@/routes/appointment';
import checkIn from "@/routes/checkIn";

interface PublicNavigation {
    checkIn: string;
    appointment: string;
    login: string;
}

interface Translations {
    publicNavigation: PublicNavigation;
}

interface PageProps {
    currentLocale: string;
    localesLabels: Record<string, string>;
    translations: Translations;
    [key: string]: unknown;
}

export default function Navbar() {
    const {
        currentLocale,
        localesLabels = {},
        translations,
    } = usePage<PageProps>().props;

    const [isOpen, setIsOpen] = useState(false);

    const nav = translations.publicNavigation;

    function switchLocale(code: string) {
        if (code === currentLocale) {
            return;
        }

        const { url } = LocaleController.post({ locale: code });
        router.post(url, {}, { preserveScroll: true, preserveState: false });
    }

    return (
        <nav className="sticky top-0 z-50 w-full border-b border-gray-200/80 bg-white/95 backdrop-blur-sm dark:border-gray-700/50 dark:bg-gray-900/95">
            <div className="mx-auto flex h-15 max-w-7xl items-center justify-between px-6">
                {/* Left: Logo + divider + links */}
                <div className="flex items-center gap-8">
                    <Link href="/" className="flex items-center">
                        <Logo />
                    </Link>

                    <div className="hidden h-5 w-px bg-gray-200 lg:block dark:bg-gray-700" />

                    {/* Desktop links */}
                    <div className="hidden items-center gap-6 text-sm lg:flex">
                        <NavbarLink
                            href={checkIn.selectLocation().url}
                            name={nav.checkIn}
                            activeRoute="checkIn.selectLocation"
                        />
                        <NavbarLink
                            href={appointment.selectLocation().url}
                            name={nav.appointment}
                            activeRoute="appointment.selectLocation"
                        />
                        <NavbarLink
                            href="#"
                            name={nav.login}
                            activeRoute="login"
                        />
                    </div>
                </div>

                {/* Right: Theme + Lang + Hamburger */}
                <div className="flex items-center gap-4">
                    {/* Language Switch */}
                    <div className="flex items-center gap-3">
                        {Object.entries(localesLabels).map(([code, label]) => (
                            <button
                                key={code}
                                type="button"
                                onClick={() => switchLocale(code)}
                                aria-label={`Switch to ${label}`}
                                aria-pressed={currentLocale === code}
                                className={`group relative cursor-pointer text-xs font-medium tracking-wide transition-colors duration-300 ${
                                    currentLocale === code
                                        ? 'font-semibold text-brand-grey-link dark:text-gray-200'
                                        : 'text-brand-grey-link dark:text-gray-200'
                                }`}
                            >
                                {code.toUpperCase()}
                                <span
                                    className={`absolute -bottom-1.5 left-0 h-0.5 bg-brand-green transition-all duration-300 ${
                                        currentLocale === code
                                            ? 'w-full'
                                            : 'w-0 group-hover:w-full'
                                    }`}
                                />
                            </button>
                        ))}
                    </div>

                    {/* Theme toggle — moved to the right of lang */}
                    <ThemeToggle />

                    {/* Animated hamburger */}
                    <button
                        onClick={() => setIsOpen(!isOpen)}
                        className="flex h-8 w-8 cursor-pointer items-center justify-center rounded-md text-brand-grey-link hover:bg-gray-100 focus:outline-none lg:hidden dark:hover:bg-gray-800"
                        aria-label="Toggle navigation menu"
                    >
                        <div className="relative h-4 w-5">
                            <span
                                className={`absolute left-0 h-0.5 w-full bg-current transition-all duration-300 ease-in-out ${isOpen ? 'top-1.5 rotate-45' : 'top-0'}`}
                            />
                            <span
                                className={`absolute top-1.5 left-0 h-0.5 w-full bg-current transition-all duration-300 ease-in-out ${isOpen ? 'translate-x-2 opacity-0' : 'opacity-100'}`}
                            />
                            <span
                                className={`w-current absolute left-0 h-0.5 bg-current transition-all duration-300 ease-in-out ${isOpen ? 'top-1.5 w-full -rotate-45' : 'top-3 w-full'}`}
                            />
                        </div>
                    </button>
                </div>
            </div>

            {/* Mobile menu */}
            {isOpen && (
                <div className="flex flex-col border-t border-gray-100 px-6 py-4 dark:border-gray-800">
                    <NavbarLink
                        href={checkIn.selectLocation().url}
                        name={nav.checkIn}
                        activeRoute="checkIn.selectLocation"
                        variant="mobile"
                    />
                    <NavbarLink
                        href={appointment.selectLocation().url}
                        name={nav.appointment}
                        activeRoute="appointment.selectLocation"
                        variant="mobile"
                    />
                    <NavbarLink
                        href="/login"
                        name={nav.login}
                        activeRoute="login"
                        variant="mobile"
                    />
                </div>
            )}
        </nav>
    );
}
