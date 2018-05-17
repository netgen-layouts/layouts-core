@admin @managing_shared_layouts @javascript
Feature: Editing a shared layout
    In order to edit a set of pages
    As an administrator
    I need to be able to edit a shared layout

    Background:
        Given there is a shared layout called "Example layout"
        And I am logged in as an administrator

    Scenario: Editing a shared layout
        When I edit a shared layout called "Example layout"
        Then edit interface for shared layout called "Example layout" should open

    Scenario: Editing a shared layout by clicking on a name
        When I click on a shared layout called "Example layout"
        Then edit interface for shared layout called "Example layout" should open
