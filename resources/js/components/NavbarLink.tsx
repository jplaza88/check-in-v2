import { Link, usePage } from '@inertiajs/react';

interface NavbarLinkProps {
    href: string;
    name: string;
    activeRoute: string;
}

interface PageProps {
    currentRoute: string;
    [key: string]: unknown;
}

export default function NavbarLink({ href, name, activeRoute
}: NavbarLinkProps) {
    const { currentRoute } = usePage<PageProps>().props;

    console.log(currentRoute)

    const isActive = currentRoute === activeRoute;

    return (
        <Link
            href={href}
            className={`group relative transition-colors duration-300 tracking-wide ${
                isActive
                    ? 'text-brand-grey-link font-semibold dark:text-gray-200'
                    : 'text-brand-grey-link dark:text-gray-200'
            }`}
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
