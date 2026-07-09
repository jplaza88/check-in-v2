export function StepBadge({ number }: { number: number }) {
    return (
        <span className="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-brand-green text-[10px] font-bold text-white">
            {number}
        </span>
    );
}

export default function SectionHeader({
    step,
    label,
}: {
    step: number;
    label: string;
}) {
    return (
        <div className="flex items-center gap-2.5">
            <StepBadge number={step} />
            <span className="text-xs font-bold tracking-widest text-brand-green uppercase">
                {label}
            </span>
        </div>
    );
}
