import type { ReactNode } from 'react';
import React from 'react';
import { useEffect, useState } from 'react';

interface ModalProps {
    id: string;
    modalOpen: boolean;
    onClose: () => void;
    children: ReactNode;
}

export default function Modal({
    id,
    modalOpen,
    onClose,
    children,
}: ModalProps) {
    const [show, setShow] = useState<boolean>(false);
    const [animate, setAnimate] = useState<boolean>(false);

    useEffect(() => {
        if (modalOpen) {
            // eslint-disable-next-line react-hooks/set-state-in-effect
            setShow(true);
            requestAnimationFrame(() => setAnimate(true));
        } else {
            setAnimate(false);
            const timer = setTimeout(() => setShow(false), 300);

            return () => clearTimeout(timer);
        }
    }, [modalOpen]);

    useEffect(() => {
        if (!modalOpen) {
            return;
        }

        const onKey = (e: KeyboardEvent) => e.key === 'Escape' && onClose();
        document.addEventListener('keydown', onKey);

        return () => document.removeEventListener('keydown', onKey);
    }, [modalOpen, onClose]);

    if (!show) {
        return null;
    }

    return (
        <>
            <div
                className={`fixed inset-0 z-50 bg-gray-900/30 transition-opacity duration-300 ${
                    animate ? 'opacity-100' : 'opacity-0'
                }`}
                aria-hidden="true"
            />

            <div
                id={id}
                role="dialog"
                aria-modal="true"
                className="fixed inset-0 z-50 flex items-center justify-center overflow-hidden px-4 sm:px-6"
                onClick={(e: React.MouseEvent<HTMLDivElement>) => {
                    if (e.target === e.currentTarget) {
                        onClose();
                    }
                }}
            >
                <div
                    className={`max-h-full w-full max-w-lg overflow-auto rounded-lg bg-white shadow-lg transition-all duration-300 ease-out dark:bg-gray-800 ${
                        animate ? 'scale-100 opacity-100' : 'scale-95 opacity-0'
                    }`}
                >
                    {children}
                </div>
            </div>
        </>
    );
}
