import { Link, router, usePage } from '@inertiajs/react';
import { LogOut, User as UserIcon } from 'lucide-react';

import { Avatar, AvatarFallback } from '@/components/ui/avatar';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { profile as accountProfile } from '@/routes/account';

interface PageProps {
    auth: { user: { name: string; email: string } };
    translations: { publicNavigation: { logout: string; editProfile: string } };
    [key: string]: unknown;
}

function initialsOf(name: string): string {
    return name
        .trim()
        .split(/\s+/)
        .map((part) => part[0] ?? '')
        .slice(0, 2)
        .join('')
        .toUpperCase();
}

export default function UserMenu() {
    const { auth, translations } = usePage<PageProps>().props;
    const nav = translations.publicNavigation;
    const initials = initialsOf(auth.user.name);

    return (
        <DropdownMenu>
            <DropdownMenuTrigger asChild>
                <button
                    type="button"
                    aria-label={auth.user.name}
                    className="group cursor-pointer rounded-full outline-none transition-transform active:scale-95 focus-visible:ring-2 focus-visible:ring-brand-green/50 focus-visible:ring-offset-2 focus-visible:ring-offset-white dark:focus-visible:ring-offset-gray-900"
                >
                    <Avatar className="size-9 shadow-sm ring-2 ring-white transition-shadow group-hover:shadow-md dark:ring-gray-800">
                        <AvatarFallback className="bg-gradient-to-br from-brand-green to-green-600 text-sm font-semibold text-white">
                            {initials}
                        </AvatarFallback>
                    </Avatar>
                </button>
            </DropdownMenuTrigger>

            <DropdownMenuContent align="end" className="w-64 p-0">
                {/* Header strip — echoes the trigger avatar */}
                <div className="flex items-center gap-3 border-b border-gray-100 bg-brand-green/[0.06] px-3.5 py-3 dark:border-gray-700/50 dark:bg-brand-green/10">
                    <Avatar className="size-10 shadow-sm ring-2 ring-white dark:ring-gray-800">
                        <AvatarFallback className="bg-gradient-to-br from-brand-green to-green-600 text-sm font-semibold text-white">
                            {initials}
                        </AvatarFallback>
                    </Avatar>
                    <div className="min-w-0 flex-1">
                        <p className="truncate text-sm font-semibold text-foreground">
                            {auth.user.name}
                        </p>
                        <p className="truncate text-xs text-muted-foreground">
                            {auth.user.email}
                        </p>
                    </div>
                </div>

                {/* Actions */}
                <div className="p-1.5">
                    <DropdownMenuItem asChild>
                        <Link href={accountProfile().url}>
                            <UserIcon />
                            {nav.editProfile}
                        </Link>
                    </DropdownMenuItem>
                    <DropdownMenuItem
                        onSelect={() => router.post('/logout')}
                    >
                        <LogOut />
                        {nav.logout}
                    </DropdownMenuItem>
                </div>
            </DropdownMenuContent>
        </DropdownMenu>
    );
}
