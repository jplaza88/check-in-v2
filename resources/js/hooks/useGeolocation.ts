import { usePage } from '@inertiajs/react';
import { useState, useEffect } from 'react';
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

const DEFAULT_OPTIONS: PositionOptions = {
    enableHighAccuracy: false,
    timeout: 10_000,
    maximumAge: 60_000,
};
export function useGeolocation(
    options: PositionOptions = DEFAULT_OPTIONS,
): GeolocationState {
    const [state, setState] = useState<GeolocationState>({
        coords: null,
        loading: true,
        error: null,
        warning: null,
    });

    const { userCoordsBrowserTtl } = usePage<{ userCoordsBrowserTtl: number }>()
        .props;

    useEffect(() => {
        const cached = getDriverCoords();

        if (cached) {
            // eslint-disable-next-line react-hooks/set-state-in-effect
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
    }, []);

    return state;
}
