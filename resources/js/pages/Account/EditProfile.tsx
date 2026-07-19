import { Head, Link, useForm, usePage } from '@inertiajs/react';
import { Check, ChevronLeft, Lock, User } from 'lucide-react';
import type { FormEvent, ReactNode } from 'react';

import { Avatar, AvatarFallback } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import { Field, FieldError, FieldLabel } from '@/components/ui/field';
import { Input } from '@/components/ui/input';
import { PhoneInput, formatUsPhone } from '@/components/PhoneInput';
import AppLayout from '@/layouts/AppLayout';

// ── Types ────────────────────────────────────────────────────────────────────

interface AccountProfileTranslations {
    back: string;
    title: string;
    subtitle: string;
    infoHeading: string;
    infoSubheading: string;
    nameLabel: string;
    emailLabel: string;
    cellphoneLabel: string;
    cellphonePlaceholder: string;
    cellphoneHelp: string;
    emailChangeNote: string;
    save: string;
    saved: string;
    passwordHeading: string;
    passwordSubheading: string;
    currentPasswordLabel: string;
    newPasswordLabel: string;
    confirmPasswordLabel: string;
    updatePassword: string;
}

interface PageProps {
    auth: { user: { name: string; email: string; cellphone: string | null } };
    translations: { accountProfile: AccountProfileTranslations };
    [key: string]: unknown;
}

// ── Helpers ──────────────────────────────────────────────────────────────────

function initialsOf(name: string): string {
    return name
        .trim()
        .split(/\s+/)
        .map((part) => part[0] ?? '')
        .slice(0, 2)
        .join('')
        .toUpperCase();
}

function SavedPill({ label }: { label: string }) {
    return (
        <span className="inline-flex items-center gap-1 text-sm font-medium text-brand-green">
            <Check className="h-4 w-4" />
            {label}
        </span>
    );
}

function SectionCard({
    icon,
    heading,
    subheading,
    onSubmit,
    children,
}: {
    icon: ReactNode;
    heading: string;
    subheading: string;
    onSubmit: (event: FormEvent) => void;
    children: ReactNode;
}) {
    return (
        <form
            onSubmit={onSubmit}
            className="rounded-2xl border border-gray-200/80 bg-white p-6 shadow-sm ring-1 ring-black/[0.02] dark:border-gray-700/60 dark:bg-gray-800/50 dark:ring-white/[0.02]"
        >
            <div className="flex items-center gap-3">
                <span className="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-brand-green/10 text-brand-green">
                    {icon}
                </span>
                <div className="min-w-0">
                    <h2 className="text-base font-bold text-brand-grey dark:text-gray-100">
                        {heading}
                    </h2>
                    <p className="text-sm text-gray-500 dark:text-gray-400">
                        {subheading}
                    </p>
                </div>
            </div>
            {children}
        </form>
    );
}

// ── Page ─────────────────────────────────────────────────────────────────────

