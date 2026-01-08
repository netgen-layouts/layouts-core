@admin @managing_layouts @javascript
Feature: Editing a layout
    In order to edit a page
    As an administrator
    I need to be able to edit a layout

    Background:
        Given there is a layout called "Example layout"
        And I am logged in as an administrator

    Scenario: Editing a layout
        When I edit a layout called "Example layout"
        Then edit interface for layout called "Example layout" should open

    Scenario: Editing a layout by clicking on a name
        When I click on a layout called "Example layout"
        Then edit interface for layout called "Example layout" should open
