Feature: A User can login, logout and register

    @javascript
    Scenario: As a User I can login and logout
        Given I am on "/admin"
        And I click the ".logout" element
        Then I should see "Create your account"
        And I fill in "username" with "alice"
        And I fill in "password" with "foobar"
        And I click the ".ah-button-login" element
        Then I wait for "Dashboard"
        When I click the ".logout" element
        Then I should see "Create your account"

    @javascript
    Scenario: As a User I can login from the homepage with username and logout
        Given I am on the homepage
        And I follow "link.login"
        When I fill in "username" with "futurecat"
        And I fill in "password" with "foobar"
        And I press "security.login.submit"
        Then I should see "title.dashboard"
        When I follow "link.logout"
        Then I should see "link.features"
        And I should see "link.pricing"
        And I should see "link.contact"

    Scenario: As a user, I can request a new password
        Given I am on "/login"
        When I follow "link.do_you_have_problem_login_in"
        Then I should see "resetting.request.username"
        When I fill in "username" with "futurecat"
        And I press "resetting.request.submit"
        Then I should see "resetting.check_email"
        When "futurecat" go to the password reset page
        And I fill in "fos_user_resetting_form_plainPassword_first" with "foobar"
        And I fill in "fos_user_resetting_form_plainPassword_second" with "foobar"
        And I press "resetting.reset.submit"
        Then I should see "link.acquisition_free_downloads"
        Then I should see "link.mailings"
        Then I should see "link.podcast_channels"
        Then I should see "link.promos"

