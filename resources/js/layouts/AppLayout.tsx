import type { ReactNode } from 'react';

import Navbar from '@/components/Navbar';

export default function AppLayout({ children }: { children: ReactNode }) {
    return (
        <div className="flex min-h-dvh flex-col bg-white dark:bg-gray-900">
            <Navbar />

            <main className="flex-1">{children}</main>
        </div>
    );
}
