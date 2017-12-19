import { transformJsonLdToAOR } from './hydraClient';

/**
 * @copyright Kevin Dunglas
 * @see https://github.com/api-platform/admin
 */
describe('map a json-ld document to an admin on rest compatible document', function() {
    const jsonLdDocument = {
        '@id': '/reviews/327',
        id: 327,
        '@type': 'http://schema.org/Review',
        reviewBody:
            'Accusantium quia ipsam omnis praesentium. Neque quidem omnis perspiciatis sed. Officiis quo dolor esse nisi molestias.',
        rating: 3,
        itemReviewed: {
            '@id': '/books/2',
            id: 2,
            '@type': 'http://schema.org/Book',
            isbn: '9792828761393',
            name: '000',
            description: 'string',
            author: 'string',
            dateCreated: '2017-04-25T00:00:00+00:00',
        },
    };

    describe('transform only the main document when called with a max depth of 1', function() {
        const AORDocument = transformJsonLdToAOR(1)(jsonLdDocument);

        test('add an id property equal to the numeric value of @id property', () => {
            expect(AORDocument.id).toEqual('327');
        });

        test('preserve the previous id property value in a new originId property', () => {
            expect(AORDocument.originId).toEqual(jsonLdDocument['id']);
        });

        test('do not alter the embedded document', () => {
            expect(AORDocument.id).toEqual('327');
        });
    });

    describe('transform the embedded document when called with a max depth of 2', function() {
        const AORDocument = transformJsonLdToAOR()(jsonLdDocument);

        test('add an id property on the embedded document equal to the @id property of the embedded document', () => {
            expect(AORDocument.itemReviewed.id).toEqual('2');
        });
    });
});

describe('map a long json-ld document to an AOR compatible list of document', () => {
    const jsonLdDocument = {
        '@context': '/api/contexts/ContactsGroupForm',
        '@id': '/api/contacts_group_forms',
        '@type': 'hydra:Collection',
        'hydra:member': [
            {
                '@id':
                    '/api/contacts_group_forms/042b85aa-09f9-46f0-9196-92a9853908ec',
                '@type': 'ContactsGroupForm',
                name: 'fdsfasdfFoobar',
                description: null,
                contactsGroup: {
                    '@id':
                        '/api/contacts_groups/638252cc-f7ea-47aa-848e-b98ccdafaf65',
                    '@type': 'ContactsGroup',
                    name: 'Pros',
                    description: 'Your contacts in the music business',
                    createdAt: '2017-02-12T08:47:54+00:00',
                    updatedAt: '2017-05-12T08:47:55+00:00',
                    owner: {
                        '@id':
                            '/api/users/b2559645-3b3a-4693-950a-22341566f96e',
                        '@type': 'User',
                        id: 'b2559645-3b3a-4693-950a-22341566f96e',
                        username: 'futurecat',
                        email: 'futurecat@example.com',
                        createdAt: '2017-05-12T08:47:54+00:00',
                        updatedAt: '2017-08-08T08:36:04+00:00',
                    },
                },
                image: null,
                askEmail: true,
                askName: true,
                askCity: true,
                askCountry: true,
                askPhone: true,
                displayQRCode: true,
                createdAt: '2017-08-07T16:23:01+00:00',
                updatedAt: '2017-08-07T16:23:01+00:00',
                owner: {
                    '@id': '/api/users/b2559645-3b3a-4693-950a-22341566f96e',
                    '@type': 'User',
                    id: 'b2559645-3b3a-4693-950a-22341566f96e',
                    username: 'futurecat',
                    email: 'futurecat@example.com',
                    createdAt: '2017-05-12T08:47:54+00:00',
                    updatedAt: '2017-08-08T08:36:04+00:00',
                },
            },
        ],
        'hydra:totalItems': 1,
        'hydra:view': {
            '@id': '/api/contacts_group_forms?order%5Bid%5D=DESC',
            '@type': 'hydra:PartialCollectionView',
        },
    };

    test('conversion works on depth', () => {
        const document = jsonLdDocument['hydra:member'].map(
            transformJsonLdToAOR()
        );
        console.log(document);
    });
});
