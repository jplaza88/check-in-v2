import { usePage } from '@inertiajs/react';
import type { PropsWithChildren } from 'react';
import React, { useState } from 'react';

import Footer from '@/components/Footer';
import Modal from '@/components/Modal';
import Navbar from '@/components/Navbar';

type PublicLayoutProps = PropsWithChildren;

interface LocationRequiredModal {
    title: string;
    message: string;
}

interface Translations {
    locationRequiredModal: LocationRequiredModal;
}

interface PageProps {
    currentRoute?: string;
    translations: Translations;
    [key: string]: unknown;
}

export default function PublicLayout({ children }: PublicLayoutProps) {
    const { currentRoute, translations } = usePage<PageProps>().props;

    const [infoModalOpen, setInfoModalOpen] = useState<boolean>(true);

    return (
        <main className="bg-white dark:bg-gray-900">
            <div className="relative flex">
                {/* Left-side content */}
                <div className="w-full md:w-[60%]">
                    <div className="flex h-full min-h-dvh flex-col">
                        <div className="shrink-0">
                            <Navbar />
                        </div>

                        <div className="flex flex-1 items-center px-4 py-4">
                            <div className="mx-auto w-full max-w-md lg:max-w-2xl">
                                {children}
                            </div>
                        </div>

                        <div className="shrink-0">
                            <Footer />
                        </div>
                    </div>
                </div>

                {/* Right-side background image */}
                <div
                    aria-hidden
                    className="pointer-events-none absolute inset-y-0 right-0 hidden w-[40%] bg-side-image bg-cover bg-center bg-no-repeat md:block"
                />

                {/* Info Modal */}
                {currentRoute === 'checkIn.selectLocation' && infoModalOpen && (
                    <div className="m-1.5">
                        <Modal
                            id="info-modal"
                            modalOpen={infoModalOpen}
                            onClose={() => setInfoModalOpen(false)}
                        >
                            <div className="flex space-x-4 p-5">
                                <div className="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-700">
                                    <svg
                                        className="shrink-0 fill-current text-brand-green"
                                        width="16"
                                        height="16"
                                        viewBox="0 0 16 16"
                                    >
                                        <path d="M8 0C3.6 0 0 3.6 0 8s3.6 8 8 8 8-3.6 8-8-3.6-8-8-8zm1 12H7V7h2v5zM8 6c-.6 0-1-.4-1-1s.4-1 1-1 1 .4 1 1-.4 1-1 1z" />
                                    </svg>
                                </div>

                                <div>
                                    <div className="mb-2">
                                        <div className="text-lg font-semibold text-gray-800 dark:text-gray-100">
                                            {
                                                translations
                                                    .locationRequiredModal.title
                                            }
                                        </div>
                                    </div>

                                    <div className="mb-6 text-sm">
                                        <div className="space-y-2">
                                            <p className="text-gray-800 dark:text-gray-100">
                                                {
                                                    translations
                                                        .locationRequiredModal
                                                        .message
                                                }
                                            </p>
                                        </div>
                                    </div>

                                    <div className="flex flex-wrap justify-end space-x-2">
                                        <button
                                            onClick={() =>
                                                setInfoModalOpen(false)
                                            }
                                            className="btn-sm mt-5 inline-flex cursor-pointer items-center justify-center rounded-lg border border-transparent bg-gray-900 px-4 py-2 text-gray-100 hover:bg-gray-800 dark:bg-gray-100 dark:text-gray-800 dark:hover:bg-white"
                                        >
                                            OK
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </Modal>
                    </div>
                )}
            </div>
        </main>
    );
}
