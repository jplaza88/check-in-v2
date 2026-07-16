import { usePage } from '@inertiajs/react';

/**
 * Renders the deployed commit SHA in a subtle fixed badge at the bottom-right.
 * Sourced from the `commit` shared prop; hidden entirely in local dev where it
 * is null.
 */
export default function BuildStamp() {
    const { commit } = usePage().props;

    if (!commit) {
        return null;
    }

    return (
        <span
            className="pointer-events-none fixed right-2 bottom-2 z-50 select-none font-mono text-[10px] tracking-wide text-gray-400/70 dark:text-gray-500/70"
            title="Deployed commit"
        >
            {commit}
        </span>
    );
}
