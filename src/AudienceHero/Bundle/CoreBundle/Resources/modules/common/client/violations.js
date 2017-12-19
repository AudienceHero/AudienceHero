const keyViolations = /violations$/;
const keyPropertyPath = /propertyPath$/;
const keyMessage = /message$/;

export const extractViolations = json => {
    const violations = [];
    if (json === null || typeof json === 'undefined') {
        return violations;
    }

    Object.keys(json).forEach(key => {
        if (null === key.match(keyViolations)) {
            return;
        }

        json[key].forEach(item => {
            var property = '';
            var message = '';
            Object.keys(item).forEach(itemKey => {
                if (null !== itemKey.match(keyPropertyPath)) {
                    if (typeof item[itemKey] === 'object') {
                        property = item[itemKey][0]['@value'];
                    } else {
                        property = item[itemKey];
                    }
                }
                if (null !== itemKey.match(keyMessage)) {
                    if (typeof item[itemKey] === 'object') {
                        message = item[itemKey][0]['@value'];
                    } else {
                        message = item[itemKey];
                    }
                }
            });
            violations.push({ property, message });
        });
    });

    return violations;
};

export const transformViolations = body => {
    let json = body;
    if (!Array.isArray(json)) {
        json = [json];
    }

    const violations = [];

    json.forEach(item => {
        let list = extractViolations(item);
        list.forEach(item => {
            violations.push(item);
        });
    });

    var errors = {};
    violations.forEach(item => {
        errors[item.property] = item.message;
    });

    return errors;
};
