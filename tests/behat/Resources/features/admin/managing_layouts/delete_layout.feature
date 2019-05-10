@admin @managing_layouts @javascript
Feature: Deleting a layout
    In order to delete a page
    As an administrator
    I need to be able to delete a layout

    Background:
        Given there is a layout called "Example layout"
        And I am logged in as an administrator

    Scenario: Deleting a layout
        When I delete a layout called "Example layout"
        And I confirm the action
        Then there should be no error
        And a layout called "Example layout" should not exist

    Scenario: Deleting a layout and cancelling
        When I delete a layout called "Example layout"
        And I cancel the action
        Then there should be no error
        And a layout called "Example layout" should exist
