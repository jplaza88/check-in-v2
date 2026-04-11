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
    });

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
            });

            return;
        }

        if (!navigator.geolocation) {
            console.warn('Geolocation is not supported by this browser.');
            setState((prev) => ({ ...prev, loading: false }));

            return;
        }

        const success = (pos: GeolocationPosition) => {
            saveDriverCoords(pos.coords.latitude, pos.coords.longitude);
            setState({
                coords: {
                    latitude: pos.coords.latitude,
                    longitude: pos.coords.longitude,
                },
                loading: false,
                error: null,
            });
        };

        const error = (err: GeolocationPositionError) => {
            setState({ coords: null, loading: false, error: err });
        };

        navigator.geolocation.getCurrentPosition(success, error, options);
    }, []);

    return state;
}
