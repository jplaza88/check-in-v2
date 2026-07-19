import { Link, usePage } from '@inertiajs/react';
import { UserPlus } from 'lucide-react';

import { Button } from '@/components/ui/button';

interface RegisterCtaTranslations {
    heading: string;
    body: string;
    createAccount: string;
    signIn: string;
}

interface PageProps {
    auth: { user: { id: number } | null };
    translations: { registerCta: RegisterCtaTranslations };
    [key: string]: unknown;
}

/**
 * Post-check-in / post-booking prompt to create an account. Shown on the
 * confirmation pages, where the short registration window is open. Hidden for
 * users who are already signed in.
 */
export default function RegisterCta() {
    const { auth, translations } = usePage<PageProps>().props;

    if (auth.user) {
        return null;
    }

    const t = translations.registerCta;

    return (
        <div className="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700/60 dark:bg-gray-800">
            <div className="px-5 py-5">
                <div className="flex items-start gap-3">
                    <div className="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-brand-green/10">
                        <UserPlus className="h-5 w-5 text-brand-green" />
                    </div>
                    <div>
                        <p className="text-sm font-semibold text-gray-900 dark:text-gray-100">
                            {t.heading}
                        </p>
                        <p className="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            {t.body}
                        </p>
                    </div>
                </div>
                <div className="mt-4 flex gap-3">
                    <Button asChild variant="outline" className="flex-1">
                        <Link href="/login">{t.signIn}</Link>
                    </Button>
                    <Button
                        asChild
                        className="flex-1 bg-brand-green text-white hover:bg-brand-green/90 focus-visible:ring-brand-green/50"
                    >
                        <Link href="/register">{t.createAccount}</Link>
                    </Button>
                </div>
            </div>
        </div>
    );
}
