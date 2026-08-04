import { Head, router, usePage } from '@inertiajs/react';
import {
    Bell,
    KeyRound,
    Languages,
    MapPin,
    Moon,
    ShieldCheck,
    SlidersHorizontal,
    Trash2,
    TriangleAlert,
    Truck,
} from 'lucide-react';
import { useState } from 'react';

import LocaleController from '@/actions/App/Http/Controllers/LocaleController';
import SavedPill from '@/components/SavedPill';
import SectionCard from '@/components/SectionCard';
import SegmentedControl from '@/components/settings/SegmentedControl';
import SettingRow from '@/components/settings/SettingRow';
import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
    AlertDialogTrigger,
} from '@/components/ui/alert-dialog';
import { Button } from '@/components/ui/button';
import { Switch } from '@/components/ui/switch';
import AccountLayout from '@/layouts/AccountLayout';
import { profile as accountProfile } from '@/routes/account';
import { clearDriverCoords } from '@/utils/driverCoords';
import { useThemeProvider } from '@/utils/ThemeContext';

// ── Types ────────────────────────────────────────────────────────────────────

interface SettingsTranslations {
    title: string;
    subtitle: string;
    saved: string;
    soon: string;
    preferencesHeading: string;
    preferencesSubheading: string;
    languageLabel: string;
    languageDescription: string;
    themeLabel: string;
    themeDescription: string;
    themeLight: string;
    themeDark: string;
    defaultLocationLabel: string;
    defaultLocationDescription: string;
    defaultLocationNone: string;
    notificationsHeading: string;
    notificationsSubheading: string;
    channelLabel: string;
    channelEmail: string;
    channelSms: string;
    channelBoth: string;
    channelNeedsPhone: string;
    checkInCopyLabel: string;
    checkInCopyDescription: string;
    appointmentReminderLabel: string;
    appointmentReminderDescription: string;
    securityAlwaysEmail: string;
    securityHeading: string;
    securitySubheading: string;
    twoFactorLabel: string;
    twoFactorDescription: string;
    passkeyLabel: string;
    passkeyDescription: string;
    dataHeading: string;
    dataSubheading: string;
    clearLocationLabel: string;
    clearLocationDescription: string;
    clearLocationAction: string;
    clearLocationDone: string;
    deleteHeading: string;
    deleteSubheading: string;
    deleteLabel: string;
    deleteDescription: string;
    deleteAction: string;
    deleteDialogTitle: string;
    deleteDialogBody: string;
    deleteDialogCancel: string;
    deleteDialogConfirm: string;
}

interface PageProps {
    locations: { id: string; name: string }[];
    hasCellphone: boolean;
    currentLocale: string;
    localesLabels: Record<string, string>;
    translations: { accountSettings: SettingsTranslations };
    [key: string]: unknown;
}

type Channel = 'email' | 'sms' | 'both';

// Mirrors the native select on the profile page, so the two read as one app.
// text-base on mobile stops iOS zooming the viewport on focus.
const SELECT_CLASSES =
    'h-9 w-full min-w-0 rounded-4xl border border-input bg-input/30 px-3 py-1 text-base transition-colors outline-none focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50 md:text-sm';

// ── Page ─────────────────────────────────────────────────────────────────────