export default function EditProfile() {
    const { auth, translations } = usePage<PageProps>().props;
    const t = translations.accountProfile;

    const profile = useForm({
        name: auth.user.name,
        email: auth.user.email,
        cellphone: formatUsPhone((auth.user.cellphone ?? '').replace(/^\+1/, '')),
    });

    const password = useForm({
        current_password: '',
        password: '',
        password_confirmation: '',
    });

    const submitProfile = (event: FormEvent) => {
        event.preventDefault();
        profile.put('/user/profile-information', {
            errorBag: 'updateProfileInformation',
            preserveScroll: true,
        });
    };

    const submitPassword = (event: FormEvent) => {
        event.preventDefault();
        password.put('/user/password', {
            errorBag: 'updatePassword',
            preserveScroll: true,
            onSuccess: () => password.reset(),
        });
    };

    const emailChanged = profile.data.email !== auth.user.email;

    return (
        <AppLayout>
            <Head title={t.title} />

            {/* Hero */}
            <div className="relative overflow-hidden">
                <div
                    aria-hidden
                    className="pointer-events-none absolute -top-16 left-1/2 h-56 w-[36rem] max-w-full -translate-x-1/2 rounded-full bg-brand-green/10 blur-3xl dark:bg-brand-green/[0.07]"
                />
                <div className="relative mx-auto max-w-2xl px-6 pt-6 pb-2">
                    <Link
                        href="/account"
                        className="inline-flex items-center gap-1 text-sm font-medium text-gray-500 transition-colors hover:text-brand-green dark:text-gray-400"
                    >
                        <ChevronLeft className="h-4 w-4" />
                        {t.back}
                    </Link>

                    <div className="mt-5 flex items-center gap-4">
                        <Avatar className="size-14 shadow-sm ring-2 ring-white dark:ring-gray-800">
                            <AvatarFallback className="bg-gradient-to-br from-brand-green to-green-600 text-lg font-semibold text-white">
                                {initialsOf(auth.user.name)}
                            </AvatarFallback>
                        </Avatar>
                        <div className="min-w-0">
                            <h1 className="truncate text-2xl font-bold tracking-tight text-brand-grey dark:text-gray-50">
                                {auth.user.name}
                            </h1>
                            <p className="truncate text-sm text-gray-500 dark:text-gray-400">
                                {t.subtitle}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div className="mx-auto max-w-2xl space-y-6 px-6 pt-4 pb-12">
                {/* Personal information */}
                <SectionCard
                    icon={<User className="h-5 w-5" />}
                    heading={t.infoHeading}
                    subheading={t.infoSubheading}
                    onSubmit={submitProfile}
                >
                    <div className="mt-5 space-y-4">
                        <Field data-invalid={!!profile.errors.name || undefined}>
                            <FieldLabel htmlFor="name">
                                {t.nameLabel}
                            </FieldLabel>
                            <Input
                                id="name"
                                name="name"
                                type="text"
                                autoComplete="name"
                                value={profile.data.name}
                                aria-invalid={!!profile.errors.name || undefined}
                                onChange={(e) =>
                                    profile.setData('name', e.target.value)
                                }
                            />
                            <FieldError>{profile.errors.name}</FieldError>
                        </Field>

                        <Field data-invalid={!!profile.errors.email || undefined}>
                            <FieldLabel htmlFor="email">
                                {t.emailLabel}
                            </FieldLabel>
                            <Input
                                id="email"
                                name="email"
                                type="email"
                                autoComplete="email"
                                value={profile.data.email}
                                aria-invalid={
                                    !!profile.errors.email || undefined
                                }
                                onChange={(e) =>
                                    profile.setData('email', e.target.value)
                                }
                            />
                            <FieldError>{profile.errors.email}</FieldError>
                            {emailChanged && !profile.errors.email && (
                                <p className="text-xs text-amber-600 dark:text-amber-500">
                                    {t.emailChangeNote}
                                </p>
                            )}
                        </Field>

                        <Field
                            data-invalid={!!profile.errors.cellphone || undefined}
                        >
                            <FieldLabel htmlFor="cellphone">
                                {t.cellphoneLabel}
                            </FieldLabel>
                            <PhoneInput
                                id="cellphone"
                                name="cellphone"
                                placeholder={t.cellphonePlaceholder}
                                value={profile.data.cellphone}
                                invalid={!!profile.errors.cellphone}
                                onChange={(v) =>
                                    profile.setData('cellphone', v)
                                }
                            />
                            <FieldError>{profile.errors.cellphone}</FieldError>
                            {!profile.errors.cellphone && (
                                <p className="text-xs text-gray-400 dark:text-gray-500">
                                    {t.cellphoneHelp}
                                </p>
                            )}
                        </Field>
                    </div>

                    <div className="mt-6 flex items-center gap-4">
                        <Button
                            type="submit"
                            disabled={profile.processing}
                            className="h-10 rounded-4xl bg-brand-green px-5 text-sm font-semibold text-white shadow-sm shadow-brand-green/25 transition-colors hover:bg-brand-green/90 focus-visible:ring-brand-green/50"
                        >
                            {t.save}
                        </Button>
                        {profile.recentlySuccessful && (
                            <SavedPill label={t.saved} />
                        )}
                    </div>
                </SectionCard>

                {/* Change password */}
                <SectionCard
                    icon={<Lock className="h-5 w-5" />}
                    heading={t.passwordHeading}
                    subheading={t.passwordSubheading}
                    onSubmit={submitPassword}
                >
                    <div className="mt-5 space-y-4">
                        <Field
                            data-invalid={
                                !!password.errors.current_password || undefined
                            }
                        >
                            <FieldLabel htmlFor="current_password">
                                {t.currentPasswordLabel}
                            </FieldLabel>
                            <Input
                                id="current_password"
                                name="current_password"
                                type="password"
                                autoComplete="current-password"
                                value={password.data.current_password}
                                aria-invalid={
                                    !!password.errors.current_password ||
                                    undefined
                                }
                                onChange={(e) =>
                                    password.setData(
                                        'current_password',
                                        e.target.value,
                                    )
                                }
                            />
                            <FieldError>
                                {password.errors.current_password}
                            </FieldError>
                        </Field>

                        <Field
                            data-invalid={
                                !!password.errors.password || undefined
                            }
                        >
                            <FieldLabel htmlFor="password">
                                {t.newPasswordLabel}
                            </FieldLabel>
                            <Input
                                id="password"
                                name="password"
                                type="password"
                                autoComplete="new-password"
                                value={password.data.password}
                                aria-invalid={
                                    !!password.errors.password || undefined
                                }
                                onChange={(e) =>
                                    password.setData('password', e.target.value)
                                }
                            />
                            <FieldError>{password.errors.password}</FieldError>
                        </Field>

                        <Field>
                            <FieldLabel htmlFor="password_confirmation">
                                {t.confirmPasswordLabel}
                            </FieldLabel>
                            <Input
                                id="password_confirmation"
                                name="password_confirmation"
                                type="password"
                                autoComplete="new-password"
                                value={password.data.password_confirmation}
                                onChange={(e) =>
                                    password.setData(
                                        'password_confirmation',
                                        e.target.value,
                                    )
                                }
                            />
                        </Field>
                    </div>

                    <div className="mt-6 flex items-center gap-4">
                        <Button
                            type="submit"
                            disabled={password.processing}
                            className="h-10 rounded-4xl bg-brand-green px-5 text-sm font-semibold text-white shadow-sm shadow-brand-green/25 transition-colors hover:bg-brand-green/90 focus-visible:ring-brand-green/50"
                        >
                            {t.updatePassword}
                        </Button>
                        {password.recentlySuccessful && (
                            <SavedPill label={t.saved} />
                        )}
                    </div>
                </SectionCard>
            </div>
        </AppLayout>
    );
}
