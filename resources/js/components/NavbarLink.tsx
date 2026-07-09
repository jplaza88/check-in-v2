import { Link, usePage } from '@inertiajs/react';

interface NavbarLinkProps {
    href: string;
    name: string;
    activeRoute: string | string[];
    variant?: 'desktop' | 'mobile';
}

interface PageProps {
    currentRoute: string;
    [key: string]: unknown;
}

export default function NavbarLink({
    href,
    name,
    activeRoute,
    variant = 'desktop',
}: NavbarLinkProps) {
    const { currentRoute } = usePage<PageProps>().props;
    const isActive = Array.isArray(activeRoute)
        ? activeRoute.includes(currentRoute)
        : currentRoute === activeRoute;

    if (variant === 'mobile') {
        return (
            <Link
                href={href}
                className={`py-2.5 text-sm tracking-wide transition-colors duration-300 ${
                    isActive
                        ? 'font-semibold text-brand-green'
                        : 'font-medium text-brand-grey-link hover:text-brand-green dark:text-gray-200'
                }`}
            >
                {name}
            </Link>
        );
    }

    return (
        <Link
            href={href}
            className={`group relative tracking-wide transition-colors duration-300 ${
                isActive
                    ? 'font-semibold text-brand-grey-link dark:text-gray-200'
                    : 'text-brand-grey-link dark:text-gray-200'
            }`}
            prefetch
        >
            {name}
            <span
                className={`absolute -bottom-1.5 left-0 h-0.5 bg-brand-green transition-all duration-300 ${
                    isActive ? 'w-full' : 'w-0 group-hover:w-full'
                }`}
            />
        </Link>
    );
}
