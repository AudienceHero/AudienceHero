export default {
    en: {
        'ah.mailing': {
            menu: {
                mailings: 'Mailings',
            },
            field: {
                status: 'Status',
                reference: 'Reference',
                subject: 'Subject',
                contactsGroup: 'Group',
            },
            action: {
                boost: 'Boost',
                send_preview: 'Send preview',
                send: 'Send',
            },
            dialog: {
                send_preview: {
                    title: 'Send a preview to an email address',
                    explain:
                        'You can safely preview what the email will look like by sending a test email. Just enter a recipient email address and click the send button.',
                    input: {
                        test_recipient: 'Test email recipient address',
                    },
                    button: {
                        send_preview: 'Send preview',
                    },
                },
                send: {
                    title: 'Send this campaign',
                    explain:
                        'This will send this campaign to all the recipients of the chosen group.',
                    button: {
                        send: 'Send',
                    },
                },
                boost: {
                    title: 'Boost this campaign?',
                    explain:
                        'This will send an email to people who did not previously open this email. Do not abuse this feature!',
                    button: {
                        send_boost: 'Boost this campaign',
                    },
                },
            },
            rate: {
                delivery: 'Delivery rate',
                open: 'Open rate',
                click: 'Click rate',
                click_by_unique_open: 'Click by unique open',
            },
            stat: {
                delivered: 'delivered emails',
                non_delivered: 'non delivered emails',
                total_opens: 'total opens',
                unique_opens: 'unique opens',
                total_clicks: 'total clicks',
                unique_clicks: 'unique clicks',
            },
        },
    },
};
