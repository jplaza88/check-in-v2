'use client';
import { format } from 'date-fns';
import { CalendarIcon, ChevronUpIcon, ChevronDownIcon, ClockIcon } from 'lucide-react';
import * as React from 'react';
import { Button } from '@/components/ui/button';
import { Calendar } from '@/components/ui/calendar';
import {
    Popover,
    PopoverContent,
    PopoverTrigger,
} from '@/components/ui/popover';
import { cn } from '@/lib/utils';

const MINUTE_INTERVAL = 15;

// ─── Drum Column ──────────────────────────────────────────────────────────────

function DrumColumn({
    values,
    selected,
    onSelect,
    label,
    fmt = (v: number) => String(v).padStart(2, '0'),
    className,
}: {
    values: number[];
    selected: number;
    onSelect: (v: number) => void;
    label: string;
    fmt?: (v: number) => string;
    className?: string;
}) {
    const idx = values.indexOf(selected);
    const prev = values[(idx - 1 + values.length) % values.length];
    const next = values[(idx + 1) % values.length];

    return (
        <div className={cn('flex flex-col items-center gap-1', className)}>
            <span className="text-[10px] text-muted-foreground">{label}</span>
            <button
                type="button"
                onClick={() => onSelect(prev)}
                className="flex h-7 w-full items-center justify-center rounded-md text-muted-foreground transition-colors hover:bg-accent hover:text-accent-foreground"
            >
                <ChevronUpIcon className="size-4" />
            </button>
            <div className="w-full overflow-hidden rounded-md border bg-muted/40">
                <div className="flex h-8 items-center justify-center text-sm text-muted-foreground/40 select-none">
                    {fmt(prev)}
                </div>
                <div className="flex h-10 items-center justify-center border-y bg-background text-base font-medium tabular-nums select-none dark:bg-gray-700">
                    {fmt(selected)}
                </div>
                <div className="flex h-8 items-center justify-center text-sm text-muted-foreground/40 select-none">
                    {fmt(next)}
                </div>
            </div>
            <button
                type="button"
                onClick={() => onSelect(next)}
                className="flex h-7 w-full items-center justify-center rounded-md text-muted-foreground transition-colors hover:bg-accent hover:text-accent-foreground"
            >
                <ChevronDownIcon className="size-4" />
            </button>
        </div>
    );
}

// ─── Date Picker ──────────────────────────────────────────────────────────────

export function DatePicker({
    date,
    onDateChange,
}: {
    date: Date | undefined;
    onDateChange: (d: Date | undefined) => void;
}) {
    const [open, setOpen] = React.useState(false);

    return (
        <Popover open={open} onOpenChange={setOpen}>
            <PopoverTrigger asChild>
                <Button
                    variant="outline"
                    className="w-full justify-between border-input font-normal aria-expanded:bg-white dark:aria-expanded:bg-gray-800"
                >
                    <CalendarIcon data-icon="inline-start" className="size-4 opacity-50" />
                    {date ? format(date, 'PP') : 'Select date'}
                    <ChevronDownIcon className="size-4 opacity-50" />
                </Button>
            </PopoverTrigger>
            <PopoverContent
                className="w-auto overflow-hidden p-0"
                align="start"
            >
                <Calendar
                    mode="single"
                    selected={date}
                    captionLayout="dropdown"
                    defaultMonth={date}
                    disabled={{ before: new Date() }}
                    onSelect={(d) => {
                        onDateChange(d);
                        setOpen(false);
                    }}
                />
            </PopoverContent>
        </Popover>
    );
}

// ─── Time Picker ──────────────────────────────────────────────────────────────

export function TimePicker({
    value,
    onChange,
}: {
    value: string; // "HH:MM:00" 24h
    onChange: (v: string) => void;
}) {
    const [open, setOpen] = React.useState(false);

    const [h24, m] = value.split(':').map(Number);
    const isPm = h24 >= 12;
    const h12 = h24 % 12 === 0 ? 12 : h24 % 12;

    const hours = Array.from({ length: 12 }, (_, i) => i + 1);
    const minutes = Array.from(
        { length: Math.ceil(60 / MINUTE_INTERVAL) },
        (_, i) => i * MINUTE_INTERVAL,
    );

    const snapped = minutes.reduce(
        (best, v) => (Math.abs(v - m) < Math.abs(best - m) ? v : best),
        minutes[0],
    );

    const [selHour, setSelHour] = React.useState(h12);
    const [selMinute, setSelMinute] = React.useState(snapped);
    const [selPm, setSelPm] = React.useState(isPm);

    React.useEffect(() => {
        const out24 = selPm
            ? selHour === 12
                ? 12
                : selHour + 12
            : selHour === 12
              ? 0
              : selHour;
        const pad = (n: number) => String(n).padStart(2, '0');
        onChange(`${pad(out24)}:${pad(selMinute)}:00`);
    }, [selHour, selMinute, selPm]);

    const label = `${String(selHour).padStart(2, '0')}:${String(selMinute).padStart(2, '0')} ${selPm ? 'PM' : 'AM'}`;

    return (
        <Popover open={open} onOpenChange={setOpen}>
            <PopoverTrigger asChild>
                <Button
                    variant="outline"
                    className="w-full justify-between border-input font-normal tabular-nums aria-expanded:bg-white dark:aria-expanded:bg-gray-800"
                >
                    <ClockIcon data-icon="inline-start" className="size-4 opacity-50" />
                    {label}
                    <ChevronDownIcon className="size-4 opacity-50" />
                </Button>
            </PopoverTrigger>
            <PopoverContent className="w-44 p-4" align="start">
                {/* drums */}
                <div className="flex w-full items-start gap-3">
                    <DrumColumn
                        label="hour"
                        values={hours}
                        selected={selHour}
                        onSelect={setSelHour}
                        className="flex-1"
                    />
                    <span className="mt-18 text-xl font-light text-muted-foreground">
                        :
                    </span>
                    <DrumColumn
                        label="min"
                        values={minutes}
                        selected={selMinute}
                        onSelect={setSelMinute}
                        className="flex-1"
                    />
                </div>

                {/* AM / PM below */}
                <div className="mt-3 flex overflow-hidden rounded-md border border-input">
                    {(['AM', 'PM'] as const).map((period) => (
                        <button
                            key={period}
                            type="button"
                            onClick={() => setSelPm(period === 'PM')}
                            className={cn(
                                'flex-1 py-1.5 text-sm font-medium transition-colors',
                                selPm === (period === 'PM')
                                    ? 'bg-primary text-primary-foreground'
                                    : 'bg-background text-muted-foreground hover:bg-accent',
                            )}
                        >
                            {period}
                        </button>
                    ))}
                </div>
            </PopoverContent>
        </Popover>
    );
}
