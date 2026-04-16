(function () {
    const DATA_URL = window.INDIA_LOCATION_DATA_URL || '/assets/data/statescities.json';
    let locationsPromise = null;

    function loadLocations() {
        if (!locationsPromise) {
            locationsPromise = fetch(DATA_URL, {
                headers: {
                    Accept: 'application/json',
                },
            }).then((response) => {
                if (!response.ok) {
                    throw new Error('Unable to load India location data.');
                }

                return response.json();
            });
        }

        return locationsPromise;
    }

    function uniqueSorted(values) {
        return [...new Set(values.filter(Boolean))].sort((a, b) => a.localeCompare(b));
    }

    function createOption(label, value, selected = false) {
        const option = document.createElement('option');
        option.value = value;
        option.textContent = label;
        option.selected = selected;
        return option;
    }

    function populateStateSelect(stateSelect, states, selectedState) {
        stateSelect.innerHTML = '';
        stateSelect.appendChild(createOption('Select state', ''));

        if (selectedState && !states.includes(selectedState)) {
            stateSelect.appendChild(createOption(selectedState, selectedState, true));
        }

        states.forEach((state) => {
            stateSelect.appendChild(createOption(state, state, state === selectedState));
        });
    }

    function populateCitySelect(citySelect, cities, selectedCity, stateSelected) {
        citySelect.innerHTML = '';
        citySelect.appendChild(createOption('Select city', ''));

        if (!stateSelected) {
            citySelect.disabled = true;
            return;
        }

        citySelect.disabled = false;

        if (selectedCity && !cities.includes(selectedCity)) {
            citySelect.appendChild(createOption(selectedCity, selectedCity, true));
        }

        cities.forEach((city) => {
            citySelect.appendChild(createOption(city, city, city === selectedCity));
        });
    }

    function initPicker(container, locations) {
        const stateSelect = container.querySelector('select[name="state"]');
        const citySelect = container.querySelector('select[name="city"]');
        const countryInput = container.querySelector('input[name="country"]');

        if (!stateSelect || !citySelect) {
            return;
        }

        const selectedState = (container.dataset.selectedState || stateSelect.dataset.selected || stateSelect.value || '').trim();
        const selectedCity = (container.dataset.selectedCity || citySelect.dataset.selected || citySelect.value || '').trim();
        const countryValue = container.dataset.countryValue || 'India';

        if (countryInput) {
            countryInput.value = countryValue;
            countryInput.readOnly = true;
        }

        const states = uniqueSorted(locations.map((item) => item.state));
        const citiesByState = locations.reduce((accumulator, item) => {
            if (!accumulator[item.state]) {
                accumulator[item.state] = [];
            }

            accumulator[item.state].push(item.city);
            return accumulator;
        }, {});

        Object.keys(citiesByState).forEach((state) => {
            citiesByState[state] = uniqueSorted(citiesByState[state]);
        });

        populateStateSelect(stateSelect, states, selectedState);
        populateCitySelect(citySelect, citiesByState[selectedState] || [], selectedCity, Boolean(selectedState));

        stateSelect.addEventListener('change', function () {
            const nextState = this.value.trim();
            const nextCities = citiesByState[nextState] || [];

            populateCitySelect(citySelect, nextCities, '', Boolean(nextState));
        });
    }

    document.addEventListener('DOMContentLoaded', async function () {
        const pickers = document.querySelectorAll('[data-india-location-picker]');
        if (!pickers.length) {
            return;
        }

        try {
            const locations = await loadLocations();
            pickers.forEach((picker) => initPicker(picker, locations));
        } catch (error) {
            console.error(error);
        }
    });
})();
