import { PlusIcon, Trash2Icon } from 'lucide-react';
import {

    Controller



} from 'react-hook-form';
import type {Control, FieldArrayWithId, UseFieldArrayAppend, UseFieldArrayRemove} from 'react-hook-form';
import { z } from 'zod';

import { Button } from '@/components/ui/button';
import { Field, FieldError, FieldLabel } from '@/components/ui/field';
import { Input } from '@/components/ui/input';

export interface PurchaseOrderTranslations {
    label: string;
    placeholder: string;
    required: string;
    format: string;
    minOne: string;
    addMore: string;
    removeAriaLabel: string;
}

interface PurchaseOrderInputsProps {
    control: Control<any>;
    fields: FieldArrayWithId[];
    append: UseFieldArrayAppend<any>;
    remove: UseFieldArrayRemove;
    name?: string;
    translations: PurchaseOrderTranslations;
}

export function poNumberSchema(t: {
    required: string;
    format: string;
    minOne: string;
}) {
    return z
        .array(
            z.object({
                value: z
                    .string()
                    .min(1, t.required)
                    .regex(/^[A-Z]{2,3}-\d+$/i, t.format),
            }),
        )
        .min(1, t.minOne);
}

export default function PurchaseOrderInputs({
    control,
    fields,
    append,
    remove,
    name = 'po_numbers',
    translations,
}: PurchaseOrderInputsProps) {
    return (
        <Field>
            <FieldLabel>{translations.label}</FieldLabel>
            <div className="space-y-3">
                {fields.map((field, index) => (
                    <Controller
                        key={field.id}
                        name={`${name}.${index}.value`}
                        control={control}
                        render={({ field: inputField, fieldState }) => (
                            <div>
                                <div className="flex items-center gap-2">
                                    <Input
                                        {...inputField}
                                        id={`${name}_${index}`}
                                        aria-invalid={fieldState.invalid}
                                        placeholder={translations.placeholder}
                                    />
                                    {fields.length > 1 && (
                                        <Button
                                            type="button"
                                            variant="ghost"
                                            size="icon-sm"
                                            onClick={() => remove(index)}
                                            aria-label={translations.removeAriaLabel.replace(
                                                ':number',
                                                String(index + 1),
                                            )}
                                        >
                                            <Trash2Icon className="size-4 text-muted-foreground" />
                                        </Button>
                                    )}
                                </div>
                                {fieldState.invalid && (
                                    <FieldError
                                        errors={[fieldState.error]}
                                    />
                                )}
                            </div>
                        )}
                    />
                ))}
            </div>
            <Button
                type="button"
                variant="ghost"
                size="sm"
                className="mt-1.5 gap-1 text-xs text-muted-foreground"
                onClick={() => append({ value: '' })}
            >
                <PlusIcon className="size-3.5" />
                {translations.addMore}
            </Button>
        </Field>
    );
}
