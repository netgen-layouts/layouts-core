@admin @managing_shared_layouts @javascript
Feature: Deleting a shared layout
    In order to delete a set of pages
    As an administrator
    I need to be able to delete a shared layout

    Background:
        Given there is a shared layout called "Example layout"
        And I am logged in as an administrator

    Scenario: Deleting a shared layout
        When I delete a shared layout called "Example layout"
        And I confirm the action
        Then there should be no error
        And a shared layout called "Example layout" should not exist

    Scenario: Deleting a shared layout and cancelling
        When I delete a shared layout called "Example layout"
        And I cancel the action
        Then there should be no error
        And a shared layout called "Example layout" should exist
