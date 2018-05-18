@admin @managing_shared_layouts @javascript
Feature: Creating a shared layout
    In order to create a set of pages
    As an administrator
    I need to be able to create a shared layout

    Background:
        Given I am logged in as an administrator

    Scenario: Creating a shared layout
        When I create a new shared layout
        Then interface for creating a new shared layout should open
