import { useForm, usePage } from '@inertiajs/react';
import { FileText, Loader2, Mail } from 'lucide-react';

import SavedPill from '@/components/SavedPill';

interface PageProps {
    auth: { user: { email: string } };
    [key: string]: unknown;
}

export default function RecordActions({
    pdfUrl,
    emailUrl,
    labels,
}: {
    pdfUrl: string;
    emailUrl: string;
    labels: { viewPdf: string; emailCopy: string; emailSent: string };
}) {
    const { auth } = usePage<PageProps>().props;
    const form = useForm({});

    return (
        <div className="flex flex-col gap-3 sm:flex-row">
            {/* A real navigation, not an Inertia visit: Inertia cannot handle a
                binary response. Served inline, so this opens in the phone's PDF
                viewer rather than vanishing into Downloads. */}
            <a
                href={pdfUrl}
                target="_blank"
                rel="noopener"
                className="flex min-h-11 flex-1 items-center justify-center gap-2 rounded-4xl bg-brand-green px-5 text-sm font-semibold text-white shadow-sm shadow-brand-green/25 transition-colors hover:bg-brand-green/90"
            >
                <FileText className="h-4 w-4" />
                {labels.viewPdf}
            </a>

            {form.recentlySuccessful ? (
                <div className="flex min-h-11 flex-1 items-center justify-center">
                    <SavedPill
                        label={labels.emailSent.replace(
                            ':email',
                            auth.user.email,
                        )}
                    />
                </div>
            ) : (
                <button
                    type="button"
                    disabled={form.processing}
                    onClick={() =>
                        form.post(emailUrl, { preserveScroll: true })
                    }
                    className="flex min-h-11 flex-1 cursor-pointer items-center justify-center gap-2 rounded-4xl border border-gray-200 bg-white px-5 text-sm font-semibold text-brand-grey transition-colors hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-60 dark:border-gray-700/60 dark:bg-gray-800/50 dark:text-gray-200 dark:hover:bg-gray-800"
                >
                    {form.processing ? (
                        <Loader2 className="h-4 w-4 animate-spin" />
                    ) : (
                        <Mail className="h-4 w-4" />
                    )}
                    {labels.emailCopy}
                </button>
            )}
        </div>
    );
}
