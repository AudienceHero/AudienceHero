export default {
    en: {
        'ah.csv_import': {
            card: {
                title: 'CSV',
                subtitle: 'Import your contacts from a CSV file',
                button: 'Import my contacts',
            },
            input: {
                file: 'Chose a CSV file to import',
                group: 'Add the imported contact to this group',
                select_column: 'Match this column with a field',
            },
            column: {
                skip: 'Skip (Do not import)',
                email: 'Email address',
                full_name: 'Full name',
                first_name: 'First name',
                last_name: 'Last name',
                salutation_name: 'Nickname',
                address: 'Postal Address',
                postal_code: 'Postal Code',
                city: 'City',
                country: 'Country',
                phone: 'Phone',
                homepage: 'Homepage',
                company_name: 'Company Name',
                notes: 'Notes',
                facebook: 'Facebook',
                instagram: 'Instagram',
                mixcloud: 'Mixcloud',
                soundcloud: 'Soundcloud',
                twitter: 'Twitter',
            },
            validate: {
                match_required: 'Please select a field or skip.',
                duplicate_choice: 'You cannot match the same field twice',
                first_last_or_full_name:
                    'Please select either the full name or the first/last name',
            },
        },
    },
};
