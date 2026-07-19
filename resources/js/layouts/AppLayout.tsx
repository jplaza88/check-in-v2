import { Link } from '@inertiajs/react';
import type { ReactNode } from 'react';

import Logo from '@/components/Logo';
import ThemeToggle from '@/components/ThemeToggle';
import UserMenu from '@/components/UserMenu';

export default function AppLayout({ children }: { children: ReactNode }) {
    return (
        <div className="flex min-h-dvh flex-col bg-white dark:bg-gray-900">
            <header className="sticky top-0 z-10 border-b border-gray-200/80 bg-white/90 backdrop-blur dark:border-gray-700/60 dark:bg-gray-900/90">
                <div className="mx-auto flex max-w-2xl items-center justify-between px-5 py-3">
                    <Link href="/account" className="flex items-center">
                        <Logo size="sm" />
                    </Link>
                    <div className="flex items-center gap-3">
                        <ThemeToggle />
                        <span
                            aria-hidden
                            className="h-5 w-px bg-gray-200 dark:bg-gray-700/60"
                        />
                        <UserMenu />
                    </div>
                </div>
            </header>

            <main className="flex-1">{children}</main>
        </div>
    );
}
