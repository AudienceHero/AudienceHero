export const flattenBundleProperty = (bundles, property) => {
    const flattened = [];
    bundles.forEach(bundle => {
        if (!bundle.hasOwnProperty(property)) {
            return;
        }

        if (Array.isArray(bundle[property])) {
            bundle[property].forEach(prop => {
                flattened.push(prop);
            });
        } else {
            flattened.push(bundle[property]);
        }
    });

    return flattened;
};

export const flattenReducers = bundles => {
    const reducers = {};

    bundles.forEach(bundle => {
        if (!bundle.hasOwnProperty('reducer')) {
            return;
        }

        Object.keys(bundle.reducer).forEach(key => {
            reducers[key] = bundle.reducer[key];
        });
    });

    return reducers;
};
