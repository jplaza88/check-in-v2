export interface CheckInRowData {
    key: string;
    uuid: string;
    referenceNumber: string;
    locationName: string;
    customer: string;
    date: string;
    status: string;
    href: string;
}

export interface AppointmentRowData {
    key: string;
    uuid: string;
    referenceNumber: string;
    locationName: string;
    date: string;
    time: string;
    monthShort: string;
    day: string;
    status: string;
    isUpcoming: boolean;
    href: string;
}

export type HistoryRowData = CheckInRowData | AppointmentRowData;

export interface HistoryPage<T = HistoryRowData> {
    data: T[];
    currentPage: number;
    hasMore: boolean;
}
