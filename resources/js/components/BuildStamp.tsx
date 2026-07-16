import { usePage } from '@inertiajs/react';

/**
 * Renders the deployed commit SHA as small muted text. Sourced from the
 * `commit` shared prop; renders nothing in local dev where it is null.
 */
export default function BuildStamp() {
    const { commit } = usePage().props;

    if (!commit) {
        return null;
    }

    return (
        <span className="font-mono text-[10px] tracking-wide text-gray-400 select-none dark:text-gray-500">
            {commit}
        </span>
    );
}
