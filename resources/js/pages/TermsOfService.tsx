import { Head } from '@inertiajs/react';

import {
    LegalCallout,
    LegalDocument,
    LegalList,
    LegalSection,
    Placeholder,
} from '@/components/LegalDocument';
import PublicLayout from '@/layouts/PublicLayout';

const appName = (import.meta.env.VITE_APP_NAME as string) ?? 'the Service';

export default function TermsOfService() {
    return (
        <PublicLayout>
            <Head title="Terms of Service" />

            <LegalDocument
                eyebrow="Legal"
                title="Terms of Service"
                effectiveDate="July 18, 2026"
                intro={
                    <p>
                        These Terms of Service (“Terms”) govern your use of{' '}
                        {appName} (the “Service”), operated by{' '}
                        <Placeholder>[COMPANY LEGAL NAME]</Placeholder> (“we,”
                        “us,” or “our”). Please read them carefully. By accessing
                        or using the Service, you agree to be bound by these
                        Terms. If you do not agree, do not use the Service.
                    </p>
                }
            >
                <LegalSection number={1} title="The Service">
                    <p>
                        The Service allows truck drivers and authorized carrier
                        personnel to check in on arrival at our distribution
                        centers, verify proximity to a facility, schedule
                        appointments, and provide the shipment and vehicle
                        information needed to process a load.
                    </p>
                </LegalSection>

                <LegalSection number={2} title="Eligibility">
                    <p>
                        You must be at least 18 years old and using the Service
                        in a commercial or business capacity — for example, as a
                        driver or on behalf of a carrier. By using the Service,
                        you represent that you meet these requirements and that
                        you are authorized to provide the information you submit.
                    </p>
                </LegalSection>

                <LegalSection number={3} title="Accounts">
                    <p>
                        Some features may require an account. If you create one,
                        you are responsible for keeping your credentials
                        confidential and for all activity under your account. You
                        agree to provide accurate account information and to keep
                        it up to date.
                    </p>
                </LegalSection>

                <LegalSection number={4} title="Accuracy of information you provide">
                    <p>
                        You agree that the information you submit — including your
                        name, phone number, driver’s license details, vehicle
                        and trailer plates, customer, destination, and
                        purchase-order numbers — is true, accurate, and complete.
                        Providing false or misleading information may result in a
                        denied or revoked check-in and may violate facility
                        rules or law.
                    </p>
                </LegalSection>

                <LegalSection number={5} title="Location services">
                    <p>
                        Check-in relies on your device sharing its location to
                        confirm you are near the facility. You are responsible
                        for enabling location access when prompted. If you
                        decline, you may be unable to complete a check-in.
                    </p>
                </LegalSection>

                <LegalSection
                    number={6}
                    title="Text message (SMS) and email communications"
                >
                    <p>
                        By providing your mobile phone number or email address,
                        you consent to receive communications from us related to
                        your check-in or appointment, which may include text
                        messages and emails. Message and data rates may apply.
                        You can opt out of non-essential text messages by
                        replying STOP, though we may still send messages
                        necessary to operate a check-in or appointment.{' '}
                        <Placeholder>
                            [Confirm SMS program details and opt-out keyword with
                            your SMS provider before enabling texting.]
                        </Placeholder>
                    </p>
                </LegalSection>

                <LegalSection number={7} title="Acceptable use">
                    <p>You agree not to:</p>
                    <LegalList
                        items={[
                            'Use the Service for any unlawful, fraudulent, or unauthorized purpose.',
                            'Submit information that belongs to someone else without authorization.',
                            'Interfere with, disrupt, or attempt to gain unauthorized access to the Service or its systems.',
                            'Use automated means to access the Service in a way that burdens or disrupts it.',
                            'Attempt to bypass proximity, scheduling, or other check-in controls.',
                        ]}
                    />
                </LegalSection>

                <LegalSection number={8} title="Intellectual property">
                    <p>
                        The Service, including its software, design, and content,
                        is owned by us or our licensors and is protected by
                        applicable laws. We grant you a limited, non-exclusive,
                        non-transferable, revocable license to use the Service
                        for its intended purpose. You may not copy, modify,
                        distribute, or create derivative works except as
                        permitted by these Terms.
                    </p>
                </LegalSection>

                <LegalSection number={9} title="Third-party services">
                    <p>
                        The Service may rely on third-party providers (such as
                        hosting, email, and messaging services). We are not
                        responsible for the content, policies, or practices of
                        third parties, and your use of their services may be
                        subject to their own terms.
                    </p>
                </LegalSection>

                <LegalSection number={10} title="Disclaimers">
                    <p>
                        The Service is provided “as is” and “as available,”
                        without warranties of any kind, whether express or
                        implied, including implied warranties of merchantability,
                        fitness for a particular purpose, and non-infringement.
                        We do not warrant that the Service will be uninterrupted,
                        error-free, or secure.
                    </p>
                </LegalSection>

                <LegalSection number={11} title="Limitation of liability">
                    <p>
                        To the fullest extent permitted by law, we will not be
                        liable for any indirect, incidental, special,
                        consequential, or punitive damages, or any loss of
                        profits, revenues, data, or goodwill, arising out of or
                        related to your use of (or inability to use) the Service.
                        Our total liability for any claim relating to the Service
                        will not exceed{' '}
                        <Placeholder>[e.g., US $100]</Placeholder>.
                    </p>
                </LegalSection>

                <LegalSection number={12} title="Indemnification">
                    <p>
                        You agree to indemnify and hold us harmless from any
                        claims, damages, liabilities, and expenses (including
                        reasonable legal fees) arising out of your use of the
                        Service, your violation of these Terms, or your violation
                        of any law or the rights of a third party.
                    </p>
                </LegalSection>

                <LegalSection number={13} title="Governing law and disputes">
                    <p>
                        These Terms are governed by the laws of the State of{' '}
                        <Placeholder>Arizona</Placeholder>, United States,
                        without regard to its conflict-of-laws rules. You agree
                        that any dispute arising out of or relating to these
                        Terms or the Service will be resolved in the state or
                        federal courts located in{' '}
                        <Placeholder>
                            [COUNTY], Arizona
                        </Placeholder>
                        , and you consent to their jurisdiction.
                    </p>
                </LegalSection>

                <LegalSection number={14} title="Termination">
                    <p>
                        We may suspend or terminate your access to the Service at
                        any time, with or without notice, if we believe you have
                        violated these Terms or to protect the Service, our
                        people, or others. Provisions that by their nature should
                        survive termination will do so.
                    </p>
                </LegalSection>

                <LegalSection number={15} title="Changes to these Terms">
                    <p>
                        We may update these Terms from time to time. When we do,
                        we will revise the “Effective date” above. Your continued
                        use of the Service after an update means you accept the
                        revised Terms.
                    </p>
                </LegalSection>

                <LegalSection number={16} title="Contact us">
                    <LegalCallout title="Questions about these Terms?">
                        <p>
                            You can reach us at{' '}
                            <Placeholder>[legal@example.com]</Placeholder> or by
                            mail at{' '}
                            <Placeholder>[BUSINESS MAILING ADDRESS]</Placeholder>
                            .
                        </p>
                    </LegalCallout>
                </LegalSection>
            </LegalDocument>
        </PublicLayout>
    );
}
