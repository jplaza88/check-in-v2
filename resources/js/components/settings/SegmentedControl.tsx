import { RadioGroup as RadioGroupPrimitive } from 'radix-ui';

export interface Segment<T extends string> {
    value: T;
    label: string;
    disabled?: boolean;
}

/**
 * A real radio group rather than a row of buttons, so arrow keys move between
 * options and screen readers announce "1 of 3". Matches the pill rail used by
 * the account sub-nav and the history tabs.
 */
export default function SegmentedControl<T extends string>({
    value,
    onChange,
    segments,
    label,
}: {
    value: T;
    onChange: (value: T) => void;
    segments: Segment<T>[];
    label: string;
}) {
    return (
        <RadioGroupPrimitive.Root
            value={value}
            onValueChange={(next) => onChange(next as T)}
            aria-label={label}
            className="flex gap-1 rounded-2xl bg-gray-100/80 p-1 dark:bg-gray-800/50"
        >
            {segments.map((segment) => (
                <RadioGroupPrimitive.Item
                    key={segment.value}
                    value={segment.value}
                    disabled={segment.disabled}
                    className="min-h-11 min-w-0 flex-1 cursor-pointer truncate rounded-xl px-3 text-sm font-semibold text-gray-600 transition-colors outline-none hover:text-brand-grey focus-visible:ring-[3px] focus-visible:ring-brand-green/40 disabled:cursor-not-allowed disabled:opacity-40 data-[state=checked]:bg-brand-green data-[state=checked]:text-white data-[state=checked]:shadow-sm data-[state=checked]:shadow-brand-green/25 dark:text-gray-300 dark:hover:text-gray-100"
                >
                    {segment.label}
                </RadioGroupPrimitive.Item>
            ))}
        </RadioGroupPrimitive.Root>
    );
}
