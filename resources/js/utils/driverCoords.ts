const STORAGE_KEY = 'driver_coords';
const TTL_MINUTES = 30;

interface StoredCoords {
    latitude: number;
    longitude: number;
    expiresAt: number; // Unix ms
}

export function saveDriverCoords(latitude: number, longitude: number): void {
    const payload: StoredCoords = {
        latitude,
        longitude,
        expiresAt: Date.now() + TTL_MINUTES * 60 * 1000,
    };

    localStorage.setItem(STORAGE_KEY, JSON.stringify(payload));
}

export function getDriverCoords(): StoredCoords | null {
    const raw = localStorage.getItem(STORAGE_KEY);

    if (!raw) {
        return null;
    }

    try {
        const stored: StoredCoords = JSON.parse(raw);

        if (Date.now() > stored.expiresAt) {
            clearDriverCoords();

            return null;
        }

        return stored;
    } catch {
        clearDriverCoords();

        return null;
    }
}

export function clearDriverCoords(): void {
    localStorage.removeItem(STORAGE_KEY);
}
