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

export default function PrivacyPolicy() {
    return (
        <PublicLayout>
            <Head title="Privacy Policy" />

            <LegalDocument
                eyebrow="Legal"
                title="Privacy Policy"
                effectiveDate="July 18, 2026"
                intro={
                    <p>
                        This Privacy Policy explains how{' '}
                        <Placeholder>[COMPANY LEGAL NAME]</Placeholder> (“we,”
                        “us,” or “our”) collects, uses, shares, and protects
                        information in connection with {appName} (the
                        “Service”), a platform that lets truck drivers check in
                        and schedule appointments at our distribution centers.
                        By using the Service, you agree to the practices
                        described here.
                    </p>
                }
            >
                <LegalSection number={1} title="Who this policy applies to">
                    <p>
                        This policy applies to drivers, carrier personnel, and
                        other visitors who use the Service to check in, request
                        or book an appointment, or otherwise interact with our
                        check-in and scheduling features. It does not cover
                        third-party websites or services that we do not control.
                    </p>
                    <p>
                        The Service is offered at our distribution centers in
                        Florida, California, Arizona, Pennsylvania, and Texas,
                        and is intended for use in the United States and governed
                        by United States law.
                    </p>
                </LegalSection>

                <LegalSection number={2} title="Information we collect">
                    <p>We collect the following categories of information:</p>
                    <p className="font-semibold text-brand-grey dark:text-gray-200">
                        Location information
                    </p>
                    <LegalList
                        items={[
                            'Your device’s precise geographic coordinates (latitude and longitude), which you provide through your browser when you begin a check-in. We use this to confirm that you are physically near the facility. These coordinates are held only briefly (a matter of minutes) for that verification and are not used to track you over time.',
                        ]}
                    />
                    <p className="font-semibold text-brand-grey dark:text-gray-200">
                        Driver and identity information
                    </p>
                    <LegalList
                        items={[
                            'Driver’s full name and mobile phone number.',
                            'Driver’s license number, its expiration date, and the issuing state/province and country.',
                        ]}
                    />
                    <p className="font-semibold text-brand-grey dark:text-gray-200">
                        Vehicle and shipment information
                    </p>
                    <LegalList
                        items={[
                            'Truck and trailer license plates, and where applicable their issuing state/province and country, truck color, and empty vehicle weight.',
                            'Customer name, destination, and purchase-order (PO) numbers associated with your load.',
                            'Loading instructions and similar details you enter.',
                        ]}
                    />
                    <p className="font-semibold text-brand-grey dark:text-gray-200">
                        Technical information
                    </p>
                    <LegalList
                        items={[
                            'Session cookies and similar technologies that are strictly necessary to operate the check-in and appointment flow (for example, to remember your selected location while you complete a form).',
                            'Basic technical data such as your preferred language.',
                        ]}
                    />
                </LegalSection>

                <LegalSection number={3} title="How we use your information">
                    <p>We use the information above to:</p>
                    <LegalList
                        items={[
                            'Verify that you are near the facility and are eligible to check in.',
                            'Process and manage your check-in or appointment.',
                            'Notify facility and shipping staff that you have arrived or booked, so they can prepare for your load.',
                            'Communicate with you about your check-in or appointment, including by email or text message where you have provided contact details.',
                            'Maintain security, prevent fraud or misuse, and keep accurate operational records.',
                            'Comply with legal, safety, and regulatory obligations.',
                        ]}
                    />
                </LegalSection>

                <LegalSection number={4} title="How we share your information">
                    <p>
                        We do not sell your personal information. We share it
                        only as needed to operate the Service:
                    </p>
                    <LegalList
                        items={[
                            'With our facility, shipping, and operations personnel who manage check-ins and appointments.',
                            'With service providers that help us run the Service — such as cloud hosting, email delivery, and (where applicable) text-message (SMS) providers — who may process information only on our behalf and under confidentiality obligations.',
                            'When required by law, legal process, or to protect the rights, property, or safety of our people, drivers, or the public.',
                            'In connection with a business transfer, such as a merger, acquisition, or sale of assets, subject to this policy.',
                        ]}
                    />
                </LegalSection>

                <LegalSection number={5} title="Location data">
                    <p>
                        Location access is used solely to confirm proximity to
                        the facility during check-in. You can decline to share
                        location through your browser or device settings;
                        however, because proximity verification is a core part
                        of check-in, you may be unable to complete a check-in
                        without it.
                    </p>
                </LegalSection>

                <LegalSection number={6} title="Cookies and similar technologies">
                    <p>
                        We use only cookies and similar technologies that are
                        strictly necessary to provide the Service and remember
                        your progress through a check-in or appointment. We do
                        not use them for advertising.{' '}
                        <Placeholder>
                            [Confirm whether any analytics or non-essential
                            cookies are used; if so, list them here.]
                        </Placeholder>
                    </p>
                </LegalSection>

                <LegalSection number={7} title="Data retention">
                    <p>
                        We keep check-in and appointment records for as long as
                        necessary to fulfill the purposes described in this
                        policy, to maintain operational and business records,
                        and to comply with our legal obligations, after which we
                        delete or de-identify them.{' '}
                        <Placeholder>
                            [Specify a retention period if you have one, e.g.
                            “check-in records are retained for 24 months.”]
                        </Placeholder>{' '}
                        Precise location coordinates used for proximity checks
                        are retained only briefly and are not stored long-term.
                    </p>
                </LegalSection>

                <LegalSection number={8} title="How we protect your information">
                    <p>
                        We use reasonable administrative, technical, and
                        organizational safeguards designed to protect your
                        information against unauthorized access, use, or
                        disclosure. These measures include encryption of certain
                        sensitive information in transit and at rest. No method
                        of transmission or storage is completely secure, however,
                        and we cannot guarantee absolute security.
                    </p>
                </LegalSection>

                <LegalSection number={9} title="Your privacy rights">
                    <p>
                        Depending on your state of residence, you may have some
                        or all of the following rights regarding the personal
                        information we hold about you:
                    </p>
                    <LegalList
                        items={[
                            'Access — confirm whether we process your personal information and request a copy of it.',
                            'Correct — ask us to fix inaccurate personal information.',
                            'Delete — ask us to delete personal information we collected from you.',
                            'Portability — obtain a copy of certain information in a portable format.',
                            'Opt out — opt out of the “sale” of personal information or targeted advertising. We do not sell your personal information and do not use it for targeted advertising.',
                        ]}
                    />
                    <p>
                        To exercise any of these rights, contact us using the
                        details below. We will respond within the timeframe
                        required by applicable law and may need to verify your
                        identity first. You will not be discriminated against for
                        exercising your rights.
                    </p>
                    <p className="font-semibold text-brand-grey dark:text-gray-200">
                        California
                    </p>
                    <p>
                        The California Consumer Privacy Act, as amended by the
                        CPRA, gives California residents the rights described
                        above, including the right to know, access, correct, and
                        delete personal information and to limit the use of
                        sensitive personal information (such as precise
                        geolocation and driver’s license number). We do not sell
                        or share personal information for cross-context
                        behavioral advertising.
                    </p>
                    <p className="font-semibold text-brand-grey dark:text-gray-200">
                        Texas
                    </p>
                    <p>
                        The Texas Data Privacy and Security Act gives Texas
                        residents the rights described above, along with the
                        right to appeal a denial of a request. If we deny your
                        request, you may appeal by contacting us; if your appeal
                        is denied, you may then contact the Texas Attorney
                        General.
                    </p>
                    <p className="font-semibold text-brand-grey dark:text-gray-200">
                        Florida
                    </p>
                    <p>
                        Florida’s Digital Bill of Rights provides similar rights
                        but applies only to businesses that meet its high revenue
                        and other thresholds.{' '}
                        <Placeholder>
                            [Confirm with counsel whether the FDBR applies to
                            your business; if it does, Florida residents have the
                            rights described above.]
                        </Placeholder>
                    </p>
                    <p className="font-semibold text-brand-grey dark:text-gray-200">
                        Arizona and Pennsylvania
                    </p>
                    <p>
                        Arizona and Pennsylvania do not currently have
                        comprehensive consumer privacy laws granting these
                        specific rights. Even so, we honor reasonable requests to
                        access, correct, or delete your information, and we
                        comply with applicable Arizona and Pennsylvania law,
                        including data-breach notification requirements.
                    </p>
                </LegalSection>

                <LegalSection number={10} title="United States only">
                    <p>
                        Although some drivers may travel from Canada or Mexico,
                        and license or plate information may originate outside the
                        United States, the Service is operated from and intended
                        for use in the United States. We handle information in
                        accordance with United States federal law and the
                        applicable laws of the states in which we operate. We do
                        not undertake to comply with the data-protection laws of
                        other countries, and by using the Service you understand
                        that your information is processed in the United States.
                    </p>
                </LegalSection>

                <LegalSection number={11} title="Children’s privacy">
                    <p>
                        The Service is intended for commercial drivers and other
                        adults acting in a business capacity. It is not directed
                        to children, and we do not knowingly collect personal
                        information from anyone under 18.
                    </p>
                </LegalSection>

                <LegalSection number={12} title="Changes to this policy">
                    <p>
                        We may update this Privacy Policy from time to time. When
                        we do, we will revise the “Effective date” above and, if
                        the changes are material, provide additional notice as
                        required by law. Your continued use of the Service after
                        an update means you accept the revised policy.
                    </p>
                </LegalSection>

                <LegalSection number={13} title="Contact us">
                    <LegalCallout title="Questions about your privacy?">
                        <p>
                            You can reach us at{' '}
                            <Placeholder>[privacy@example.com]</Placeholder> or
                            by mail at{' '}
                            <Placeholder>[BUSINESS MAILING ADDRESS]</Placeholder>
                            .
                        </p>
                    </LegalCallout>
                </LegalSection>
            </LegalDocument>
        </PublicLayout>
    );
}
