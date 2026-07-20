import { Head, useForm, usePage } from '@inertiajs/react';
import type { FormEvent } from 'react';

import Logo from '@/components/Logo';
import ThemeToggle from '@/components/ThemeToggle';
import { Button } from '@/components/ui/button';
import { Field, FieldError, FieldLabel } from '@/components/ui/field';
import { Input } from '@/components/ui/input';

const appName = (import.meta.env.VITE_APP_NAME as string) ?? '';

interface LoginTranslations {
    eyebrow: string;
    title: string;
    subtitle: string;
    emailLabel: string;
    emailPlaceholder: string;
    passwordLabel: string;
    passwordPlaceholder: string;
    rememberMe: string;
    forgotPassword: string;
    signIn: string;
    tagline: string;
    taglineSub: string;
    passwordResetSuccess: string;
}

interface PageProps {
    translations: { login: LoginTranslations };
    status?: string | null;
    [key: string]: unknown;
}

export default function Login() {
    const { translations, status } = usePage<PageProps>().props;
    const t = translations.login;

    const { data, setData, post, processing, errors, reset } = useForm({
        email: '',
        password: '',
        remember: false,
    });

    const submit = (event: FormEvent) => {
        event.preventDefault();
        post('/login', { onFinish: () => reset('password') });
    };

    return (
        <>
            <Head title={t.title} />

            <div className="flex min-h-dvh bg-white dark:bg-gray-900">
                {/* ── Form side ──────────────────────────────────────────── */}
                <div className="relative flex w-full flex-col justify-center px-6 py-12 sm:px-12 lg:w-1/2 lg:px-16 xl:px-28">
                    {/* Decorative glow */}
                    <div
                        aria-hidden
                        className="pointer-events-none absolute -top-24 -left-24 h-96 w-96 rounded-full bg-brand-green/10 blur-3xl dark:bg-brand-green/5"
                    />

                    <div className="absolute top-5 right-5">
                        <ThemeToggle />
                    </div>

                    <div className="relative mx-auto w-full max-w-sm">
                        <Logo size="md" />

                        <p className="mt-10 text-xs font-bold tracking-widest text-brand-green uppercase">
                            {t.eyebrow}
                        </p>
                        <h1 className="mt-2 text-3xl font-bold tracking-tight text-brand-grey dark:text-gray-50">
                            {t.title}
                        </h1>
                        <p className="mt-2 text-sm text-gray-500 dark:text-gray-400">
                            {t.subtitle}
                        </p>

                        {status ? (
                            <div className="mt-6 rounded-2xl border border-brand-green/30 bg-brand-green/10 px-4 py-3 text-sm font-medium text-brand-green">
                                {t.passwordResetSuccess}
                            </div>
                        ) : null}

                        <form onSubmit={submit} className="mt-8 space-y-5">
                            <Field data-invalid={!!errors.email || undefined}>
                                <FieldLabel htmlFor="email">
                                    {t.emailLabel}
                                </FieldLabel>
                                <Input
                                    id="email"
                                    name="email"
                                    type="email"
                                    autoComplete="email"
                                    autoFocus
                                    value={data.email}
                                    aria-invalid={!!errors.email || undefined}
                                    placeholder={t.emailPlaceholder}
                                    onChange={(e) =>
                                        setData('email', e.target.value)
                                    }
                                />
                                <FieldError>{errors.email}</FieldError>
                            </Field>

                            <Field data-invalid={!!errors.password || undefined}>
                                <FieldLabel htmlFor="password">
                                    {t.passwordLabel}
                                </FieldLabel>
                                <Input
                                    id="password"
                                    name="password"
                                    type="password"
                                    autoComplete="current-password"
                                    value={data.password}
                                    aria-invalid={!!errors.password || undefined}
                                    placeholder={t.passwordPlaceholder}
                                    onChange={(e) =>
                                        setData('password', e.target.value)
                                    }
                                />
                                <FieldError>{errors.password}</FieldError>
                            </Field>

                            <div className="flex items-center justify-between">
                                <label className="flex cursor-pointer items-center gap-2 text-sm text-gray-600 select-none dark:text-gray-300">
                                    <input
                                        type="checkbox"
                                        checked={data.remember}
                                        onChange={(e) =>
                                            setData('remember', e.target.checked)
                                        }
                                        className="size-4 rounded border-input accent-brand-green"
                                    />
                                    {t.rememberMe}
                                </label>
                                <a
                                    href="/forgot-password"
                                    className="text-sm font-medium text-brand-green transition-opacity hover:opacity-80"
                                >
                                    {t.forgotPassword}
                                </a>
                            </div>

                            <Button
                                type="submit"
                                disabled={processing}
                                className="h-11 w-full rounded-4xl bg-brand-green text-sm font-semibold text-white shadow-sm shadow-brand-green/30 transition-colors hover:bg-brand-green/90 focus-visible:ring-brand-green/50"
                            >
                                {t.signIn}
                            </Button>
                        </form>
                    </div>

                    <div className="relative mx-auto mt-12 w-full max-w-sm">
                        <p className="text-xs text-gray-400 dark:text-gray-500">
                            © {new Date().getFullYear()} {appName}
                        </p>
                    </div>
                </div>

                {/* ── Visual side ────────────────────────────────────────── */}
                <div className="relative hidden overflow-hidden bg-side-image bg-cover bg-center lg:block lg:w-1/2">
                    <div className="absolute inset-0 bg-gradient-to-tr from-brand-green/95 via-brand-green/55 to-brand-grey/50" />

                    {/* Soft glow accents */}
                    <div
                        aria-hidden
                        className="pointer-events-none absolute -top-20 -right-20 h-80 w-80 rounded-full bg-white/15 blur-3xl"
                    />

                    <div className="relative flex h-full flex-col justify-end p-12 xl:p-16">
                        <span className="text-xs font-bold tracking-[0.2em] text-white/80 uppercase">
                            {appName}
                        </span>
                        <h2 className="mt-4 max-w-md text-4xl leading-tight font-bold text-white xl:text-5xl">
                            {t.tagline}
                        </h2>
                        <p className="mt-4 max-w-sm text-sm leading-relaxed text-white/85">
                            {t.taglineSub}
                        </p>
                    </div>
                </div>
            </div>
        </>
    );
}