export default function Settings() {
    const {
        locations,
        hasCellphone,
        currentLocale,
        localesLabels,
        translations,
    } = usePage<PageProps>().props;
    const t = translations.accountSettings;

    const { currentTheme, changeCurrentTheme } = useThemeProvider();

    // No preference schema exists yet, so these live in component state and
    // reset on reload. Nothing here fakes a save.
    const [channel, setChannel] = useState<Channel>('email');
    const [checkInCopy, setCheckInCopy] = useState(true);
    const [appointmentReminder, setAppointmentReminder] = useState(true);
    const [defaultLocation, setDefaultLocation] = useState('');
    const [locationCleared, setLocationCleared] = useState(false);

    const switchLocale = (code: string) => {
        if (code === currentLocale) {
            return;
        }

        router.post(
            LocaleController.post({ locale: code }).url,
            {},
            { preserveScroll: true, preserveState: false },
        );
    };

    const forgetLocation = () => {
        clearDriverCoords();
        setLocationCleared(true);
    };

    return (
        <AccountLayout>
            <Head title={t.title} />

            <div className="mx-auto max-w-2xl space-y-6 px-6 pt-6 pb-12">
                <div>
                    <h1 className="text-xl font-bold tracking-tight text-brand-grey dark:text-gray-50">
                        {t.title}
                    </h1>
                    <p className="mt-0.5 text-sm text-gray-500 dark:text-gray-400">
                        {t.subtitle}
                    </p>
                </div>

                {/* Preferences */}
                <SectionCard
                    bleed
                    icon={<SlidersHorizontal className="h-5 w-5" />}
                    heading={t.preferencesHeading}
                    subheading={t.preferencesSubheading}
                >
                    <div className="mt-4 divide-y divide-gray-100 dark:divide-gray-700/50">
                        <SettingRow
                            stacked
                            icon={Languages}
                            label={t.languageLabel}
                            description={t.languageDescription}
                            control={
                                <SegmentedControl
                                    label={t.languageLabel}
                                    value={currentLocale}
                                    onChange={switchLocale}
                                    segments={Object.entries(localesLabels).map(
                                        ([code, name]) => ({
                                            value: code,
                                            label: name,
                                        }),
                                    )}
                                />
                            }
                        />

                        <SettingRow
                            stacked
                            icon={Moon}
                            label={t.themeLabel}
                            description={t.themeDescription}
                            control={
                                <SegmentedControl
                                    label={t.themeLabel}
                                    value={
                                        currentTheme === 'light'
                                            ? 'light'
                                            : 'dark'
                                    }
                                    onChange={changeCurrentTheme}
                                    segments={[
                                        {
                                            value: 'light',
                                            label: t.themeLight,
                                        },
                                        { value: 'dark', label: t.themeDark },
                                    ]}
                                />
                            }
                        />

                        <SettingRow
                            stacked
                            icon={Truck}
                            label={t.defaultLocationLabel}
                            description={t.defaultLocationDescription}
                            control={
                                <select
                                    aria-label={t.defaultLocationLabel}
                                    value={defaultLocation}
                                    onChange={(e) =>
                                        setDefaultLocation(e.target.value)
                                    }
                                    className={SELECT_CLASSES}
                                >
                                    <option value="">
                                        {t.defaultLocationNone}
                                    </option>
                                    {locations.map((location) => (
                                        <option
                                            key={location.id}
                                            value={location.id}
                                        >
                                            {location.name}
                                        </option>
                                    ))}
                                </select>
                            }
                        />
                    </div>
                </SectionCard>

                {/* Notifications */}
                <SectionCard
                    bleed
                    icon={<Bell className="h-5 w-5" />}
                    heading={t.notificationsHeading}
                    subheading={t.notificationsSubheading}
                >
                    <div className="mt-4 divide-y divide-gray-100 dark:divide-gray-700/50">
                        <SettingRow
                            stacked
                            label={t.channelLabel}
                            description={
                                hasCellphone ? undefined : t.channelNeedsPhone
                            }
                            control={
                                <div className="space-y-2">
                                    <SegmentedControl
                                        label={t.channelLabel}
                                        value={channel}
                                        onChange={setChannel}
                                        segments={[
                                            {
                                                value: 'email',
                                                label: t.channelEmail,
                                            },
                                            {
                                                value: 'sms',
                                                label: t.channelSms,
                                                disabled: !hasCellphone,
                                            },
                                            {
                                                value: 'both',
                                                label: t.channelBoth,
                                                disabled: !hasCellphone,
                                            },
                                        ]}
                                    />
                                    {!hasCellphone && (
                                        <a
                                            href={accountProfile().url}
                                            className="inline-block text-sm font-semibold text-brand-green underline underline-offset-2"
                                        >
                                            {t.channelNeedsPhone}
                                        </a>
                                    )}
                                </div>
                            }
                        />

                        <SettingRow
                            label={t.checkInCopyLabel}
                            description={t.checkInCopyDescription}
                            control={
                                <Switch
                                    checked={checkInCopy}
                                    onCheckedChange={setCheckInCopy}
                                    aria-label={t.checkInCopyLabel}
                                />
                            }
                        />

                        <SettingRow
                            label={t.appointmentReminderLabel}
                            description={t.appointmentReminderDescription}
                            control={
                                <Switch
                                    checked={appointmentReminder}
                                    onCheckedChange={setAppointmentReminder}
                                    aria-label={t.appointmentReminderLabel}
                                />
                            }
                        />
                    </div>

                    <p className="px-6 py-4 text-xs text-gray-500 dark:text-gray-400">
                        {t.securityAlwaysEmail}
                    </p>
                </SectionCard>

                {/* Security */}
                <SectionCard
                    bleed
                    icon={<ShieldCheck className="h-5 w-5" />}
                    heading={t.securityHeading}
                    subheading={t.securitySubheading}
                >
                    <div className="mt-4 divide-y divide-gray-100 dark:divide-gray-700/50">
                        <SettingRow
                            disabled
                            badge={t.soon}
                            icon={ShieldCheck}
                            label={t.twoFactorLabel}
                            description={t.twoFactorDescription}
                            control={<Switch disabled aria-hidden />}
                        />
                        <SettingRow
                            disabled
                            badge={t.soon}
                            icon={KeyRound}
                            label={t.passkeyLabel}
                            description={t.passkeyDescription}
                            control={<Switch disabled aria-hidden />}
                        />
                    </div>
                </SectionCard>

                {/* Your data */}
                <SectionCard
                    bleed
                    icon={<MapPin className="h-5 w-5" />}
                    heading={t.dataHeading}
                    subheading={t.dataSubheading}
                >
                    <div className="mt-4">
                        <SettingRow
                            icon={MapPin}
                            label={t.clearLocationLabel}
                            description={t.clearLocationDescription}
                            control={
                                locationCleared ? (
                                    <SavedPill label={t.clearLocationDone} />
                                ) : (
                                    <Button
                                        type="button"
                                        variant="outline"
                                        onClick={forgetLocation}
                                        className="h-11 cursor-pointer rounded-4xl px-5 text-sm font-semibold"
                                    >
                                        {t.clearLocationAction}
                                    </Button>
                                )
                            }
                        />
                    </div>
                </SectionCard>

                {/* Delete account */}
                <SectionCard
                    bleed
                    tone="destructive"
                    icon={<TriangleAlert className="h-5 w-5" />}
                    heading={t.deleteHeading}
                    subheading={t.deleteSubheading}
                >
                    <div className="mt-4">
                        <SettingRow
                            icon={Trash2}
                            label={t.deleteLabel}
                            description={t.deleteDescription}
                            badge={t.soon}
                            control={
                                <AlertDialog>
                                    <AlertDialogTrigger asChild>
                                        <Button
                                            type="button"
                                            variant="destructive"
                                            className="h-11 cursor-pointer rounded-4xl px-5 text-sm font-semibold"
                                        >
                                            {t.deleteAction}
                                        </Button>
                                    </AlertDialogTrigger>

                                    <AlertDialogContent>
                                        <AlertDialogHeader>
                                            <AlertDialogTitle>
                                                {t.deleteDialogTitle}
                                            </AlertDialogTitle>
                                            <AlertDialogDescription>
                                                {t.deleteDialogBody}
                                            </AlertDialogDescription>
                                        </AlertDialogHeader>

                                        <AlertDialogFooter>
                                            <AlertDialogCancel asChild>
                                                <Button
                                                    type="button"
                                                    variant="outline"
                                                    className="h-11 w-full cursor-pointer rounded-4xl text-sm font-semibold sm:w-auto sm:px-5"
                                                >
                                                    {t.deleteDialogCancel}
                                                </Button>
                                            </AlertDialogCancel>
                                            <AlertDialogAction asChild>
                                                <Button
                                                    type="button"
                                                    disabled
                                                    variant="destructive"
                                                    className="h-11 w-full cursor-not-allowed rounded-4xl text-sm font-semibold sm:w-auto sm:px-5"
                                                >
                                                    {t.deleteDialogConfirm}
                                                </Button>
                                            </AlertDialogAction>
                                        </AlertDialogFooter>
                                    </AlertDialogContent>
                                </AlertDialog>
                            }
                        />
                    </div>
                </SectionCard>
            </div>
        </AccountLayout>
    );
}
