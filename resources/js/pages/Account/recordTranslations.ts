export interface RecordTranslations {
    back: string;
    checkInTitle: string;
    appointmentTitle: string;
    downloadPdf: string;
    comingSoon: string;
    reference: string;
    status: string;
    date: string;
    time: string;
    locationHeading: string;
    address: string;
    shipmentHeading: string;
    customer: string;
    destination: string;
    purchaseOrders: string;
    equipmentHeading: string;
    truckName: string;
    truckPlate: string;
    truckColor: string;
    trailerPlate: string;
    trailerChute: string;
    emptyWeight: string;
    driverHeading: string;
    driverName: string;
    driverPhone: string;
    driverEmail: string;
    license: string;
    licenseState: string;
    licenseExpiration: string;
    loadingInstructions: string;
    cancelledOn: string;
    cancellationReason: string;
    statusPending: string;
    statusCompleted: string;
    statusCancelled: string;
    statusScheduled: string;
    statusNoShow: string;
    statusCheckedIn: string;
}

export function recordStatusLabel(
    status: string,
    t: RecordTranslations,
): string {
    const labels: Record<string, string> = {
        pending: t.statusPending,
        completed: t.statusCompleted,
        cancelled: t.statusCancelled,
        scheduled: t.statusScheduled,
        'no-show': t.statusNoShow,
        'checked-in': t.statusCheckedIn,
    };

    return labels[status] ?? status;
}
