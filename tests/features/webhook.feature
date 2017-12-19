Feature: The webhooks works

    Scenario: A POST request to the mailgun webhooks fails with status code 400
        Given I send a "POST" request to "/webhooks/mailgun" with parameters:
            | key       | value |
        Then the response status code should be 200

    Scenario: A POST succeeds with status code 200
        Given I send a "POST" request to "/webhooks/mailgun" with parameters:
            | key       | value |
            | ah_message_id | {"id": "foobar"} |
        Then the response status code should be 200

    Scenario: A POST succeeds with status code 200
        Given I send a "POST" request to "/webhooks/mailgun" with parameters:
            | key       | value |
            | event     | bounced |
            | timestamp | 1461669842 |
            | mopro_message_id | {"id": "mailing_futurecat_sent_emails1"} |
        Then the response status code should be 200

    Scenario: Stripe webhooks succeeds if customer ID exists
        Given I send a "POST" request to "/webhooks/stripe" with body:
        """
        {
            "type": "customer.subscription.trial_will_end",
            "data": {
                "object": {
                    "customer": "futurecat_stripeId",
                    "trial_end": "1586614866"
                }
            }
        }
        """
        Then the response status code should be 200

    Scenario: Stripe webhooks fails if customer ID does not exist
        Given I send a "POST" request to "/webhooks/stripe" with body:
        """
        {
            "type": "customer.subscription.trial_will_end",
            "data": {
                "object": {
                    "customer": "doesnotexist_stripeId",
                    "trial_end": "1586614866"
                }
            }
        }
        """
        Then the response status code should be 500
