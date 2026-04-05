import logo from './../../images/logo.png'

type LogoSize = 'sm' | 'md' | 'lg';

interface LogoProps {
    size?: LogoSize;
    className?: string;
}

export default function Logo({ size = 'md', className = '' }: LogoProps) {
    const appName = import.meta.env.VITE_APP_NAME;

    const sizeClasses = {
        sm: 'h-5',
        md: 'h-8',
        lg: 'h-12',
    };

    return (
        <img
            src={logo}
            className={`${sizeClasses[size] || sizeClasses.md} ${className}`}
            role="img"
            aria-label={`${appName} Logo`}
            alt={`${appName} Logo`}
        />
    );
}
