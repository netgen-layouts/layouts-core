@admin @managing_shared_layouts @javascript
Feature: Duplicating a shared layout
    In order to create a slightly different variant of a set of pages
    As an administrator
    I need to be able to duplicate a shared layout

    Background:
        Given there is a shared layout called "Example layout"
        And I am logged in as an administrator

    Scenario: Duplicating a shared layout
        When I duplicate a shared layout called "Example layout"
        And I set the shared layout name to "Copy of example layout"
        And I confirm the action
        Then there should be no error
        And a shared layout called "Copy of example layout" should exist
        And a shared layout called "Example layout" should exist

    Scenario: Duplicating a shared layout and cancelling
        When I duplicate a shared layout called "Example layout"
        And I cancel the action
        Then there should be no error
        And a shared layout called "Example layout" should exist
        And a shared layout called "Copy of example layout" should not exist

    Scenario: Duplicating a shared layout with existing name
        Given there is a shared layout called "Existing layout"
        When I duplicate a shared layout called "Example layout"
        And I set the shared layout name to "Existing layout"
        And I confirm the action
        Then I should get an error saying "Layout with provided name already exists."
