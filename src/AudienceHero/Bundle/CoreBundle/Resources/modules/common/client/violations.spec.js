import { transformViolations, extractViolations } from './violations';

describe('test violations extraction', () => {
    const violationsResponse = `{
    "@context": "\/app_dev.php\/api\/contexts\/ConstraintViolationList",
    "@type":"ConstraintViolationList",
    "hydra:title":"An error occurred",
    "hydra:description":"contactsGroup: This value should not be null.",
    "violations": [
        {
            "propertyPath":"contactsGroup",
            "message":"This value should not be null."
        }
    ]
}`;

    test('transformViolations', () => {
        const json = JSON.parse(violationsResponse);
        const list = transformViolations(json);

        expect(list).toEqual({
            contactsGroup: 'This value should not be null.',
        });
    });

    test('extractViolations', () => {
        const json = JSON.parse(violationsResponse);
        const list = extractViolations(json);

        expect(list).toEqual([
            {
                property: 'contactsGroup',
                message: 'This value should not be null.',
            },
        ]);
    });

    test('transformViolations array', () => {
        const json = JSON.parse(`[${violationsResponse}]`);
        const list = transformViolations(json);

        expect(list).toEqual({
            contactsGroup: 'This value should not be null.',
        });
    });
});

describe('test violations extraction with another format', () => {
    const violationsResponse = `{
        "http://www.audiencehero.app.dev:8080/api/docs.jsonld#violations": [
            {
                "http://www.audiencehero.app.dev:8080/api/docs.jsonld#message": [{"@value":"This value should not be blank."}],
                "http://www.audiencehero.app.dev:8080/api/docs.jsonld#propertyPath": [{"@value":"title"}]
            },
            {
                "http://www.audiencehero.app.dev:8080/api/docs.jsonld#message":[{"@value":"This value should not be blank."}],
                "http://www.audiencehero.app.dev:8080/api/docs.jsonld#propertyPath":[{"@value":"description"}]
            },
            {
                "http://www.audiencehero.app.dev:8080/api/docs.jsonld#message":[{"@value":"This value should not be blank."}],
                "http://www.audiencehero.app.dev:8080/api/docs.jsonld#propertyPath":[{"@value":"fromName"}]
            },
            {
                "http://www.audiencehero.app.dev:8080/api/docs.jsonld#message":[{"@value":"This value should not be null."}],
                "http://www.audiencehero.app.dev:8080/api/docs.jsonld#propertyPath":[{"@value":"fromEmail"}]
            }
        ]
    }`;

    test('transformViolations', () => {
        const json = JSON.parse(violationsResponse);
        const list = transformViolations(json);

        expect(list).toEqual({
            title: 'This value should not be blank.',
            description: 'This value should not be blank.',
            fromName: 'This value should not be blank.',
            fromEmail: 'This value should not be null.',
        });
    });
});
