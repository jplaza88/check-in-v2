import { usePage } from '@inertiajs/react';
import { useState, useEffect, useCallback } from 'react';
import { getDriverCoords, saveDriverCoords } from '@/utils/driverCoords';
interface Coords {
    latitude: number;
    longitude: number;
}
interface GeolocationState {
    coords: Coords | null;
    loading: boolean;
    error: GeolocationPositionError | null;
    warning: string | null;
}

interface GeolocationResult extends GeolocationState {
    /**
     * Re-runs the lookup. Denying the browser prompt is otherwise a dead end, since the effect below
     * only fires on mount and recovering would mean changing a browser setting and reloading.
     */
    retry: () => void;
}

const DEFAULT_OPTIONS: PositionOptions = {
    enableHighAccuracy: false,
    timeout: 10_000,
    maximumAge: 60_000,
};
export function useGeolocation(
    options: PositionOptions = DEFAULT_OPTIONS,
): GeolocationResult {
    const [state, setState] = useState<GeolocationState>({
        coords: null,
        loading: true,
        error: null,
        warning: null,
    });

    const { userCoordsBrowserTtl } = usePage<{ userCoordsBrowserTtl: number }>()
        .props;

    const request = useCallback(() => {
        const cached = getDriverCoords();

        if (cached) {
            setState({
                coords: {
                    latitude: cached.latitude,
                    longitude: cached.longitude,
                },
                loading: false,
                error: null,
                warning: null,
            });

            return;
        }

        // For testing purposes
        //Object.defineProperty(navigator, 'geolocation', { get: () => undefined });

        if (!navigator.geolocation) {
            const message = 'Geolocation is not supported by this browser.';
            console.warn(message);
            setState({
                coords: null,
                loading: false,
                error: null,
                warning: message,
            });

            return;
        }

        setState({ coords: null, loading: true, error: null, warning: null });

        const success = (pos: GeolocationPosition) => {
            saveDriverCoords(
                pos.coords.latitude,
                pos.coords.longitude,
                userCoordsBrowserTtl,
            );
            setState({
                coords: {
                    latitude: pos.coords.latitude,
                    longitude: pos.coords.longitude,
                },
                loading: false,
                error: null,
                warning: null,
            });
        };

        const error = (err: GeolocationPositionError) => {
            setState({ coords: null, loading: false, error: err, warning: null });
            console.error(err);
        };

        navigator.geolocation.getCurrentPosition(success, error, options);
    }, [options, userCoordsBrowserTtl]);

    // Mount only. `request` is stable for the current caller, but depending on it would re-prompt on
    // every render for anyone who passes an inline options object, so the prompt stays pinned to mount
    // and re-running it is left to the explicit retry below.
    useEffect(() => {
        // eslint-disable-next-line react-hooks/set-state-in-effect
        request();
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, []);

    return { ...state, retry: request };
}
