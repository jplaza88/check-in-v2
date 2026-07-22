import { Head, useForm, usePage } from '@inertiajs/react';
import { Check, IdCard, Lock, User } from 'lucide-react';
import type { FormEvent, ReactNode } from 'react';

import LicenseCard from '@/components/LicenseCard';
import { Button } from '@/components/ui/button';
import { Field, FieldError, FieldLabel } from '@/components/ui/field';
import { Input } from '@/components/ui/input';
import { PhoneInput, formatUsPhone } from '@/components/PhoneInput';
import AccountLayout from '@/layouts/AccountLayout';
import { US_STATES } from '@/lib/usStates';

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
    licenseHeading: string;
    licenseSubheading: string;
    licenseNumberLabel: string;
    licenseNumberPlaceholder: string;
    licenseStateLabel: string;
    licenseStatePlaceholder: string;
    licenseExpirationLabel: string;
    licenseSecure: string;
    licenseEmpty: string;
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
    license: {
        number: string | null;
        state: string | null;
        expirationDate: string | null;
    };
    translations: { accountProfile: AccountProfileTranslations };
    [key: string]: unknown;
}

// ── Helpers ──────────────────────────────────────────────────────────────────

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
    const { auth, license, translations } = usePage<PageProps>().props;
    const t = translations.accountProfile;

    const profile = useForm({
        name: auth.user.name,
        email: auth.user.email,
        cellphone: formatUsPhone((auth.user.cellphone ?? '').replace(/^\+1/, '')),
        drivers_license_number: license.number ?? '',
        drivers_license_state: license.state ?? '',
        drivers_license_expiration_date: license.expirationDate ?? '',
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
        <AccountLayout>
            <Head title={t.title} />

            <div className="mx-auto max-w-2xl space-y-6 px-6 pt-6 pb-12">
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
                            className="h-10 rounded-4xl bg-brand-green px-5 text-sm font-semibold text-white shadow-sm shadow-brand-green/25 transition-colors hover:bg-brand-green/90 focus-visible:ring-brand-green/50 cursor-pointer"
                        >
                            {t.save}
                        </Button>
                        {profile.recentlySuccessful && (
                            <SavedPill label={t.saved} />
                        )}
                    </div>
                </SectionCard>

                {/* Driver's license */}
                <SectionCard
                    icon={<IdCard className="h-5 w-5" />}
                    heading={t.licenseHeading}
                    subheading={t.licenseSubheading}
                    onSubmit={submitProfile}
                >
                    <div className="mt-5 space-y-5">
                        {/* Live, state-themed card preview */}
                        <LicenseCard
                            name={profile.data.name}
                            number={profile.data.drivers_license_number || null}
                            state={profile.data.drivers_license_state || null}
                            expirationDate={
                                profile.data.drivers_license_expiration_date ||
                                null
                            }
                            secureLabel={t.licenseSecure}
                            emptyLabel={t.licenseEmpty}
                            className="mx-auto max-w-sm"
                        />

                        <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <Field
                                data-invalid={
                                    !!profile.errors.drivers_license_number ||
                                    undefined
                                }
                            >
                                <FieldLabel htmlFor="drivers_license_number">
                                    {t.licenseNumberLabel}
                                </FieldLabel>
                                <Input
                                    id="drivers_license_number"
                                    name="drivers_license_number"
                                    type="text"
                                    maxLength={20}
                                    autoComplete="off"
                                    autoCapitalize="characters"
                                    value={
                                        profile.data.drivers_license_number
                                    }
                                    aria-invalid={
                                        !!profile.errors
                                            .drivers_license_number || undefined
                                    }
                                    placeholder={t.licenseNumberPlaceholder}
                                    onChange={(e) =>
                                        profile.setData(
                                            'drivers_license_number',
                                            e.target.value,
                                        )
                                    }
                                />
                                <FieldError>
                                    {profile.errors.drivers_license_number}
                                </FieldError>
                            </Field>

                            <Field
                                data-invalid={
                                    !!profile.errors.drivers_license_state ||
                                    undefined
                                }
                            >
                                <FieldLabel htmlFor="drivers_license_state">
                                    {t.licenseStateLabel}
                                </FieldLabel>
                                <select
                                    id="drivers_license_state"
                                    name="drivers_license_state"
                                    value={profile.data.drivers_license_state}
                                    aria-invalid={
                                        !!profile.errors
                                            .drivers_license_state || undefined
                                    }
                                    onChange={(e) =>
                                        profile.setData(
                                            'drivers_license_state',
                                            e.target.value,
                                        )
                                    }
                                    className="h-9 w-full min-w-0 rounded-4xl border border-input bg-input/30 px-3 py-1 text-base transition-colors outline-none focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50 aria-invalid:border-destructive aria-invalid:ring-[3px] aria-invalid:ring-destructive/20 md:text-sm"
                                >
                                    <option value="">
                                        {t.licenseStatePlaceholder}
                                    </option>
                                    {US_STATES.map((state) => (
                                        <option
                                            key={state.abbr}
                                            value={state.name}
                                        >
                                            {state.name}
                                        </option>
                                    ))}
                                </select>
                                <FieldError>
                                    {profile.errors.drivers_license_state}
                                </FieldError>
                            </Field>
                        </div>

                        <Field
                            data-invalid={
                                !!profile.errors
                                    .drivers_license_expiration_date || undefined
                            }
                            className="sm:max-w-[calc(50%-0.5rem)]"
                        >
                            <FieldLabel htmlFor="drivers_license_expiration_date">
                                {t.licenseExpirationLabel}
                            </FieldLabel>
                            <Input
                                id="drivers_license_expiration_date"
                                name="drivers_license_expiration_date"
                                type="date"
                                value={
                                    profile.data
                                        .drivers_license_expiration_date
                                }
                                aria-invalid={
                                    !!profile.errors
                                        .drivers_license_expiration_date ||
                                    undefined
                                }
                                onChange={(e) =>
                                    profile.setData(
                                        'drivers_license_expiration_date',
                                        e.target.value,
                                    )
                                }
                            />
                            <FieldError>
                                {
                                    profile.errors
                                        .drivers_license_expiration_date
                                }
                            </FieldError>
                        </Field>
                    </div>

                    <div className="mt-6 flex items-center gap-4">
                        <Button
                            type="submit"
                            disabled={profile.processing}
                            className="h-10 rounded-4xl bg-brand-green px-5 text-sm font-semibold text-white shadow-sm shadow-brand-green/25 transition-colors hover:bg-brand-green/90 focus-visible:ring-brand-green/50 cursor-pointer"
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
                            className="h-10 rounded-4xl bg-brand-green px-5 text-sm font-semibold text-white shadow-sm shadow-brand-green/25 transition-colors hover:bg-brand-green/90 focus-visible:ring-brand-green/50 cursor-pointer"
                        >
                            {t.updatePassword}
                        </Button>
                        {password.recentlySuccessful && (
                            <SavedPill label={t.saved} />
                        )}
                    </div>
                </SectionCard>
            </div>
        </AccountLayout>
    );
}
