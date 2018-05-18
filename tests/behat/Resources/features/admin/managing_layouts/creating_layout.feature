@admin @managing_layouts @javascript
Feature: Creating a layout
    In order to create a page
    As an administrator
    I need to be able to create a layout

    Background:
        Given I am logged in as an administrator

    Scenario: Creating a layout
        When I create a new layout
        Then interface for creating a new layout should open
